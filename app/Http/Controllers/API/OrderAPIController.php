<?php
/**
 * File name: OrderAPIController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers\API;


use App\Criteria\Orders\OrdersOfStatusesCriteria;
use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Events\OrderChangedEvent;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Notifications\AssignedOrder;
use App\Notifications\NewOrder;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CartRepository;
use App\Repositories\FoodOrderRepository;
use App\Repositories\CustomOrderRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use Stripe\Token;
use Carbon\Carbon;
use App\Models\CustomOrder;
use App\Models\OrderHistory;
use App\Models\Restaurant;
use App\Models\DeliveryAddress;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use File;
use App\Models\Coupon;
use App\Models\FoodOrder;
use App\Models\Stock;

/**
 * Class OrderController
 * @package App\Http\Controllers\API
 */
class OrderAPIController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;
    /** @var  FoodOrderRepository */
    private $foodOrderRepository;
    /** @var  CartRepository */
    private $cartRepository;
    /** @var  UserRepository */
    private $userRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;

    /**
     * OrderAPIController constructor.
     * @param OrderRepository $orderRepo
     * @param FoodOrderRepository $foodOrderRepository
     * @param CartRepository $cartRepo
     * @param PaymentRepository $paymentRepo
     * @param NotificationRepository $notificationRepo
     * @param UserRepository $userRepository
     */
    public function __construct(OrderRepository $orderRepo, FoodOrderRepository $foodOrderRepository, CartRepository $cartRepo, PaymentRepository $paymentRepo, NotificationRepository $notificationRepo, UserRepository $userRepository)
    {
        $this->orderRepository = $orderRepo;
        $this->foodOrderRepository = $foodOrderRepository;
        $this->cartRepository = $cartRepo;
        $this->userRepository = $userRepository;
        $this->paymentRepository = $paymentRepo;
        $this->notificationRepository = $notificationRepo;
    }

    /**
     * Display a listing of the Order.
     * GET|HEAD /orders
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->orderRepository->pushCriteria(new RequestCriteria($request));
            $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $orders = $this->orderRepository->with('customOrders')->all();

        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }

    // public function fetchsearch(Request $request)
    // {
    //     try {
    //         $this->orderRepository->pushCriteria(new RequestCriteria($request));
    //         $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
    //         $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
    //         $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
    //     } catch (RepositoryException $e) {
    //         return $this->sendError($e->getMessage());
    //     }
    //     $orders = $this->orderRepository->with('customOrders')->all();

    //     return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    // }


    public function fetchdrivers(Request $request)
    {
        try {
            $this->orderRepository->pushCriteria(new OrdersOfStatusesCriteria($request));
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $orders = $this->orderRepository->with('customOrders')->with('orderStatus')->with('foodOrders')->all();

        return $this->sendResponse($orders->toArray(), 'Orders retrieved successfully');
    }

    /**
     * Display the specified Order.
     * GET|HEAD /orders/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Order $order */
        if (!empty($this->orderRepository)) {
            try {
                $this->orderRepository->pushCriteria(new RequestCriteria($request));
                $this->orderRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $order = $this->orderRepository->with('customOrders')->findWithoutFail($id);
            if($order->driver_id){
                $driver = 
                // User::join('drivers','users.id','drivers.user_id')
                User::where('id',$order->driver_id)->first();
                // User::where('users.id',$order->driver_id)
                // ->join('drivers','users.id','drivers.user_id')
                // ->select('users.*','drivers.delivery_fee')
                // ->first();
                $order->driver = $driver;
            }
        }

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        return $this->sendResponse($order->toArray(), 'Order retrieved successfully');


    }

    /**
     * Store a newly created Order in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $payment = $request->only('payment');
        if (isset($payment['payment']) && $payment['payment']['method']) {
            if ($payment['payment']['method'] == "Credit Card (Stripe Gateway)") {
                return $this->stripPayment($request);
            } else {
                return $this->cashPayment($request);

            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function stripPayment(Request $request)
    {
        $input = $request->all();
        $amount = 0;
        try {
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }
            $stripeToken = Token::create(array(
                "card" => array(
                    "number" => $input['stripe_number'],
                    "exp_month" => $input['stripe_exp_month'],
                    "exp_year" => $input['stripe_exp_year'],
                    "cvc" => $input['stripe_cvc'],
                    "name" => $user->name,
                )
            ));
            if ($stripeToken->created > 0) {
                if (empty($input['delivery_address_id'])) {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'hint')
                    );
                } else {
                    $order = $this->orderRepository->create(
                        $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
                    );
                }
                foreach ($input['foods'] as $foodOrder) {
                    $foodOrder['order_id'] = $order->id;
                    $amount += $foodOrder['price'] * $foodOrder['quantity'];
                    $this->foodOrderRepository->create($foodOrder);
                }
                // if(isset($input['customfoods'])){
                //     if(count($input['customfoods'])>0){
                //         foreach ($input['customfoods'] as $CustomOrder) {
                //             $CustomOrder['order_id'] = $order->id;
                //             $this->CustomOrderRepository->create($CustomOrder);
                //         }
                //     }
                // }
                $amount += $order->delivery_fee;
                $amountWithTax = $amount + ($amount * $order->tax / 100);
                $charge = $user->charge((int)($amountWithTax * 100), ['source' => $stripeToken]);
                $payment = $this->paymentRepository->create([
                    "user_id" => $input['user_id'],
                    "description" => trans("lang.payment_order_done"),
                    "price" => $amountWithTax,
                    "status" => $charge->status, // $charge->status
                    "method" => $input['payment']['method'],
                ]);
                $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

                $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);

                Notification::send($order->foodOrders[0]->food->restaurant->users, new NewOrder($order));
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    private function cashPayment(Request $request)
    {
        \DB::beginTransaction();
        $input = $request->all();
        $amount = 0;
        try {
            $order = $this->orderRepository->create(
                $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
            );
            $codeQR = generateCode($order->id);
            if($codeQR){
                $order->update(['qrcode'=>$codeQR]);
                QrCode::size(300)->generate($order->qrcode,'../storage/app/public/codeQR/'.$order->qrcode.'.svg');
            }
            if($input['latitude'] && $input['langitude'] && $input['adress']){
                $adress_delivery = new DeliveryAddress();
                $adress_delivery->address  =  $input['adress'];
                $adress_delivery->latitude  =  $input['latitude'];
                $adress_delivery->longitude  =  $input['langitude'];
                $adress_delivery->is_default  =  0;
                $adress_delivery->user_id  = $input['user_id'];
                $adress_delivery->description  = $input['adress'];
                $adress_delivery->save();

                $order->update(['delivery_address_id' =>$adress_delivery->id]);
                
            }
            // $order = $this->orderRepository->create(
            //     $request->only('user_id', 'order_status_id', 'tax', 'delivery_address_id', 'delivery_fee', 'hint')
            // );


            foreach ($input['foods'] as $foodOrder) {
                $foodOrder['order_id'] = $order->id;
                $amount += $foodOrder['price'] * $foodOrder['quantity'];
                $this->foodOrderRepository->create($foodOrder);
            }
            
            if(isset($input['customfoods'])){
                if(count($input['customfoods'])>0){
                    foreach ($input['customfoods'] as $CustomOrder) {
                        $CustomOrder['order_id'] = $order->id;
                        CustomOrder::create([
                            'order_id' => $order->id,
                            'name' => $CustomOrder['name'],
                            'quantite' => $CustomOrder['quantity'],
                            'description' => $CustomOrder['description']
                        ]);
                        // $this->CustomOrderRepository->create($CustomOrder);
                    }
                }
            }

                /**  Calcul des frais de livraison */
            $fees = getDeliveryFees($order->id);
            $order->update(['delivery_fee'=>$fees]);


            $amount += $order->delivery_fee;
            if($request->coupon){
                $coupon_tax=0;
                $coupon = Coupon::where('code',$request->coupon)->where('enabled','1')->where('expires_at','>',Carbon::now())->first();
                // if($coupon){
                //     if($coupon->discount_type=='percent'){
                //         $coupon_tax = 1-$coupon->discount/100
                //     }
                //     if($coupon->discount_type=='fixed'){
                //         $coupon_tax =-$discount->total;
                //             
                //     }

                    //  $order->update(['coupon_id'=>$coupon->id]);
                // }


            }
            // $amountWithTax = $amount + ($amount * $order->tax / 100);
            $payment = $this->paymentRepository->create([
                "user_id" => $input['user_id'],
                "description" => trans("lang.payment_order_waiting"),
                "price" => $amount,
                "status" => 'Waiting for Client',
                "method" => $input['payment']['method'],
            ]);

            $this->orderRepository->update(['payment_id' => $payment->id], $order->id);

            if($input['latitude'] && $input['langitude']){
                $restau = GetRestaurantOrder($input['latitude'], $input['langitude']);
                $is_open=1;
                if($restau){
                    $this->orderRepository->update(['restaurant_id' => $restau['id']], $order->id);
                        if($restau['start_date'] <=  Carbon::now()->format('H:i:s') && $restau['end_date'] >=  Carbon::now()->format('H:i:s')){
                            $is_open=1;
                        }else{
                            $is_open=0;
                        }
                }
            }
            $this->cartRepository->deleteWhere(['user_id' => $order->user_id]);
            
            $restau = Order::join('restaurants','orders.restaurant_id','restaurants.id')
            ->select('orders.*','restaurants.start_date','restaurants.end_date')
            ->where('orders.id',$order->id)->first();
            $restau->is_open=$is_open;

            // Notification::send($order->restaurant->users, new NewOrder($order));
            // Notification::send($order->restaurant->users, new NewOrder($order));

            \DB::commit();
            return $this->sendResponse($restau->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
            // $order= $order->with('restaurant');
            
            
            

            // get all users of a restaurant for notifs

            


        } catch (ValidatorException $e) {
            \DB::rollback();
            return $this->sendError($e->getMessage());
        }
        
    }


    public function editDeliveryTime(Request $request,$id){
        $input = $request->all();
        try{
            $user = $this->userRepository->findWithoutFail($input['user_id']);
            if (empty($user)) {
                return $this->sendError('User not found');
            }

            $Order = $this->orderRepository->findWithoutFail($id);
            if (empty($Order)) {
                return $this->sendError('Order not found');
            }
            if($input['option']){
                $option = $input['option'];
                $time='';
                if($option == 1){
                    $restau = Restaurant::where('id',$Order->restaurant_id)->first();
                    if($restau){
                        $time = Carbon::tomorrow()->format('Y-m-d').' '.$restau->start_date;
                       
                    }
                }else if($option == 2){
                    $time=$input['time'];
                }
            }
            AddOrderHistory($Order->id,$time,$Order->order_status_id,'Date de livraison choisi par le client');
            return $this->sendResponse($Order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
        }catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $oldOrder = $this->orderRepository->findWithoutFail($id);
        if (empty($oldOrder)) {
            return $this->sendError('Order not found');
        }
        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();

        try {
            $order = $this->orderRepository->update($input, $id);
            if (isset($input['order_status_id']) && $input['order_status_id'] == 5 && !empty($order)) {
                AddOrderHistory($order->id,null,$input['order_status_id'],'Changer statut');
                $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);
                // Edit stock product
                $foods = FoodOrder::where('order_id',$order->id)->get();
                if(count($foods)>0){
                    foreach($foods as $food){
                        Stock::where('food_id',$food->food_id)->where('restaurant_id',$order->restaurant_id)
                        ->decrement('initial_qty',$food->quantity);
                    }
                }
            }
            event(new OrderChangedEvent($oldStatus, $order));

            if (setting('enable_notifications', false)) {
                if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                    Notification::send($order->restaurant->users, new StatusChangedOrder($order));
                }

                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));
    }


    public function editStatus(Request $request)
    {
        $user = auth()->user();
        if($request->qrcode && $user){
            $order = Order::where('id',$request->id)->where('qrcode',$request->qrcode)->where('driver_id',$user->id)->where('order_status_id','!=',5)->first();
            if (empty($order)) {
                return $this->sendError('Order not found');
            }
            try {
                    $order->update(['order_status_id'=>5,'onway'=>0]);
                    if($request->paid == 1){
                      $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);  
                        // Edit stock product
                        $foods = FoodOrder::where('order_id',$order->id)->get();
                        if(count($foods)>0){
                            foreach($foods as $food){
                                Stock::where('food_id',$food->food_id)->where('restaurant_id',$order->restaurant_id)
                                ->decrement('initial_qty',$food->quantity);
                            }
                        }
                    }else if($request->paid == 0){
                        $this->paymentRepository->update(['status' => 'Non Paid'], $order['payment_id']); 
                    }
                    // Driver::where('user_id',$user->id)->update([''])

                    if(setting('enable_notifications', false)) {
                            Notification::send([$order->user], new StatusChangedOrder($order));
                            Notification::send($order->restaurant->users, new StatusChangedOrder($order));

                        if ($order['driver_id']) {
                            $driver = $this->userRepository->findWithoutFail($user->id);
                            if (!empty($driver)) {
                                Notification::send([$driver], new StatusChangedOrder($order));
                            }
                        }
                    }

            } catch (ValidatorException $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));    
        }
        
    }
    
    
    public function cancelOrder(Request $request)
    {
        $user = auth()->user();
        if($request->order_id && $user){
            $order = Order::where('id',$request->order_id)->where('user_id',$user->id)->first();
            if (empty($order) || $order->order_status_id == 5) {
                return $this->sendError('Order not found');
            }
            try {
                    $order->update(['order_status_id'=>6,'onway'=>0,'active'=>0]);
                    

                    if(setting('enable_notifications', false)) {
                            Notification::send([$order->user], new StatusChangedOrder($order));
                            Notification::send($order->restaurant->users, new StatusChangedOrder($order));

                    }

            } catch (ValidatorException $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendResponse($order->toArray(), __('lang.saved_successfully', ['operator' => __('lang.order')]));    
        }
        
    }

    public function feesOrder($id){
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order)) {
            return $this->sendError('Order not found');
        }

        $fees =  getDeliveryFees($order->id);
        return $this->sendResponse($fees, 'Order retrieved successfully');

    }

    public function countfeesOrder(){
        $settings = DB::table('app_settings')->where('key','average_price')->first();
        $fees_more = DB::table('app_settings')->where('key','more_price_fees')->first();
        $fees_less = DB::table('app_settings')->where('key','less_price_fees')->first();

        if (empty($settings) || empty($fees_more) || empty($fees_less)) {
            return $this->sendError('not found');
        }

        $fees  = new \stdClass();
        $fees->basic_price =(float)$settings->value;
        $fees->more_price =(float)$fees_more->value;
        $fees->less_price =(float)$fees_less->value;
        return $this->sendResponse($fees, 'Retrieved successfully');

    }

    public function qrcodeOrder($id){
        $order = $this->orderRepository->findWithoutFail($id);

        if (empty($order) || !$order->qrcode) {
            return $this->sendError('Order not found');
        }

        return QrCode::size(300)->generate($order->qrcode);
        
        // return $this->sendResponse($fees, 'Order retrieved successfully');
    }

    public function editOrderWay(Request $request,$id){
        $user = auth()->user();
        if($user){
            $order = $this->orderRepository->findWithoutFail($id);
            if (empty($order) && !$order->driver_id){
                return $this->sendError('Order not found');
            }
            if($request->onway == 1){
                if($this->orderRepository->where('driver_id',$user->id)->where('onway',1)->exists()){
                    return $this->sendError('can not edit');
                }else{
                    $order->update(['onway'=>$request->onway]);
                    return $this->sendResponse($order->toArray(), 'Retrieved successfully');        
                }    
            }else if($request->onway == 0){
                $order->update(['onway'=>$request->onway]);
                return $this->sendResponse($order->toArray(), 'Retrieved successfully'); 
            }
        }else{
            return $this->sendError('User not found');
        }
    }

    public function destroy($id)
    {
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
            $order = $this->orderRepository->findWithoutFail($id);

            // return $order->order_status_id;exit();
            if (empty($order) || $order->order_status_id !=1) {
                return $this->sendError('Order not found');
            }

            $this->orderRepository->delete($id);

            return $this->sendResponse('success','Removed successfully');
    }

}
