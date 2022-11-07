<?php
/**
 * File name: CategoryAPIController.php
 * Last modified: 2020.05.04 at 09:04:18
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers\API;


use App\Criteria\Categories\CategoriesOfCuisinesCriteria;
use App\Criteria\Categories\CategoriesOfRestaurantCriteria;
use App\Http\Controllers\Controller;
use App\Models\SupCategory;
use App\Repositories\SupCategoryRepository;
use App\Http\Requests\UpdateSupCategoryRequest;
use Illuminate\Http\Request;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class CategoryController
 * @package App\Http\Controllers\API
 */
class SupCategoryAPIController extends Controller
{
    /** @var  SupCategoryRepository */
    private $supcategoryRepository;

    public function __construct(SupCategoryRepository $supcategoryRepo)
    {
        $this->supcategoryRepository = $supcategoryRepo;
    }

    /**
     * Display a listing of the Category.
     * GET|HEAD /categories
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $this->supcategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->supcategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->supcategoryRepository->pushCriteria(new CategoriesOfCuisinesCriteria($request));
            $this->supcategoryRepository->pushCriteria(new CategoriesOfRestaurantCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $categories = $this->supcategoryRepository->with('categories')->all();
        // $categories = $this->supcategoryRepository->with('categories', function ($query){
        //     $query->limit(1);
        // })->all();

        return $this->sendResponse($categories->toArray(), 'Categories retrieved successfully');
    }
    
    
     public function Fetch(Request $request)
    {
        try {
            $this->supcategoryRepository->pushCriteria(new RequestCriteria($request));
            $this->supcategoryRepository->pushCriteria(new LimitOffsetCriteria($request));
            $this->supcategoryRepository->pushCriteria(new CategoriesOfCuisinesCriteria($request));
            $this->supcategoryRepository->pushCriteria(new CategoriesOfRestaurantCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $categories = $this->supcategoryRepository->with(['categories','categories.foods'])->all();
        // $categories = $this->supcategoryRepository->with(['categories' => function ($query) {
        //     $query->join('foods', 'categories.id','foods.category_id')
        //     ->get('foods.*');
        // }])->get();
        
        
        // with('categories')->get();

        return $this->sendResponse($categories->toArray(), 'Categories retrieved successfully');
    }

    /**
     * Display the specified Category.
     * GET|HEAD /categories/{id}
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var SupCategory $category */
        if (!empty($this->supcategoryRepository)) {
            $category = $this->supcategoryRepository->with(['categories','categories.limitFoods'])->findWithoutFail($id);
            
        }

        if (empty($category)) {
            return $this->sendError('Category not found');
        }

        return $this->sendResponse($category->toArray(), 'Category retrieved successfully');
    }

    /**
     * Store a newly created Category in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->supcategoryRepository->model());
        try {
            $category = $this->supcategoryRepository->create($input);
            $category->customFieldsValues()->createMany(getCustomFieldsValues($customFields, $request));
            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($category, 'image');
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($category->toArray(), __('lang.saved_successfully', ['operator' => __('lang.category')]));
    }

    /**
     * Update the specified Category in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $category = $this->supcategoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError('Category not found');
        }
        $input = $request->all();
        $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->supcategoryRepository->model());
        try {
            $category = $this->supcategoryRepository->update($input, $id);

            if (isset($input['image']) && $input['image']) {
                $cacheUpload = $this->uploadRepository->getByUuid($input['image']);
                $mediaItem = $cacheUpload->getMedia('image')->first();
                $mediaItem->copy($category, 'image');
            }
            foreach (getCustomFieldsValues($customFields, $request) as $value) {
                $category->customFieldsValues()
                    ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($category->toArray(), __('lang.updated_successfully', ['operator' => __('lang.category')]));

    }

    /**
     * Remove the specified Category from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $category = $this->supcategoryRepository->findWithoutFail($id);

        if (empty($category)) {
            return $this->sendError('Category not found');
        }

        $category = $this->supcategoryRepository->delete($id);

        return $this->sendResponse($category, __('lang.deleted_successfully', ['operator' => __('lang.category')]));
    }
}
