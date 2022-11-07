<?php
/**
 * File name: OrderController.php
 * Last modified: 2020.06.11 at 16:10:52
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 */

namespace App\Http\Controllers;

use App\Criteria\Orders\OrdersOfUserCriteria;
use App\Criteria\Users\ClientsCriteria;
use App\Criteria\Users\DriversCriteria;
use App\Criteria\Users\DriversOfRestaurantCriteria;
use App\DataTables\OrderDataTable;
use App\DataTables\FoodOrderDataTable;
use App\Events\OrderChangedEvent;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Notifications\AssignedOrder;
use App\Notifications\StatusChangedOrder;
use App\Repositories\CustomFieldRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\OrderRepository;
use App\Repositories\CustomOrderRepository;
use App\Repositories\FoodRepository;
use App\Repositories\FoodOrderRepository;

use App\Repositories\OrderStatusRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\UserRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\Order;
use App\Models\CustomOrder;
use App\Models\Food;
use App\Models\FoodOrder;
use App\Models\OrderHistory;
use App\Models\OrderStatus;
use App\Models\Stock;
use App\Models\Driver;

class OrderController extends Controller
{
    /** @var  OrderRepository */
    private $orderRepository;
    private $customOrderRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;
    /** @var  NotificationRepository */
    private $notificationRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(OrderRepository $orderRepo,CustomOrderRepository $customOrderRepo, CustomFieldRepository $customFieldRepo, UserRepository $userRepo
        , OrderStatusRepository $orderStatusRepo, NotificationRepository $notificationRepo, PaymentRepository $paymentRepo,FoodRepository $foodRepo,FoodOrderRepository $foodOrderRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->customOrderRepository = $customOrderRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->userRepository = $userRepo;
        $this->orderStatusRepository = $orderStatusRepo;
        $this->notificationRepository = $notificationRepo;
        $this->paymentRepository = $paymentRepo;
        $this->foodRepository = $foodRepo;
        $this->foodOrderRepository = $foodOrderRepository;

    }

    /**
     * Display a listing of the Order.
     *
     * @param OrderDataTable $orderDataTable
     * @return Response
     */
    public function index(OrderDataTable $orderDataTable)
    {
        $status = Food::where('featured',0)->select('id','name','price')->get();
        return $orderDataTable->render('orders.index');
        // ->with("status", $status)
    }

    /**
     * Show the form for creating a new Order.
     *
     * @return Response
     */
    public function create()
    {
        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversCriteria())->pluck('name', 'id');

        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');


        $customorders = [];
        $FoodsOrder=[];
        $customfoods = Food::where('featured',0)->select('id','name','price')->get();

        $foods =  Food::select('id','name','price')->get()->pluck('name', 'id','price');

        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('orders.create')->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus)->with('customorders', $customorders)->with('customfoods', $customfoods)->with("FoodsOrder", $FoodsOrder)->with("foods", $foods);
    }

    /**
     * Store a newly created Order in storage.
     *
     * @param CreateOrderRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        try {
            $order = $this->orderRepository->create($input);
            if($request->latitude && $request->langitude){
                $restau = GetRestaurantOrder($request->latitude, $request->langitude);
                if($restau){
                    $order->update([
                        'restaurant_id' => $restau->id
                    ]);
                }
            }
            $order->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));

        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Display the specified Order.
     *
     * @param int $id
     * @param FoodOrderDataTable $foodOrderDataTable
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */

    public function show(FoodOrderDataTable $foodOrderDataTable, $id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);
        $restaurant = $order->restaurant;
        $customorders = CustomOrder::where('order_id',$id)
        ->leftjoin('foods','custom_orders.food_id','=','foods.id')
        ->select('custom_orders.*','foods.name as food_name','foods.price')
        ->where('active',0)
        ->get();
        // return $order->restaurant->id;exit();
        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

            return redirect(route('orders.index'));
        }
        $subtotal = 0;

        foreach ($order->foodOrders as $foodOrder) {
            foreach ($foodOrder->extras as $extra) {
                $foodOrder->price += $extra->price;
            }
            $subtotal += $foodOrder->price * $foodOrder->quantity;
        }

        $total = $subtotal + $order['delivery_fee'];
        // $taxAmount = $total * $order['tax'] / 100;
        // $total += $taxAmount;
        // $total += $total;

        $foodOrderDataTable->id = $id;
        return $foodOrderDataTable->render('orders.show', ["order" => $order, "total" => $total, "subtotal" => $subtotal,'customorders'=>$customorders,'FoodsOrder'=>$order->foodOrders]);

        // return $foodOrderDataTable->render('orders.show', ["order" => $order, "total" => $total, "subtotal" => $subtotal,"taxAmount" => $taxAmount,'customorders'=>$customorders]);
    }

    /**
     * Show the form for editing the specified Order.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function edit($id)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $order = $this->orderRepository->findWithoutFail($id);
       
        if (empty($order)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

            return redirect(route('orders.index'));
        }

        // $restaurant = $order->foodOrders()->first();
        // return($customorder);exit();
        // $restaurant = isset($restaurant) ? $restaurant->food['restaurant_id'] : 0;
        $customorders = CustomOrder::where('order_id',$id)
        ->leftjoin('foods','custom_orders.food_id','=','foods.id')
        ->select('custom_orders.*','foods.name as food_name','foods.price')
        ->where('active',0)
        ->get();
        // $customfoods = Food::where('featured',0)->select('id','name','price')->get();
        $foods =  Food::select('id','name','price')->get()->pluck('name', 'id','price');

        $FoodsOrder = FoodOrder::select('food_orders.*','foods.name')
        ->where('food_orders.order_id',$order->id)
        ->join('foods','food_orders.food_id','foods.id')->get();

        // $restaurant=Order::join('food_orders', 'orders.id', '=', 'food_orders.order_id')
        // ->join('foods', 'food_orders.food_id', '=', 'foods.id')
        // ->join("food_restaurants", "foods.id", "=", "food_restaurants.food_id")
        // ->select('food_restaurants.restaurant_id')
        // ->where('orders.id',$id)->get()->pluck('restaurant_id');
        $restaurant = $order->restaurant;
        $restaurant = isset($restaurant) ? $restaurant->id : 0;
        
        $user = $this->userRepository->getByCriteria(new ClientsCriteria())->pluck('name', 'id');
        $driver = $this->userRepository->getByCriteria(new DriversOfRestaurantCriteria($restaurant))->pluck('name', 'id');
        $orderStatus = $this->orderStatusRepository->pluck('status', 'id');


        $customFieldsValues = $order->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        $hasCustomField = in_array($this->orderRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }
// ->with('customfoods', $customfoods)
        return view('orders.edit')->with('order', $order)->with('customorders', $customorders)->with("customFields", isset($html) ? $html : false)->with("user", $user)->with("driver", $driver)->with("orderStatus", $orderStatus)->with("FoodsOrder", $FoodsOrder)->with("foods", $foods);
    }

    /**
     * Update the specified Order in storage.
     *
     * @param int $id
     * @param UpdateOrderRequest $request
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function update($id, UpdateOrderRequest $request)
    {
        $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
        $oldOrder = $this->orderRepository->findWithoutFail($id);
        // return $oldOrder->payment;exit();
        if (empty($oldOrder)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));
            return redirect(route('orders.index'));
        }
        $oldStatus = $oldOrder->payment->status;
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderRepository->model());
        try {

            $order = $this->orderRepository->update($input, $id);

            $customOrders =  CustomOrder::join('foods','custom_orders.food_id','foods.id')
            ->where('custom_orders.order_id',$id)->where('custom_orders.food_id','!=',0)
            ->select('custom_orders.*','foods.price')
            ->where('active',0)
            ->get();
            // if(count($customOrders)>0){
            //     foreach($customOrders as $custom){
            //         // FoodOrder::create([
            //         //     'food_id' => $custom->food_id,
            //         //     'order_id' => $id,
            //         //     'quantity' => $custom->quantite,
            //         //     'price' => $custom->price
            //         // ]);
            //         // CustomOrder::where('id',$custom->id)->update(['active'=>1]);
            //     }
            // }

            if (isset($input['driver_id'])) {
                $driver = Driver::where('user_id',$input['driver_id'])->first();
                if (!empty($driver)) {
                    Order::where('id',$id)->update(['driver_fee'=>$driver->delivery_fee]);  
                }
            }

            if (setting('enable_notifications', false)) {
                if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id) {
                    Notification::send([$order->user], new StatusChangedOrder($order));
                }

                
                if (isset($input['driver_id']) && ($input['driver_id'] != $oldOrder['driver_id'])) {
                    $driver = $this->userRepository->findWithoutFail($input['driver_id']);
                    if (!empty($driver)) {
                        Notification::send([$driver], new AssignedOrder($order));
                    }
                }
            }

            if (isset($input['order_status_id']) && $input['order_status_id'] != $oldOrder->order_status_id){
                AddOrderHistory($oldOrder->id,null,$input['order_status_id'],'Changer statut');
            }

            $this->paymentRepository->update([
                "status" => $input['status'],
            ], $order['payment_id']);
            if($input['status'] == 'Paid'){
                $foods = FoodOrder::where('order_id',$order->id)->get();
                if(count($foods)>0){
                    foreach($foods as $food){
                        Stock::where('food_id',$food->food_id)->where('restaurant_id',$order->restaurant_id)
                        ->decrement('initial_qty',$food->quantity);
                    }
                }
            }
            //dd($input['status']);


            // Edit Status
            // if (isset($input['order_status_id']) && $input['order_status_id'] == 5 && !empty($order)) {
            //     $this->paymentRepository->update(['status' => 'Paid'], $order['payment_id']);
            //     // Edit stock product
            // }
           
            
            event(new OrderChangedEvent($oldStatus, $order));

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $order->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.order')]));

        return redirect(route('orders.index'));
    }

    /**
     * Remove the specified Order from storage.
     *
     * @param int $id
     *
     * @return Response
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function destroy($id)
    {
        if (!env('APP_DEMO', false)) {
            $this->orderRepository->pushCriteria(new OrdersOfUserCriteria(auth()->id()));
            $order = $this->orderRepository->findWithoutFail($id);

            if (empty($order)) {
                Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

                return redirect(route('orders.index'));
            }

            $this->orderRepository->delete($id);

            Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.order')]));


        } else {
            Flash::warning('This is only demo app you can\'t change this section ');
        }
        return redirect(route('orders.index'));
    }

    /**
     * Remove Media of Order
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $order = $this->orderRepository->findWithoutFail($input['id']);
        try {
            if ($order->hasMedia($input['collection'])) {
                $order->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }


    function action(Request $request)
    {
            $product = CustomOrder::find($request->id);
            $product->delete();
            return response()->json(['success' => 'success']);
    }

    public function removeOrder(Request $request){
        $product = FoodOrder::find($request->id);
        if($product){
            $order = Order::where('id',$product->order_id)->first();
            if($order && $order->order_status_id==1 && $order->active == 1){
                $product->delete();
                return response()->json(['success']);   
            }else{
                return response()->json(['no access']);   
            }

             
        }else{
            return response()->json(['no product']);   
        }
        
    }
    public function editOrder(Request $request){
        $product = FoodOrder::find($request->foodId);
        if($product && $request->qnty){
            $order = Order::where('id',$product->order_id)->first();
            if($order && $order->order_status_id==1 && $order->active == 1){
                $product->update(['quantity'=>$request->qnty]);
                return response()->json(['success']);   
            }else{
                return response()->json(['no access']);   
            }

             
        }else{
            return response()->json(['no product']);   
        }
        
    }
    public function editStatus(Request $request){
        try {
            $order = Order::find($request->id);
            if($order  && $order->order_status_id!=5 &&  $request->statut && $order->active == 1){
                // && $order->order_status_id!=5
                $status = OrderStatus::where('id',$request->statut)->first();
                if($status){
                    $old_status_id = $order->order_status_id;
                    $order->update(['order_status_id'=>$status->id]);
                    if ( $request->order_status_id != $old_status_id){
                        AddOrderHistory($order->id,null,$order->order_status_id,'Changer statut');
                    }

                    if ($status->id == 5) {
                        $this->paymentRepository->update(['status' => 'Paid'], $order->payment_id);
                        // Edit stock product
                        $foods = FoodOrder::where('order_id',$order->id)->get();
                        if(count($foods)>0){
                            foreach($foods as $food){
                                Stock::where('food_id',$food->food_id)->where('restaurant_id',$order->restaurant_id)
                                ->decrement('initial_qty',$food->quantity);
                            }
                        }
                    }
                    return response()->json(['success']);   
                }else{
                    return response()->json(['no statut']);   
                }
            }else{
                return response()->json(['no order']);   
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function activeOrder(Request $request){
        try {
            $order = Order::find($request->id);
            if($order){
                $order->update(['active'=>!$order->active]);
                return response()->json(['success']);   
            }else{
                return response()->json(['no order']);   
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    function editCustom(Request $request)
    {
        $food=Food::find($request->foodId);
        if($food){
            CustomOrder::find($request->id)
                ->update([
                    'food_id' => $food->id,
                    'name' => $food->name

                ]);

            return response()->json(['success' => 'success']);
        }else{
            return response()->json(['error' => 'error']);
        }
        
    }
    function OrderHistories(Request $request){
        // $order = $this->orderRepository->findWithoutFail($id);
        // if (empty($order)) {
        //     Flash::error(__('lang.not_found', ['operator' => __('lang.order')]));

        //     return redirect(route('orders.index'));
        // }

        $orderHistories = OrderHistory::where('order_id',$request->id)->with('orderStatus')->get();
        $html = "";
        
        $html .='

        <table class="table table-sm table-bordered">
            <tr class="">
            <td>Order ID</td>
            <td>Statut</td>
            <td>Désignation</td>
            <td>Date Livraison</td>
            <td>Date</td>
            </tr>
            ';


        if(count($orderHistories)>0){

            foreach($orderHistories as $row){
                $html .='
                <tr class="tracking-table-body">
                <td class="td-nowrap"><b>'.$row->order_id.'</b></td>
                <td class="td-nowrap"><b>'.$row->orderStatus->status.'</b></td>
                <td class="td-nowrap">'.$row->text.'</td>
                <td class="td-nowrap">'.$row->delivery_time.'</td>
                <td class="td-nowrap">'.$row->created_at.'</td>

                </tr>';}
            
        }
        $html .='
        </table>
        ';
        $response['html'] = $html;

        return response()->json($response);
        // return view('orders.histories')->with('order', $order)->with('orderHistories', $orderHistories);

    }


    public function addNewFood(Request $request){
        $input = $request->all();
        $order = Order::find($request->orderId);
        if (empty($order) || $order->active == 0) {
            return response()->json('no order');
        }
      
        $food=Food::find($request->foodId);
        if($food){
            FoodOrder::create([
                'price' => $food->price,
                'quantity' => $request->qnty,
                'food_id' => $food->id,
                'order_id' => $order->id
            ]);

            return response()->json('success');
        }
        
        
       
        

           



    }
    
}
