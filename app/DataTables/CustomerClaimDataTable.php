<?php
/**
 * File name: CategoryDataTable.php
 * Last modified: 2020.04.30 at 08:21:08
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2020
 *
 */

namespace App\DataTables;

use App\Models\Claim;
use App\Models\SubClaim;
use App\Models\SubClaimOrder;
use App\Models\CustomField;
use Yajra\DataTables\Services\DataTable;
use Yajra\DataTables\EloquentDataTable;
use Barryvdh\DomPDF\Facade as PDF;

class CustomerClaimDataTable extends DataTable
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
            // ->editColumn('image', function ($annonce) {
            //     return getMediaColumn($annonce, 'image');
            // })
            ->editColumn('updated_at', function ($subclaimorder) {
                return getDateColumn($subclaimorder, 'updated_at');
            })
           
            // ->editColumn('active', function ($subclaimorder) {
            //     return getBooleanColumn($subclaimorder, 'active');
            // })
            // ->editColumn('type', function ($annonce) {
            //     $x='';
            //     if($annonce['type']==1){
            //         $x.='<span class="badge badge-primary">Slider </span>';
            //     }if ($annonce['type']==2) {
            //         $x.='<span class="badge badge-info">Pop Up </span>';
            //     }if ($annonce['type']==3) {
            //         $x.='<span class="badge badge-warning">Vente Flash </span>';
            //     }
            //     return $x;
            //     // return $food['weight'].' '.$food['unit'];
            //     // return getBooleanColumn($annonce, 'active');
            // })
            ->addColumn('action', 'claimcustomers.datatables_actions')
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
                'data' => 'sub_claim.text',
                'name' => 'subClaim.text',
                'title' => trans('lang.claim_name'),

            ],
            [
                'data' => 'order.user.name',
                'name' => 'order.user.name',
                'title' => trans('lang.order_user_id'),

            ],
                     
            [
                'data' => 'order.order_status.status',
                'name' => 'order.orderStatus.status',
                'title' => trans('lang.order_order_status_id'),

            ],
            [
                'data' => 'order.payment.status',
                'name' => 'order.payment.status',
                'title' => trans('lang.payment_status'),

            ],
            [
                'data' => 'order.created_at',
                'title' => 'Date de la commande',
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Claim::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Claim::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.claimcustomers_' . $field->name),
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
    public function query(SubClaimOrder $model)
    {
        // return $model->newQuery();
        return $model->newQuery()->with('subClaim')->with(['order','order.user','order.orderStatus','order.payment']);
        // ->SubClaim::with(['subClaimsOrders','subClaimsOrders.order']);
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
        return 'claimscustomersdatatable_' . time();
    }
}