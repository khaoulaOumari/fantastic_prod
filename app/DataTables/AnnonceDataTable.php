<?php
/**
 * File name: CategoryDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\Annonce;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class AnnonceDataTable extends DataTable
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
            ->editColumn('image', function ($annonce) {
                return getMediaColumn($annonce, 'image');
            })
            ->editColumn('updated_at', function ($annonce) {
                return getDateColumn($annonce, 'updated_at');
            })
           
            ->editColumn('active', function ($annonce) {
                return getBooleanColumn($annonce, 'active');
            })
            ->editColumn('type', function ($annonce) {
                $x='';
                if($annonce['type']==1){
                    $x.='<span class="badge badge-primary">Slider </span>';
                }if ($annonce['type']==2) {
                    $x.='<span class="badge badge-info">Pop Up </span>';
                }if ($annonce['type']==3) {
                    $x.='<span class="badge badge-warning">Vente Flash </span>';
                }
                return $x;
                // return $food['weight'].' '.$food['unit'];
                // return getBooleanColumn($annonce, 'active');
            })
            ->addColumn('action', 'annonces.datatables_actions')
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
                'data' => 'name',
                'title' => trans('lang.annonce_name'),

            ],
            [
                'data' => 'image',
                'title' => trans('lang.annonce_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'type',
                'title' => trans('lang.annonce_type'),

            ],
            [
                'data' => 'active',
                'title' => trans('lang.annonce_active'),

            ],
            [
                'data' => 'start_date',
                'title' => trans('lang.start_date'),

            ],
            [
                'data' => 'end_date',
                'title' => trans('lang.end_date'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.custom_field_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Annonce::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Annonce::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.annonce_' . $field->name),
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
    public function query(Annonce $model)
    {
        return $model->newQuery();
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
        return 'annoncesdatatable_' . time();
    }
}