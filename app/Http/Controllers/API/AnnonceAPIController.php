<?php

namespace App\Http\Controllers\API;


use App\Models\Annonce;
use App\Repositories\AnnonceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;
use Carbon\Carbon;
/**
 * Class AnnonceController
 * @package App\Http\Controllers\API
 */

class AnnonceAPIController extends Controller
{
    /** @var  AnnonceRepository */
    private $annonceRepository;

    public function __construct(AnnonceRepository $annonceRepo)
    {
        $this->annonceRepository = $annonceRepo;
    }

    /**
     * Display a listing of the Slide.
     * GET|HEAD /slides
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->annonceRepository->pushCriteria(new RequestCriteria($request));
            $this->annonceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        $ads = $this->annonceRepository->all();

        return $this->sendResponse($ads->toArray(), 'ads retrieved successfully');
    }

    public function FetchPopAds(Request $request)
    {
        try{
            $this->annonceRepository->pushCriteria(new RequestCriteria($request));
            $this->annonceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        $ads = $this->annonceRepository->where('type',2)
        ->whereDate('start_date', '<=',Carbon::now()->format('Y-m-d'))
        ->whereDate('end_date', '>=',Carbon::now()->format('Y-m-d'))
        ->where('active',1)
        ->get();

        return $this->sendResponse($ads->toArray(), 'ads retrieved successfully');
    }

    public function FetchFalshAds(Request $request)
    {
        try{
            $this->annonceRepository->pushCriteria(new RequestCriteria($request));
            $this->annonceRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        $ads = $this->annonceRepository->where('type',3)
        ->whereDate('start_date', '<=',Carbon::now()->format('Y-m-d'))
        ->whereDate('end_date', '>=',Carbon::now()->format('Y-m-d'))
        ->where('active',1)
        ->get();

        return $this->sendResponse($ads->toArray(), 'ads retrieved successfully');
    }

    /**
     * Display the specified Slide.
     * GET|HEAD /slides/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Annonce $ads */
        if (!empty($this->annonceRepository)) {
            $ads = $this->annonceRepository->findWithoutFail($id);
        }

        if (empty($ads)) {
            return $this->sendError('Ads not found');
        }

        return $this->sendResponse($ads->toArray(), 'Ads retrieved successfully');
    }
}
