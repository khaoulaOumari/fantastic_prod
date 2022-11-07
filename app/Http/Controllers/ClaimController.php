<?php

namespace App\Http\Controllers;

use App\DataTables\ClaimDataTable;
use App\DataTables\CustomerClaimDataTable;
use App\Http\Requests\CreateClaimRequest;
use App\Http\Requests\UpdateClaimRequest;
use App\Repositories\ClaimRepository;
use App\Repositories\CustomFieldRepository;
use App\Repositories\OrderStatusRepository;

use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Models\OrderStatus;
use App\Models\Icon;
use App\Models\SubClaim;
use App\Models\Claim;
use App\Models\SubClaimOrder;


class ClaimController extends Controller
{
    /** @var  AnnonceRepository */
    private $claimRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

     

    public function __construct(ClaimRepository $claimRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo,OrderStatusRepository $orderStatusRepo)
    {
        parent::__construct();
        $this->claimRepository = $claimRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
        $this->orderStatusRepository = $orderStatusRepo;
    }

    /**
     * Display a listing of the Claim.
     *
     * @param AnnonceDataTable $annonceDataTable
     * @return Response
     */
    public function index(ClaimDataTable $claimDataTable)
    {
        return $claimDataTable->render('claims.index');
    }

    public function claimsCustomer(CustomerClaimDataTable $claimcustomerDataTable){
        return $claimcustomerDataTable->render('claimcustomers.index');
    }

    /**
     * Show the form for creating a new Claim.
     *
     * @return Response
     */
    public function create()
    {

        $hasCustomField = in_array($this->claimRepository->model(), setting('custom_field_models', []));
        $status = $this->orderStatusRepository->pluck('status', 'id');
        $icons = Icon::select('id','icon','code')->get()->pluck('icon','id');

        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->claimRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('claims.create')->with("customFields", isset($html) ? $html : false)->with('status',$status)->with('icons', $icons);
    }

    /**
     * Store a newly created Claim in storage.
     *
     * @param CreateClaimRequest $request
     *
     * @return Response
     */
    public function store(CreateClaimRequest $request)
    {
        $input = $request->all();
       
        
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->claimRepository->model());
        try {
            $claim = $this->claimRepository->create($input);
            if($request->input('text')){
                foreach($request->input('text') as $key => $value) {
                    $rules["text.{$key}"] = 'required';
                }
    
                foreach($request->input('text') as $key => $value) {
                    SubClaim::create(['text'=>$value,'claim_id'=>$claim->id]);
                }
    
            }
            $claim->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.claim')]));

        return redirect(route('claims.index'));
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
        $claim = $this->claimRepository->findWithoutFail($id);
        $status = $this->orderStatusRepository->pluck('status', 'id');

        if (empty($claim)) {
            Flash::error('Claim not found');

            return redirect(route('claims.index'));
        }

        return view('claims.show')->with('claim', $claim)->with('status', $status)->with('orderStatus');
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
        $claim = $this->claimRepository->findWithoutFail($id);


        if (empty($claim)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.claim')]));

            return redirect(route('claims.index'));
        }
        // $supcategory = $this->supannonceRepository->pluck('name', 'id');
        $customFieldsValues = $claim->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->claimRepository->model());
        $status = $this->orderStatusRepository->pluck('status', 'id');
        $icons = Icon::select('id','icon','code')->get()->pluck('icon','id');

        $subClaims = SubClaim::select('id','text')->withCount('subClaimsOrders as nb_orders')->get();

        $hasCustomField = in_array($this->claimRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('claims.edit')->with('claim', $claim)->with('subClaims',$subClaims)->with("customFields", isset($html) ? $html : false)->with('status', $status)->with('icons', $icons);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateClaimRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateClaimRequest $request)
    {
        $claim = $this->claimRepository->findWithoutFail($id);

        if (empty($claim)) {
            Flash::error('Claim not found');
            return redirect(route('claims.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->claimRepository->model());
        try {
            $claim = $this->claimRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($claim, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $claim->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.claim')]));

        return redirect(route('claims.index'));
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
        $claim = $this->claimRepository->findWithoutFail($id);

        if (empty($claim)) {
            Flash::error('claim not found');

            return redirect(route('claims.index'));
        }

        $this->claimRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.claim')]));

        return redirect(route('claims.index'));
    }

    /**
     * Remove Media of Category
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $claim = $this->claimRepository->findWithoutFail($input['id']);
        try {
            if ($claim->hasMedia($input['collection'])) {
                $claim->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
  

    public function addMoreSubClaim(Request $request)
    {
        $rules = [];


        foreach($request->input('text') as $key => $value) {
            $rules["text.{$key}"] = 'required';
        }


        // $validator = Validator::make($request->all(), $rules);


        // if ($validator->passes()) {


            foreach($request->input('text') as $key => $value) {
                SubClaim::create(['text'=>$value,'claim_id'=>2]);
            }


            return response()->json(['success'=>'done']);
        // }


        return response()->json(['error']);
    }


    function removeSubClaim(Request $request){
        if($request->id){
            $subClaims = SubClaim::findOrfail($request->id);
            if($subClaims){
                $subClaims->delete();
                return response()->json(['success']);

            }else{
                return response()->json(['error']);
            }
        }else{
            return response()->json(['error']);
        }
        
    }

    function editSubClaim(Request $request){
        if($request->id && $request->text){
            $subClaims = SubClaim::findOrfail($request->id);
            if($subClaims){
                $subClaims->update(['text'=>$request->text]);
                return response()->json(['success']);

            }else{
                return response()->json(['error']);
            }
        }else{
            return response()->json(['error']);
        }

    }
        
    function newSubclaim(Request $request){
        if($request->id && $request->text){
            $claim = Claim::findOrfail($request->id);
            if($claim){
                SubClaim::create([
                    'text' => $request->text,
                    'claim_id' =>$claim->id
                ]);
                return response()->json(['success']);

            }else{
                return response()->json(['error']);
            }
        }else{
            return response()->json(['error']);
        }
        

    }
}
