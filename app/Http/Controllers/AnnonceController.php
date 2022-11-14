<?php

namespace App\Http\Controllers;

use App\DataTables\AnnonceDataTable;
use App\Http\Requests\CreateAnnonceRequest;
use App\Http\Requests\UpdateAnnonceRequest;
use App\Repositories\AnnonceRepository;
use App\Repositories\CustomFieldRepository;

use App\Repositories\UploadRepository;
use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Support\Facades\DB;
use App\Models\Annonce;
use App\Models\AnnonceFood;
use App\Models\Food;

class AnnonceController extends Controller
{
    /** @var  AnnonceRepository */
    private $annonceRepository;

    /**
     * @var CustomFieldRepository
     */
    private $customFieldRepository;

    /**
     * @var UploadRepository
     */
    private $uploadRepository;

     

    public function __construct(AnnonceRepository $annonceRepo, CustomFieldRepository $customFieldRepo, UploadRepository $uploadRepo)
    {
        parent::__construct();
        $this->annonceRepository = $annonceRepo;
        $this->customFieldRepository = $customFieldRepo;
        $this->uploadRepository = $uploadRepo;
    }

    /**
     * Display a listing of the Annonce.
     *
     * @param AnnonceDataTable $annonceDataTable
     * @return Response
     */
    public function index(AnnonceDataTable $annonceDataTable)
    {
        return $annonceDataTable->render('annonces.index');
    }

    /**
     * Show the form for creating a new Annonce.
     *
     * @return Response
     */
    public function create()
    {

        $hasCustomField = in_array($this->annonceRepository->model(), setting('custom_field_models', []));
        $promofoods = Food::where('discount_price','!=',null)
        ->select('id','discount_price',DB::raw('CONCAT(name, " (", discount_price," Dh)") AS name'))
        ->take(10)->get()->pluck('name', 'id','discount_price')
        ->toArray();
        $promofoodselected =[];
        // $img = Food::getMedia('images')->first()->getUrl('thumb');
        if ($hasCustomField) {
            $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->annonceRepository->model());
            $html = generateCustomField($customFields);
        }
        return view('annonces.create')->with('promofoods',$promofoods)->with('promofoodselected', $promofoodselected)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Store a newly created Annonce in storage.
     *
     * @param CreateAnnonceRequest $request
     *
     * @return Response
     */
    public function store(CreateAnnonceRequest $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->annonceRepository->model());
        try {
            // $annonce = $this->annonceRepository->create($input);
            // if(isset($input['foods']) && count($input['foods'])>0){
            //     foreach($input['foods'] as $row){
            //         AnnonceFood::create([
            //             'annonce_id' => $annonce->id,
            //             'food_id' => $row
            //         ]);
            //     }
            // }
            if($input['end_date'] < $input['start_date']){
                Flash::error('Date est invalide');
                return redirect(route('annonces.index'));
            }
            $exist = Annonce::whereDate('start_date', '<=',$input['start_date'])
                    ->whereDate('end_date', '>=',$input['end_date'])
                    ->where('type',$input['type'])
                    ->first();
            if($exist){
                Flash::error('Une annonce dans cette période est déjà exist');
                return redirect(route('annonces.index'));
            }else{
                $annonce = new Annonce();
                $annonce->name = $input['name'];
                $annonce->type = $input['type'];
                $annonce->active = $input['active'];
                $annonce->start_date = $input['start_date']." 00:00:00";
                $annonce->end_date = $input['end_date']." 23:59:59";
                $annonce->showing = $input['showing'];
                $annonce->save();

                $annonce->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
                if (isset($input['image']) && $input['image']) {
                    $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($annonce, 'image');
                }    
            }
            
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.saved_successfully', ['operator' => __('lang.annonce')]));

        return redirect(route('annonces.index'));
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
        $annonce = $this->annonceRepository->findWithoutFail($id);

        if (empty($annonce)) {
            Flash::error('Annonce not found');

            return redirect(route('annonces.index'));
        }

        return view('annonces.show')->with('annonce', $annonce);
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
        $annonce = $this->annonceRepository->findWithoutFail($id);


        if (empty($annonce)) {
            Flash::error(__('lang.not_found', ['operator' => __('lang.annonce')]));

            return redirect(route('annonces.index'));
        }
        // $supcategory = $this->supannonceRepository->pluck('name', 'id');
        $promofoods = Food::where('discount_price','!=',null)
        ->select('id',DB::raw('CONCAT(name, " (", discount_price," Dh)") AS name'))->take(10)->get()->pluck('name', 'id')->toArray();
        $promofoodselected = $annonce->join('annonce_foods','annonces.id','annonce_foods.annonce_id')
                                    ->join('foods','annonce_foods.food_id','foods.id')
                                    ->select('foods.id',DB::raw('CONCAT(foods.name, " (", foods.discount_price," Dh)") AS name'))
                                    ->pluck('id','name')
                                    ->toArray();

        $customFieldsValues = $annonce->customFieldsValues()->with('customField')->get();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->annonceRepository->model());
        $hasCustomField = in_array($this->annonceRepository->model(), setting('custom_field_models', []));
        if ($hasCustomField) {
            $html = generateCustomField($customFields, $customFieldsValues);
        }

        return view('annonces.edit')->with('annonce', $annonce)->with('promofoods', $promofoods)->with('promofoodselected', $promofoodselected)->with("customFields", isset($html) ? $html : false);
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param UpdateAnnonceRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAnnonceRequest $request)
    {
        $annonce = $this->annonceRepository->findWithoutFail($id);

        if (empty($annonce)) {
            Flash::error('Annonce not found');
            return redirect(route('annonces.index'));
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->annonceRepository->model());
        try {

            $exist = Annonce::whereDate('start_date', '<=',$input['start_date'])
            ->whereDate('end_date', '>=',$input['end_date'])
            ->where('type',$input['type'])->where('id','!=',$id)
            ->first();
            if($exist){
                Flash::error('Une annonce dans cette période est déjà exist');
                return redirect(route('annonces.index'));
            }else{
                $annonce = $this->annonceRepository->update($input, $id);
                $annonce->start_date = $input['start_date']." 00:00:00";
                $annonce->end_date = $input['end_date']." 23:59:59";
                $annonce->save();
                if (isset($input['image']) && $input['image']) {
                    $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                    $mediaItem = $cacheUpload->getMedia('image')->first();
                    $mediaItem->copy($annonce, 'image');
                }
                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $annonce->customFieldsValues()
                        ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }
        } catch (ValidatorException $e) {
            Flash::error($e->getMessage());
        }

        Flash::success(__('lang.updated_successfully', ['operator' => __('lang.annonce')]));

        return redirect(route('annonces.index'));
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
        $annonce = $this->annonceRepository->findWithoutFail($id);

        if (empty($annonce)) {
            Flash::error('annonce not found');

            return redirect(route('annonces.index'));
        }

        $this->annonceRepository->delete($id);

        Flash::success(__('lang.deleted_successfully', ['operator' => __('lang.annonce')]));

        return redirect(route('annonces.index'));
    }

    /**
     * Remove Media of Category
     * @param Request $request
     */
    public function removeMedia(Request $request)
    {
        $input = $request->all();
        $annonce = $this->annonceRepository->findWithoutFail($input['id']);
        try {
            if ($annonce->hasMedia($input['collection'])) {
                $annonce->getFirstMedia($input['collection'])->delete();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
