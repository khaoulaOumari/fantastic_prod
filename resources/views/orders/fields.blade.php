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
            @if($order->order_status_id == 5)
                {!! Form::select('driver_id', $driver, null, ['data-empty'=>trans("lang.order_driver_id_placeholder"),'class' => 'select2 not-required form-control',"disabled" => "disabled"]) !!}
            @else
                {!! Form::select('driver_id', $driver, null, ['data-empty'=>trans("lang.order_driver_id_placeholder"),'class' => 'select2 not-required form-control']) !!}
            @endif
            <div class="form-text text-muted">{{ trans("lang.order_driver_id_help") }}</div>
        </div>
    </div>

    <!-- Order Status Id Field -->
    <div class="form-group row ">
        {!! Form::label('order_status_id', trans("lang.order_order_status_id"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            @if($order->order_status_id == 5)
                {!! Form::select('order_status_id', $orderStatus, null, ['class' => 'select2 form-control',"disabled" => "disabled"]) !!}
            @else
                {!! Form::select('order_status_id', $orderStatus, null, ['class' => 'select2 form-control']) !!}
            @endif
            <div class="form-text text-muted">{{ trans("lang.order_order_status_id_help") }}</div>
        </div>

        <!-- <div class="col-9">
            {!! Form::select('order_status_id', $orderStatus, null, ['class' => 'select2 form-control']) !!}
            <div class="form-text text-muted">{{ trans("lang.order_order_status_id_help") }}</div>
        </div> -->
    </div>

    <!-- Status Field -->
    

</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

    <!-- Tax Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('tax', trans("lang.order_tax"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('tax', null,  ['class' => 'form-control', 'step'=>"any",'placeholder'=>  trans("lang.order_tax_placeholder")]) !!}
            <div class="form-text text-muted">
                {{ trans("lang.order_tax_help") }}
            </div>
        </div>
    </div> -->

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


@if(isset($order))
<div class="container" style="margin-top: 3%;">
    <h2>La liste des produits</h2>
    <div class="row">
        <form id="myForm" name="myForm">
            <div class="col-6">
                <div class="form-group">
        
                    <!-- {!! Form::label('driver_id', trans("lang.order_driver_id"),['class' => 'col-3 control-label text-right']) !!} -->
        
                        {!! Form::select('food_id', $foods, null, ['class' => 'select2 required form-control', 'id'=>'food_id']) !!}

                    <!-- <label>Produits</label> -->
                    <!-- <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required> -->
                </div>              
            </div>
            <div class="col-3">
                <div class="form-group">
                    <!-- <label>Quantité</label> -->
                        <input type="number" class="form-control" id="qnty" name="qnty" min="1" placeholder="Quantité produit" >
                </div>
            </div>
            <div class="col-3">
                <button type="button" id="btn-save" class="btn btn-success" onclick="AddFood({{$order->id}})" style="color: white;">
                    <i class="fa fa-plus"></i> Nouveau Produit
                </button>
            </div>
        </form>
    </div>
    

    <table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>Produit</th>
                <!-- <th>Name</th> -->
                <th>Quantité</th>
                <th>Prix</th>
                <th>Actions</th>
            </tr>
        </thead>
         <tbody>
            @foreach($FoodsOrder as $row)
                <tr>
                    <!-- <td>{{ $row->name }}</td> -->
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->price }}</td>
                    <td>
                        <input type="number" min="1" value="{{ $row->quantity }}" id="{{$row->id}}" />
                        @can('orders.edit')    
                            <a data-toggle="tooltip" data-placement="bottom" onclick="EditOrder({{$row->id}},{{$row->quantity}})"   class="btn btn-link  text-info" data-original-title="Modifier">
                                <i class="fa fa-edit"></i>
                            </a>
                            @endcan
                      
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            @can('foods.edit')
                                <a data-toggle="tooltip" data-placement="bottom" target="_blank" title="{{trans('lang.view_details')}}" href="{{ url('foods' , [ 'id' => $row->food_id ]) }}/edit" class='btn btn-link'>
                                    <i class="fa fa-eye"></i>
                                </a>
                            @endcan
                            @can('orders.destroy')
                                <a  class="btn btn-link text-danger" onclick="RemoveOrder({{$row->id}})">
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endcan
                           
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody> 
    </table>

    <div class="modal fade" id="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="formModalLabel">Nouveau Produit</h4>
                    </div>
                    <div class="modal-body">
                        <form id="myForm" name="myForm" class="form-horizontal" required>
                            <div class="form-group">
                                <label>Produits</label>
                                <input type="text" class="form-control" id="title" name="title"
                                        placeholder="Enter title" required>
                            </div>
                            <div class="form-group">
                                <label>Quantité</label>
                                    <input type="number" class="form-control" id="description" name="description" min="1"
                                        placeholder="Quantité produit" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="btn-save" value="add">Ajouter
                        </button>
                        <input type="hidden" id="todo_id" name="todo_id" value="0">
                    </div>
                </div>
            </div>
    </div>

</div>
@endif


        

<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.order')}}</button>
    <a href="{!! route('orders.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>



