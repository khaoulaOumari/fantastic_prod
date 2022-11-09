<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RestaurantRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Food;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\SubClaimOrder;
use App\Models\SupCategory;
use App\Models\Category;


class DashboardController extends Controller
{

    /** @var  OrderRepository */
    private $orderRepository;


    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var  RestaurantRepository */
    private $restaurantRepository;
    /** @var  PaymentRepository */
    private $paymentRepository;

    public function __construct(OrderRepository $orderRepo, UserRepository $userRepo, PaymentRepository $paymentRepo, RestaurantRepository $restaurantRepo)
    {
        parent::__construct();
        $this->orderRepository = $orderRepo;
        $this->userRepository = $userRepo;
        $this->restaurantRepository = $restaurantRepo;
        $this->paymentRepository = $paymentRepo;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ordersCount = $this->orderRepository->count();
        $membersCount = $this->userRepository->count();
        $restaurantsCount = $this->restaurantRepository->count();
        // $restaurants = $this->restaurantRepository->limit(5)->get();
        $restaurants = $this->restaurantRepository->join('orders','restaurants.id','orders.restaurant_id')
        ->join('food_orders', 'orders.id', '=', 'food_orders.order_id')
        ->select('restaurants.*',DB::raw('count(orders.id) as count'))
        ->groupBy('restaurants.id')
        ->take(10)->get();

        $drivers = Driver::count();
        $earning = $this->paymentRepository->all()->sum('price');
        $ajaxEarningUrl = route('payments.byMonth',['api_token'=>auth()->user()->api_token]);

        // $ProductsWeek
        $products= Food::join('food_orders', 'foods.id', '=', 'food_orders.food_id')
        // ->join('food_restaurants', 'foods.id', '=', 'food_restaurants.food_id')
        // ->join('restaurants', 'food_restaurants.restaurant_id', '=', 'restaurants.id')
        // ->whereBetween('food_orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        // ->where('restaurants.active','1')
        ->groupBy('foods.id')
        ->orderBy('food_count', 'desc')
        ->select('foods.*', DB::raw('count(foods.id) as food_count'))
        ->take(10)->get();

        //claims
        $claims = SubClaimOrder::count();
        $categories = SupCategory::count();
        $sub_categories = Category::count();



        $profit_data = Food::join('food_orders','foods.id','food_orders.food_id')->join('orders','food_orders.order_id','orders.id')
        ->select('food_orders.order_id',DB::raw("(SUM((food_orders.price - foods.prix_achat)*food_orders.quantity))+(orders.delivery_fee - orders.driver_fee) as profit"))
        ->where('orders.order_status_id',5)
        ->groupBy('food_orders.order_id')
        ->get()->pluck('profit')->toArray();
        $profit =  array_sum($profit_data);


       
        
        return view('dashboard.index')
            ->with("ajaxEarningUrl", $ajaxEarningUrl)
            ->with("ordersCount", $ordersCount)
            ->with("restaurantsCount", $restaurantsCount)
            ->with("restaurants", $restaurants)
            ->with("membersCount", $membersCount)
            ->with("earning", $earning)
            ->with("drivers", $drivers)
            ->with("weekProducts", $products)
            ->with("claims", $claims)
            ->with("categories", $categories)
            ->with("sub_categories", $sub_categories)
            ->with("profit", $profit);
    }


    public function ajaxCatgeories(){
        $chart_categories = Category::leftjoin('foods','categories.id','foods.category_id')
        ->join('food_orders','foods.id','food_orders.food_id')
        ->select(DB::raw("COUNT(categories.id) as count"),'categories.name')
        ->groupBy('categories.name')
        ->get();
        
        $label= array_column($chart_categories->toArray(),'name');
        $total= array_column($chart_categories->toArray(),'count');
        return response()->json(['data'=>$total,'labels'=>$label]);
    }

    public function ajaxOrders(){
        $orders =  Order::select('id', 'created_at')
        ->get()
        ->groupBy(function($date) {
            //return Carbon::parse($date->created_at)->format('Y'); // grouping by years
            return Carbon::parse($date->created_at)->format('m'); // grouping by months
        });
        
        $ordercount = [];
        // $userArr = [];
        $array=[];
        
        foreach ($orders as $key => $value) {
            $ordercount[(int)$key] = count($value);

        }
        for($i = 1; $i <= 12; $i++){
            if(!empty($ordercount[$i])){
                // $userArr[$i] = $ordercount[$i]; 
                array_push($array,$ordercount[$i]);
            }else{
                // $userArr[$i] = 0;    
                array_push($array,0);

            }
            
        }
        return response()->json(['data'=>$array]);
    }

    public function ajaxDrivers(){
        $ajaxDriversData = Order::join('users','orders.driver_id','users.id')
        ->where('orders.order_status_id',5)
        ->select('users.name',DB::raw("COUNT(orders.id) as count"))
        ->groupBy('users.name')
        ->get();

        $label= array_column($ajaxDriversData->toArray(),'name');
        $total= array_column($ajaxDriversData->toArray(),'count');
        return response()->json(['data'=>$total,'labels'=>$label]);
    }

    public function ordersMap()
    {

        $arr=[];
        $locations = Order::select('delivery_addresses.longitude','delivery_addresses.latitude','delivery_addresses.address','orders.order_status_id')
        ->join('delivery_addresses','orders.delivery_address_id','delivery_addresses.id')
        // ->where('orders.order_status_id',5)
        ->where('orders.active',1)
        ->get();
        foreach ($locations as $row) {
            $a=[];
            $a = [$row->address, (float)$row->latitude,(float)$row->longitude,$row->order_status_id];
            array_push($arr, $a);
          }
        
        return response()->json($arr);
    }
}
