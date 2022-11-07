<?php
/**
 * File name: FoodController.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\Http\Controllers;

use App\DataTables\TopFoodDataTable;

use Flash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Prettus\Validator\Exceptions\ValidatorException;

class TopFoodController extends Controller
{



    /**
     * Display a listing of the Food.
     *
     * @param TopFoodDataTable $topfoodDataTable
     * @return Response
     */
    public function index(TopFoodDataTable $topfoodDataTable)
    {
        return $topfoodDataTable->render('topfoods.index');
        // $data=Food::
    }

    

    
}
