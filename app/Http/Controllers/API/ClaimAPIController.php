<?php

namespace App\Http\Controllers\API;


use App\Models\Claim;
use App\Models\SubClaim;
use App\Models\SubClaimOrder;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;
use Carbon\Carbon;
/**
 * Class AnnonceController
 * @package App\Http\Controllers\API
 */

class ClaimAPIController extends Controller
{
    

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
            if($request->order_id){
                $order = Order::findOrfail($request->order_id);
                if (empty($order)) {
                    return $this->sendError('Order not found');
                }

                $claims = Claim::where('status_id',$order->order_status_id)->with('subClaims')->get();
            }
        } catch (RepositoryException $e) {
            Flash::error($e->getMessage());
        }
        // $ads = $this->annonceRepository->all();

        return $this->sendResponse($claims->toArray(), 'claims retrieved successfully');
    }

   

  

    /**
     * Display the specified Slide.
     * GET|HEAD /slides/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if(!$user || !auth()->user()->hasRole('client')){
            return $this->sendError('user not found');
        }

        $order = Order::findOrfail($request->order_id);
        $sub_claim = SubClaim::findOrfail($request->claim_id);



        if(empty($order) || empty($sub_claim)){
            return $this->sendError('Order or Claim not found');
        }

        $claim = Claim::findOrfail($sub_claim->claim_id);
        if($claim->status_id != $order->order_status_id){
            return $this->sendError('Not match');
        }

        $sub_claim = SubClaimOrder::create([
            'sub_claim_id' => $sub_claim->id,
            'order_id' => $order->id
        ]);
        return $this->sendResponse($sub_claim->toArray(), 'saved_successfully');
    }


    public function fetchUserClaim(Request $request){
        
        $user = auth()->user();

        if(!$user || !auth()->user()->hasRole('client')){
            return $this->sendError('user not found');
        }

        $data = SubClaimOrder::join('orders','sub_claims_orders.order_id','orders.id')
        ->join('sub_claims','sub_claims_orders.sub_claim_id','sub_claims.id')
        ->where('orders.user_id',$user->id)
        ->select('sub_claims_orders.id','sub_claims.text','sub_claims.claim_id','sub_claims_orders.created_at','orders.id as order_id')
        ->get();

        return $this->sendResponse($data->toArray(), 'claims retrieved successfully');
    }

}
