<?php

namespace App\Http\Controllers\API;


use App\Http\Requests\CreateCartRequest;
use App\Http\Requests\CreateFavoriteRequest;
use App\Models\Cart;
use App\Repositories\CartRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\Response;
use Prettus\Repository\Exceptions\RepositoryException;
use Flash;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CartController
 * @package App\Http\Controllers\API
 */

class CartAPIController extends Controller
{
    /** @var  CartRepository */
    private $cartRepository;

    public function __construct(CartRepository $cartRepo)
    {
        $this->cartRepository = $cartRepo;
    }

    /**
     * Display a listing of the Cart.
     * GET|HEAD /carts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try{
            $this->cartRepository->pushCriteria(new RequestCriteria($request));
            $this->cartRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $carts = $this->cartRepository->all();

        return $this->sendResponse($carts->toArray(), 'Carts retrieved successfully');
    }

    /**
     * Display a listing of the Cart.
     * GET|HEAD /carts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function count(Request $request)
    {
        try{
            $this->cartRepository->pushCriteria(new RequestCriteria($request));
            $this->cartRepository->pushCriteria(new LimitOffsetCriteria($request));
        } catch (RepositoryException $e) {
            return $this->sendError($e->getMessage());
        }
        $count = $this->cartRepository->count();

        return $this->sendResponse($count, 'Count retrieved successfully');
    }

    /**
     * Display the specified Cart.
     * GET|HEAD /carts/{id}
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        /** @var Cart $cart */
        if (!empty($this->cartRepository)) {
            $cart = $this->cartRepository->findWithoutFail($id);
        }

        if (empty($cart)) {
            return $this->sendError('Cart not found');
        }

        return $this->sendResponse($cart->toArray(), 'Cart retrieved successfully');
    }
    /**
     * Store a newly created Cart in storage.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $input = $request->all();
        try {
            if(isset($input['reset']) && $input['reset'] == '1'){
                // delete all items in the cart of current user
                $this->cartRepository->deleteWhere(['user_id'=> $input['user_id']]);
            }
            $old_cart = $this->cartRepository->where('user_id',$input['user_id'])->where('food_id',$input['food_id'])->first();
            if($old_cart){
                $old_cart = $this->cartRepository->update(['quantity'=>$old_cart->quantity+$request->quantity], $old_cart->id);
            }else{
                $old_cart = $this->cartRepository->create($input);
            }
            
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($old_cart->toArray(), __('lang.saved_successfully',['operator' => __('lang.cart')]));
    }
    
    
    public function storeMultiple(Request $request)
    {
        $input = $request->all();
        try {
            if(isset($input['reset']) && $input['reset'] == '1'){
                // delete all items in the cart of current user
                $this->cartRepository->deleteWhere(['user_id'=> $input['user_id']]);
            }
            if(count($request->products)>0){
                foreach($request->products as $row){
                    $cart = $this->cartRepository->where('user_id',$input['user_id'])->where('food_id',$row['food_id'])->first();
                    if($cart){
                        $cart = $this->cartRepository->update(['quantity'=>$cart->quantity+$row['quantity']], $cart->id);
                    }else{
                        $cart = new Cart();
                        $cart->food_id = $row['food_id'];
                        $cart->user_id = $input['user_id'];
                        $cart->quantity = $row['quantity'];
                        $cart->save();
                    }

                    
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($cart->toArray(), __('lang.saved_successfully',['operator' => __('lang.cart')]));
    }

    /**
     * Update the specified Cart in storage.
     *
     * @param int $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            return $this->sendError('Cart not found');
        }
        $input = $request->all();

        try {
//            $input['extras'] = isset($input['extras']) ? $input['extras'] : [];
            $cart = $this->cartRepository->update($input, $id);

        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($cart->toArray(), __('lang.saved_successfully',['operator' => __('lang.cart')]));
    }

    /**
     * Remove the specified Favorite from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $cart = $this->cartRepository->findWithoutFail($id);

        if (empty($cart)) {
            return $this->sendError('Cart not found');

        }

        $cart = $this->cartRepository->delete($id);

        return $this->sendResponse($cart, __('lang.deleted_successfully',['operator' => __('lang.cart')]));

    }


    public function editCart(Request $request,$id)
    {
        $user = auth()->user();
        if($user && $user->hasRole('client')){
            $cart = $this->cartRepository->where('food_id',$id)->where('user_id',$user->id)->first();

            if (empty($cart)) {
                return $this->sendError('Cart not found');
            }

            try {
                $cart = $this->cartRepository->update(['quantity'=>$request->quantity], $cart->id);
                if($cart->quantity == 0){
                     $this->cartRepository->delete($cart->id);
                }

            } catch (ValidatorException $e) {
                return $this->sendError($e->getMessage());
            }

            return $this->sendResponse($cart->toArray(), __('lang.saved_successfully',['operator' => __('lang.cart')]));    
        }else{
            return $this->sendError('User not found');
        }
        
    }

}
