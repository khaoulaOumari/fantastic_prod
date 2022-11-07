<?php

namespace App\Http\Controllers;

use App\DataTables\OrderHistoryDataTable;
use App\Http\Requests;
use App\Http\Requests\CreateOrderHistoryRequest;
use App\Http\Requests\UpdateOrderHistoryRequest;
use App\Repositories\OrderHistoryRepository;
use App\Repositories\CustomFieldRepository;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class OrderHistoryController extends Controller
{
    /** @var  OrderHistoryRepository */
    private $orderHistoryRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    

    public function __construct(OrderHistoryRepository $orderHistoryRepo, CustomFieldRepository $customFieldRepo )
    {
        parent::__construct();
        $this->orderHistoryRepository = $orderHistoryRepo;
        $this->customFieldRepository = $customFieldRepo;
        
    }

    /**
     * Display a listing of the OrderStatus.
     *
     * @param OrderHistoryDataTable $orderHistoryDataTable
     * @return Response
     */
    public function index(OrderHistoryDataTable $orderHistoryDataTable)
    {
        return $orderHistoryDataTable->render('order_histories.index');
    }

    /**
     * Show the form for creating a new OrderStatus.
     *
     * @return Response
     */
    public function create()
    {
        
        
        // $hasCustomField = in_array($this->orderHistoryRepository->model(),setting('custom_field_models',[]));
        //     if($hasCustomField){
        //         $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderHistoryRepository->model());
        //         $html = generateCustomField($customFields);
        //     }
        // return view('order_histories.create')->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created OrderStatus in storage.
     *
     * @param CreateOrderStatusRequest $request
     *
     * @return Response
     */
    public function store(CreateOrderStatusRequest $request)
    {
        // $input = $request->all();
        // $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->orderHistoryRepository->model());
        // try {
        //     $orderHistory = $this->orderHistoryRepository->create($input);
        //     $orderHistory->customFieldsValues()->createMany(getCustomFieldsValues($customFields,$request));
            
        // } catch (ValidatorException $e) {
        //     Flash::error($e->getMessage());
        // }

        // Flash::success(__('lang.saved_successfully',['operator' => __('lang.order_status')]));

        // return redirect(route('orderStatuses.index'));
    }

    /**
     * Display the specified OrderStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $orderHistory = $this->orderHistoryRepository->findWithoutFail($id);

        if (empty($orderHistory)) {
            Flash::error('Order Status not found');

            return redirect(route('orderHistories.index'));
        }

        return view('order_histories.show')->with('orderSHistory', $orderHistory);
    }

    /**
     * Show the form for editing the specified OrderStatus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        
    }

    /**
     * Update the specified OrderStatus in storage.
     *
     * @param  int              $id
     * @param UpdateOrderStatusRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateOrderStatusRequest $request)
    {
        
    }

    /**
     * Remove the specified OrderStatus from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $orderHistory = $this->orderHistoryRepository->findWithoutFail($id);

        if (empty($orderHistory)) {
            Flash::error('Order Status not found');

            return redirect(route('orderHistories.index'));
        }

        $this->orderHistoryRepository->delete($id);

        Flash::success(__('lang.deleted_successfully',['operator' => __('lang.order_status')]));

        return redirect(route('orderHistories.index'));
    }

        /**
     * Remove Media of OrderStatus
     * @param Request $request
     */
   
}
