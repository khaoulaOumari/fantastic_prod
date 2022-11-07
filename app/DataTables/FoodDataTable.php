<?php
/**
 * File name: FoodDataTable.php
 * Last modified: 2020.05.04 at 09:04:18
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Food;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Services\DataTable;

class FoodDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('image', function ($food) {
                return getMediaColumn($food, 'image');
            })
            ->editColumn('price', function ($food) {
                return getPriceColumn($food);
            })
            ->editColumn('prix_achat', function ($food) {
                return getPriceColumn($food,'prix_achat');
            })
            ->editColumn('discount_price', function ($food) {
                return getPriceColumn($food,'discount_price');
            })
            // ->editColumn('weight', function ($food) {
            //     return $food['weight'].' '.$food['unit'];
            // })
            ->editColumn('updated_at', function ($food) {
                return getDateColumn($food, 'updated_at');
            })
            // ->editColumn('featured', function ($food) {
            //     return getBooleanColumn($food, 'featured');
            // })
            ->editColumn('featured', function ($food) {
                // return getBooleanColumnSwitch($food, 'active');
                if($food->featured){
                    return '<div class="checkbox-switch">
                            <input type="checkbox" checked=""  value="1" onclick="showFood('.$food->id.');" name="active" class="input-checkbox" id="active['.$food->id.']">
                            <div class="checkbox-animate">
                            <span class="checkbox-off">Oui</span>
                            <span class="checkbox-on">Non</span>
                            </div>
                        </div>';
                }else{
                    return '<div class="checkbox-switch">
                            <input type="checkbox" value="0" onclick="showFood('.$food->id.');" name="active" class="input-checkbox" id="active['.$food->id.']">
                            <div class="checkbox-animate">
                            <span class="checkbox-off">Oui</span>
                            <span class="checkbox-on">Non</span>
                            </div>
                        </div>';
                }
            })
            ->addColumn('action', 'foods.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Food $model)
    {

        if (auth()->user()->hasRole('admin')) {
            // return $model->newQuery()->with("category")->select('foods.*')->orderBy('foods.updated_at','desc');
            return $model->newQuery()->with("category")->select('foods.*')->orderBy('foods.updated_at','desc');
        } else if (auth()->user()->hasRole('manager')) {
            return $model->newQuery()->with("category")
                ->join('food_restaurants', 'foods.id', '=', 'food_restaurants.food_id')
                ->join('restaurants', 'food_restaurants.restaurant_id', '=', 'restaurants.id')
                ->join("user_restaurants", "user_restaurants.restaurant_id", "=", "food_restaurants.restaurant_id")
                ->where('user_restaurants.user_id', auth()->id())
                ->groupBy('foods.id')
                ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        } else if (auth()->user()->hasRole('Livreur')) {
            return $model->newQuery()->with("category")
                ->join('food_restaurants', 'foods.id', '=', 'food_restaurants.food_id')
                ->join('restaurants', 'food_restaurants.restaurant_id', '=', 'restaurants.id')
                ->join("driver_restaurants", "driver_restaurants.restaurant_id", "=", "food_restaurants.restaurant_id")
                ->where('driver_restaurants.user_id', auth()->id())
                ->groupBy('foods.id')
                ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        } else if (auth()->user()->hasRole('client')) {
            return $model->newQuery()->with("category")
                ->join("food_orders", "food_orders.food_id", "=", "foods.id")
                ->join("orders", "food_orders.order_id", "=", "orders.id")
                ->where('orders.user_id', auth()->id())
                ->groupBy('foods.id')
                ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        }

        // if (auth()->user()->hasRole('admin')) {
        //     return $model->newQuery()->with("category")->select('foods.*')->orderBy('foods.updated_at','desc');
        // } else if (auth()->user()->hasRole('manager')) {
        //     return $model->newQuery()->with("restaurant")->with("category")
        //         ->join("user_restaurants", "user_restaurants.restaurant_id", "=", "foods.restaurant_id")
        //         ->where('user_restaurants.user_id', auth()->id())
        //         ->groupBy('foods.id')
        //         ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        // } else if (auth()->user()->hasRole('driver')) {
        //     return $model->newQuery()->with("restaurant")->with("category")
        //         ->join("driver_restaurants", "driver_restaurants.restaurant_id", "=", "foods.restaurant_id")
        //         ->where('driver_restaurants.user_id', auth()->id())
        //         ->groupBy('foods.id')
        //         ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        // } else if (auth()->user()->hasRole('client')) {
        //     return $model->newQuery()->with("restaurant")->with("category")
        //         ->join("food_orders", "food_orders.food_id", "=", "foods.id")
        //         ->join("orders", "food_orders.order_id", "=", "orders.id")
        //         ->where('orders.user_id', auth()->id())
        //         ->groupBy('foods.id')
        //         ->select('foods.*')->orderBy('foods.updated_at', 'desc');
        // }
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['title'=>trans('lang.actions'),'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'name',
                'title' => trans('lang.food_name'),

            ],
            [
                'data' => 'image',
                'title' => trans('lang.food_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'price',
                'title' => trans('lang.food_price'),

            ],
            [
                'data' => 'prix_achat',
                'title' => trans('lang.purchase_price'),

            ],
            [
                'data' => 'discount_price',
                'title' => trans('lang.food_discount_price'),

            ],
            // [
            //     'data' => 'weight',
            //     'title' => trans('lang.food_weight'),

            // ],
            [
                'data' => 'featured',
                'title' => trans('lang.food_featured'),

            ],
            // [
            //     'data' => 'restaurant.name',
            //     'title' => trans('lang.food_restaurant_id'),

            // ],
            [
                'data' => 'category.name',
                'title' => trans('lang.food_category_id'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.food_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Food::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Food::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.food_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'foodsdatatable_' . time();
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }
}