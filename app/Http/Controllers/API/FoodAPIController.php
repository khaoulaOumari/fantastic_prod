<?php
/**
 * File name: FoodAPIController.php
 * Last modified: 2020.05.04 at 09:04:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;


use App\Criteria\Foods\NearCriteria;
use App\Criteria\Foods\FoodsOfCategoriesCriteria;
use App\Criteria\Foods\FoodsOfCuisinesCriteria;
use App\Criteria\Foods\TrendingWeekCriteria;
use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Repositories\CustomFieldRepository;
use App\Repositories\FoodRepository;
use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\Order;
use App\Models\StockHistory;
use Carbon\Carbon;
use App\Models\OrderHistory;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

/**
 * Class FoodController
 * @package App\Http\Controllers\API
 */
class FoodAPIController extends Controller
{
    /** @var  FoodRepository */
    private $foodRepository;
    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;
    /**
     * @var UploadRepository
     */
    private $uploadRepository;


    public function __construct(FoodRepository $foodRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->foodRepository = $foodRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Food.
     * GET|HEAD /foods
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            // $this->foodRepository->pushCriteria(new RequestCriteria($request));
            $this->foodRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->foodRepository->pushCriteria(new FoodsOfCuisinesCriteria($request));
            if ($request->get('trending', null) == 'week') {
                $this->foodRepository->pushCriteria(new TrendingWeekCriteria($request));
            } else {
                $this->foodRepository->pushCriteria(new NearCriteria($request));
            }

//            $this->foodRepository->orderBy('closed');
//            $this->foodRepository->orderBy('area');
            $foods = $this->foodRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($foods->toArray(), 'Foods retrieved successfully');
    }

    // public function fetchFoods(Request $request){
    //     try{
    //         $search_tearm = $request->search;
    //         $search_category = $request->category;
    //         $search_promo = $request->promo;
    //         $perPage = $request->limit;
            
    //         $foods = Food::join('categories','foods.category_id','categories.id')
    //         ->select('foods.id','foods.name','foods.price','foods.discount_price','foods.description',
    //         'categories.name as category_name','categories.description as category_desc');

    //         if(isset($request->search)){
    //             $foods->where(function($q) use ($search_tearm){
    //                 $q->where('foods.name', 'LIKE', '%' . $search_tearm . '%')
    //                 ->orWhere('foods.price', 'LIKE', '%' . $search_tearm . '%')
    //                 ->orWhere('foods.description', 'LIKE', '%' . $search_tearm . '%')
    //                 ->orWhere('foods.discount_price', 'LIKE', '%' . $search_tearm . '%')
    //                 ->orWhere('categories.name', 'LIKE', '%' . $search_tearm . '%')
    //                 ->orWhere('categories.description', 'LIKE', '%' . $search_tearm . '%');
    //             });
    //         }
    //         if(isset($request->category)){
    //             $foods->where('foods.category_id', $search_category);
    //         }
    //         if(isset($request->promo)){
    //             $foods->where( 'foods.discount_price','!=',null)->where( 'foods.discount_price','!=','0');
    //         }

    //         $foods = $foods->limit($perPage)->get();


    //     }catch (RepositoryException $e) {
    //         return $this->sendError($e->getMessage());
    //     }

    //     return $this->sendResponse($foods->toArray(), 'Foods retrieved successfully');
    // }

    public function fetchFoods(Request $request){
        try{


        $input = $request->all();
        $search = $request->searchTerm;
        $perPage =  ($request->limit) ? $request->limit : 10;
        $data = [];

        if(!$request->searchTerm){
            $data = Food::get();
            // ->paginate($perPage)
        } else{
            $data = Food::search($request->searchTerm)->get();
        }

        return $this->sendResponse($data->toArray(), 'Foods retrieved successfully'); 

        }catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
    }
    public function FetchPromoFood(Request $request){
        $this->foodRepository->pushCriteria(new RequestCriteria($request));
        $this->foodRepository->pushCriteria(new LimitOffsetCriteria($request));
        // $this->foodRepository->pushCriteria(new FoodsOfCuisinesCriteria($request));
        $foods = $this->foodRepository->where('discount_price','!=',null)->get();
        
        return $this->sendResponse($foods->toArray(), 'Foods retrieved successfully');
    }

    /**
     * Display a listing of the Food.
     * GET|HEAD /foods/categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function categories(Request $request)
    {
        try{
            $this->foodRepository->pushCriteria(new RequestCriteria($request));
            $this->foodRepository->pushCriteria(new LimitOffsetCriteria($request));
            // $this->foodRepository->pushCriteria(new FoodsOfCuisinesCriteria($request));
            $this->foodRepository->pushCriteria(new FoodsOfCategoriesCriteria($request));

            $foods = $this->foodRepository->all();

        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($foods->toArray(), 'Foods retrieved successfully');
    }

    /**
     * Display the specified Food.
     * GET|HEAD /foods/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        /** @var Food $food */
        if (!empty($this->foodRepository)) {
            try{
                $this->foodRepository->pushCriteria(new RequestCriteria($request));
                $this->foodRepository->pushCriteria(new LimitOffsetCriteria($request));
            } catch (RepositoryException $e) {
                return $this->sendError($e->getMessage());
            }
            $food = $this->foodRepository->findWithoutFail($id);
        }

        if (empty($food)) {
            return $this->sendError('Food not found');
        }

        return $this->sendResponse($food->toArray(), 'Food retrieved successfully');
    }

    /**
     * Store a newly created Food in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->foodRepository->model());
        try {
            $food = $this->foodRepository->create($input);
            $food->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($food, 'image');
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($food->toArray(), __('lang.saved_successfully', ['operator' => __('lang.food')]));
    }

    /**
     * Update the specified Food in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $food = $this->foodRepository->findWithoutFail($id);

        if (empty($food)) {
            return $this->sendError('Food not found');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->foodRepository->model());
        try {
            $food = $this->foodRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($food, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $food->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($food->toArray(), __('lang.updated_successfully', ['operator' => __('lang.food')]));

    }

    /**
     * Remove the specified Food from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $food = $this->foodRepository->findWithoutFail($id);

        if (empty($food)) {
            return $this->sendError('Food not found');
        }

        $food = $this->foodRepository->delete($id);

        return $this->sendResponse($food, __('lang.deleted_successfully', ['operator' => __('lang.food')]));

    }

    

    public function getFoods(Request $request){


        $order = Order::where('id',196)->with('restaurant')->first();
        return $order;
        return $order->restaurant->users;
        $data = Order::join('restaurants', 'orders.restaurant_id', 'restaurants.id')
        ->join("user_restaurants", "restaurants.id", "user_restaurants.restaurant_id")
        ->where('user_restaurants.user_id', 2)
        ->groupBy('orders.id')
        ->select('orders.*')->get();
        return $data; exit();
        $input = $request->all();
        $search = $request->searchTerm;
        $perPage = $request->limit;
        $data = [];

        if(!$request->searchTerm){
            $data = Food::get();
        } else{
            $data = Food::search($request->searchTerm)->get();
        }

        return $this->sendResponse($data->toArray(), 'Foods retrieved successfully'); 
        
        return Food::search('p')->get();exit();
        $ajaxDriversData = Order::join('users','orders.driver_id','users.id')
        ->where('orders.order_status_id',5)
        ->select('users.name',DB::raw("COUNT(orders.id) as count"))
        ->groupBy('users.name')
        ->get();

        $label= array_column($ajaxDriversData->toArray(),'name');
        $total= array_column($ajaxDriversData->toArray(),'count');
        return response()->json(['data'=>$total,'labels'=>$label]);
        // return $usermcount;
        for($i = 1; $i <= 12; $i++){
            if(!empty($usermcount[$i])){
                $userArr[$i] = $usermcount[$i]; 
                array_push($array,$usermcount[$i]);
            }else{
                $userArr[$i] = 0;    
                array_push($array,0);

            }
            
        }
        return $array;
        // $orders = Order::select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month_name"))
        // ->whereYear('created_at', date('Y'))
        // ->groupBy(DB::raw("Month(created_at)"))
        // ->get();
        return response()->json(['labels'=>$userArr]);
        // return distance(30.4030984, -9.5284776, 30.4030984, -9.5284776);
        $array = GetRestaurantOrder(30.4030984, -9.5284776);
        return $array;
        // if($array){
        //     if($array['start_date'] <=  Carbon::now()->format('H:i:s') && $array['end_date'] >=  Carbon::now()->format('H:i:s')){
        //         return 'ok';
        //     }else{
        //         return 'no';
        //     }
        // }

        return getDeliveryFees(1);


        return OrderHistory::where('order_id',1)->with('orderStatus')->get();
        return StockHistory::with('stock')->first();

        return Food::join('food_restaurants','foods.id','food_restaurants.food_id')
        ->join('restaurants','food_restaurants.restaurant_id','restaurants.id')
        ->select('restaurants.id','restaurants.name')
        ->where('foods.id',26)
        ->get()->pluck('name', 'id');
        
        // $Object = array_reduce($array,function($A,$B){
        //     return $A['distance'] < $B['distance'] ? $A : $B;
        // }, array_shift($array));
        // return $array;
        // $order = Order::first();
        // return $order->restaurant->users;

        // $Object = array_reduce($data,function($A,$B){
        //     return $A->distance < $B->distance ? $A : $B;
        // })
        // return Food::with("category")
        // ->join('food_restaurants', 'foods.id', '=', 'food_restaurants.food_id')
        // ->join('restaurants', 'food_restaurants.restaurant_id', '=', 'restaurants.id')
        // ->join("user_restaurants", "user_restaurants.restaurant_id", "=", "food_restaurants.restaurant_id")
        // ->where('user_restaurants.user_id', auth()->id())
        // ->groupBy('foods.id')
        // ->select('foods.*')->orderBy('foods.updated_at', 'desc')->get();
    }

}
