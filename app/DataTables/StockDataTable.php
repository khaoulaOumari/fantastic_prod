<?php
/**
 * File name: CategoryDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\Stock;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class StockDataTable extends DataTable
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
            ->editColumn('updated_at', function ($stock) {
                return getDateColumn($stock, 'updated_at');
            })
            ->editColumn('initial_qty', function ($stock) {
                return '<div><input type="number" min="0" name="initial_qty" id="initial_qty['.$stock->id.']" value="'.$stock->initial_qty.'" />
                        <a class="btn btn-info" onclick="EditStock('.$stock->id.','.$stock->initial_qty.');"><i class="fa fa-save"></i></a>
                        </div>';
                // return getDateColumn($stock, 'initial_qty');
            })
            ->addColumn('action', 'stocks.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
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
                'data' => 'food.name',
                'title' => trans('lang.stock_food_name'),

            ],
            [
                'data' => 'restaurant.name',
                'title' => trans('lang.stock_restaurant_name'),

            ],
            [
                'data' => 'initial_qty',
                'title' => trans('lang.stock_qty_initial'),

            ],
            // [
            //     'data' => 'reste_qty',
            //     'title' => trans('lang.stock_qty_rest'),

            // ],
            [
                'data' => 'user.name',
                'title' => trans('lang.stock_user_name'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.category_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Stock::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Stock::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.stock_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Post $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Stock $model)
    {
        // return $model->newQuery();
        return $model->newQuery()->with("food")->with("restaurant")->with("user")->select('stocks.*')->orderBy('stocks.updated_at','desc');
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
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'stocksdatatable_' . time();
    }
}