@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
    <!-- User Id Field -->
    <div class="form-group row ">
        {!! Form::label('user_id', trans("lang.order_user_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('user_id', $user, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.order_user_id_help") }}</div>
        </div>
    </div>

    <!-- Driver Id Field -->
    <div class="form-group row ">
        {!! Form::label('driver_id', trans("lang.order_driver_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('driver_id', $driver, null, ['data-empty'=>trans("lang.order_driver_id_placeholder"),'class' => 'select2 not-required form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.order_driver_id_help") }}</div>
        </div>
    </div>

    <!-- Order Status Id Field -->
    <div class="form-group row ">
        {!! Form::label('order_status_id', trans("lang.order_order_status_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('order_status_id', $orderStatus, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.order_order_status_id_help") }}</div>
        </div>
    </div>

    <!-- Status Field -->
    <div class="form-group row ">
        {!! Form::label('status', trans("lang.payment_status"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('status',
            [
            'Waiting for Client' => trans('lang.order_pending'),
            'Not Paid' => trans('lang.order_not_paid'),
            'Paid' => trans('lang.order_paid'),
            ]
            , isset($order->payment) ? $order->payment->status : '', ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.payment_status_help") }}</div>
        </div>
    </div>
    <!-- 'Boolean active Field' -->
    <div class="form-group row ">
        {!! Form::label('active', trans("lang.order_active"),['class' => 'col-3 control-label text-right']) !!}
        <div class="checkbox icheck">
            <label class="col-9 ml-2 form-check-inline">
                {!! Form::hidden('active', 0) !!}
                {!! Form::checkbox('active', 1, null) !!}
            </label>
        </div>
    </div>

</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Tax Field -->
    <div class="form-group row ">
        {!! Form::label('tax', trans("lang.order_tax"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('tax', null,  ['class' => 'form-control', 'step'=>"any",'placeholder'=>  trans("lang.order_tax_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.order_tax_help") }}
            </div>
        </div>
    </div>

    <!-- delivery_fee Field -->
    <div class="form-group row ">
        {!! Form::label('delivery_fee', trans("lang.order_delivery_fee"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('delivery_fee', null,  ['class' => 'form-control','step'=>"any",'placeholder'=>  trans("lang.order_delivery_fee_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.order_delivery_fee_help") }}
            </div>
        </div>
    </div>
    <!-- Hint Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('hint', trans("lang.order_hint"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::textarea('hint', null, ['class' => 'form-control','placeholder'=>
             trans("lang.order_hint_placeholder")  ]) !!}
            <div class="form-text text-muted">{{ trans("lang.order_hint_help") }}</div>
        </div>
    </div> -->
</div>
@if($customFields)
    <div class="clearfix"></div>
    <div class="col-12 custom-field-container">
        <h5 class="col-12 pb-4">{!! trans('lang.custom_field_plural') !!}</h5>
        {!! $customFields !!}
    </div>
@endif

@if (Route::is('orders.edit'))
    <div style="width: 100%;margin-top: 5%;">
    <h3>Liste des produits personnalisés</h3>
        @csrf
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Nom Produit</th>
                    <th>Produit Existant</th>
                    <th>Description</th>
                    <th>Quantite</th>
                    <th>Prix</th>
                </tr>
            </thead>
            <tbody>
                @foreach($customorders as $row)
                    <tr>
                        <td>
                            @if($row->food_id && $row->food_id!=0)
                            <span class="badge badge-success">{{ $row->name }}</span>
                            @endif
                            @if(!$row->food_id && $row->food_id==0)
                            <span class="badge badge-danger">{{ $row->name }}</span>
                            @endif
                        </td>
                        <td>
                            <select id="foodc" onchange="EditCustom(this,{{ $row}});"  class="select2 form-control select2-hidden-accessible selectpicker" >
                                @foreach($customfoods as $model)
                                    <option  value="0"></option>
                                    <option name="{{ $model->id }}" value="{{ $model->id }}">{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            {{ $row->description }}
                        </td>
                        <td>
                            <a href="" class="update" data-name="quantite" data-type="number" data-pk="{{ $row->id }}" data-title="Enter email">{{ $row->quantite }}</a>
                            <!-- <input type="number" name="{{$row->quantite}}"  value="{{ $row->quantite }}" /> -->
                        </td>
                        <td>{!!getPrice($row->price)!!}
                        </td>
                        <td>
                            <!-- <a class="btn btn-info" onclick="EditCustom( {{ $row}} );"><i class="fa fa-edit"></i></a> -->
                            <a class="btn btn-danger" onclick="RemoveCustom( {{ $row->id }} );"><i class="fa fa-trash"></i></a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @endif
        

<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.order')}}</button>
    <a href="{!! route('orders.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>



