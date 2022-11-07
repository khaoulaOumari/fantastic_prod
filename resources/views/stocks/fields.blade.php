@if($customFields)
    <h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif

<div style="flex: 100%;max-width: 100%;padding: 0 4px;" class="column">


    <div class="form-group row ">
        {!! Form::label('food_id', trans("lang.stock_food_name"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('food_id', $foods, null, ['class' => 'select2 form-control'],['onclick'=> 'RestauSelected()']) !!}
        </div>
    </div>

    <div class="form-group row ">
        {!! Form::label('restaurant_id', trans("lang.stock_restaurant_name"),['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::select('restaurant_id', $restaurants, null, ['class' => 'select2 form-control','disabled' => true]) !!}
        </div>
    </div>



    
 

</div>

<div style="flex: 100%;max-width: 100%;padding: 0 4px;" class="column">   

<!-- Price Field -->
    <div class="form-group row ">
        {!! Form::label('initial_qty', trans("lang.stock_qty_initial"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('initial_qty', null,  ['class' => 'form-control','placeholder'=>  trans("lang.stock_qty_initial"),'step'=>"any", 'min'=>"0"]) !!}
        </div>
    </div>

    <!-- Discount Price Field -->
    <!-- <div class="form-group row ">
        {!! Form::label('discount_price', trans("lang.stock_qty_rest"), ['class' => 'col-3 control-label text-right']) !!}
        <div class="col-9">
            {!! Form::number('discount_price', null,  ['class' => 'form-control','placeholder'=>  trans("lang.stock_qty_rest"),'step'=>"any", 'min'=>"0"]) !!}
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
<!-- Submit Field -->
<div class="form-group col-12 text-right">
    <button type="submit" class="btn btn-{{setting('theme_color')}}"><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.stock')}}</button>
    <a href="{!! route('stocks.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>
