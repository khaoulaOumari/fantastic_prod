<?php

namespace App\Http\Controllers;

use App\DataTables\StockDataTable;
use App\Http\Requests\CreateStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Repositories\StockRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RestaurantRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\Food;
use App\Models\StockHistory;

class StockController extends Controller
{
    /** @var  StockRepository */
    private $stockRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

         

    public function __construct(StockRepository $stockRepo, CustomFieldRepository $customFieldRepo, RestaurantRepository $restaurantRepo)
    {
        parent::__construct();
        $this->stockRepository = $stockRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->restaurantRepository = $restaurantRepo;
    }

    /**
     * Display a listing of the Annonce.
     *
     * @param StockRepository $stockDataTable
     * @return Response
     */
    public function index(StockDataTable $stockDataTable)
    {
        return $stockDataTable->render('stocks.index');
    }

    /**
     * Show the form for creating a new Annonce.
     *
     * @return Response
     */
    public function create()
    {

        // $restaurants= Food::join('food_restaurants','foods.id','food_restaurants.food_id')
        // ->join('restaurants','food_restaurants.restaurant_id','restaurants.id')
        // ->select('restaurants.id','restaurants.name')
        // ->where('foods.id',$request->foodId)
        // ->get()->pluck('name', 'id');

        $hasCustomField = in_array($this->stockRepository->model(), setting('custom_field_models', []));
        // $foods = Food::get()->pluck('name', 'id');
        if (auth()->user()->hasRole('admin')) {
            $restaurants = $this->restaurantRepository->pluck('name','id')->toArray();
            $restaurant = $this->restaurantRepository->pluck('id')->toArray();
        } else {
            $restaurants = $this->restaurantRepository->myActiveRestaurants()->pluck('name', 'id');
            $restaurant = $this->restaurantRepository->myActiveRestaurants()->pluck('restaurants.id')->toArray();
        }
        $foods = Food::join('food_restaurants','foods.id','food_restaurants.food_id')
            ->whereIn('food_restaurants.restaurant_id',$restaurant)
            ->select('foods.id','foods.name')->get()->pluck('name', 'id');
            // return $foods;exit();

        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->stockRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('stocks.create')->with("foods",$foods)->with("restaurants",$restaurants)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created Annonce in storage.
     *
     * @param CreateStockRequest $request
     *
     * @return Response
     */
    public function store(CreateStockRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->stockRepository->model());
        try {
            $stock = $this->stockRepository->create(array_merge($input,['user_id' => auth()->user()->id]));
            $stock->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.stock')]));

        return redirect(route('stocks.index'));
    }

    /**
     * Display the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $stock = $this->stockRepository->findWithoutFail($id);

        if (empty($stock)) {
            Flash::error('stock not found');

            return redirect(route('stocks.index'));
        }

        // $stockHistory = StockHistory::with('user')->with(['stock','stock.food'])
        // ->where('stock_id',$id)
        // ->get();

        $stockHistory = StockHistory::with('user')
        ->join('stocks','stock_histories.stock_id','stocks.id')
        ->join('foods','stocks.food_id','foods.id')
        ->join('restaurants','stocks.restaurant_id','restaurants.id')
        ->where('stock_histories.stock_id',$id)
        ->select('stock_histories.*','foods.name as food_name','restaurants.name as restaurant_name')->get();

        return view('stocks.show')->with('stock', $stock)->with('histories', $stockHistory);
    }

    /**
     * Show the form for editing the specified Category.
     *
     * @param int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $stock = $this->stockRepository->findWithoutFail($id);


        if (empty($stock)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.stock')]));

            return redirect(route('stocks.index'));
        }
        // $supcategory = $this->supstockRepository->pluck('name', 'id');
        $customFieldsValues = $stock->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->stockRepository->model());
        $hasCustomField = in_array($this->stockRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('stocks.edit')->with('stock', $stock)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateStockRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateStockRequest $request)
    {
        $stock = $this->stockRepository->findWithoutFail($id);

        if (empty($stock)) {
            Flash::error('stock not found');
            return redirect(route('stocks.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->stockRepository->model());
        try {
            $stock = $this->stockRepository->update($input, $id);

            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $stock->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.stock')]));

        return redirect(route('stocks.index'));
    }

    /**
     * Remove the specified Category from storage.
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $stock = $this->stockRepository->findWithoutFail($id);

        if (empty($stock)) {
            Flash::error('stock not found');

            return redirect(route('stocks.index'));
        }

        $this->stockRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.stock')]));

        return redirect(route('stocks.index'));
    }

    /**
     * Remove Media of Category
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $stock = $this->stockRepository->findWithoutFail($input['id']);
        try {
            if ($stock->hasMedia($input['collection'])) {
                $stock->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function FetchRestauFood(Request $request){
        $restaurants = Food::join('food_restaurants','foods.id','food_restaurants.food_id')
        ->join('restaurants','food_restaurants.restaurant_id','restaurants.id')
        ->select('restaurants.id','restaurants.name')
        ->where('foods.id',$request->foodId)
        ->get()->pluck('name', 'id');

        $html = view('stocks.create')->with(compact('restaurants'))->render();
        return response()->json(['success' => true, 'html' => $html]);

        
        // return response()->json($restaurants);
        // return view('stocks.create')->with("restaurants",$foods);
        
    }

    public function EditStock(Request $request,StockDataTable $stockDataTable){
        $stock = $this->stockRepository->findWithoutFail($request->stockId);

        if (empty($stock)) {
            Flash::error('stock not found');

            return redirect(route('stocks.index'));
        }
        $qty_add = $request->qty-$stock->initial_qty;
        $task='';
        // if($qty_add>0) $task='';
        $stock->update([
            'initial_qty' => $request->qty
        ]);
        $res = addToStock($stock->id,$qty_add,'Modifier quantité initaile du stock');
        if($res =='success'){
            return $stockDataTable->render('stocks.index');
            // return response()->json('success');
        }
    }
}
