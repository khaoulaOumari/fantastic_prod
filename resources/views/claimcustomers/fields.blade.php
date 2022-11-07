@if($customFields)
<h5 class="col-12 pb-4">{!! trans('lang.main_fields') !!}</h5>
@endif
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">
<!-- Name Field -->


<div class="form-group row ">
  {!! Form::label('title', trans("lang.claim_description"), ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    {!! Form::textarea('title', null, ['class' => 'form-control','placeholder'=>
     trans("lang.claim_description_placeholder")  ],['id' => 'title_claim']) !!}
    <div class="form-text text-muted">{{ trans("lang.claim_description_placeholder") }}</div>
  </div>
</div>



<div class="form-group row ">
  {!! Form::label('status_id', trans("lang.claim_status"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::select('status_id', $status, null, ['class' => 'select2 form-control']) !!}
    <div class="form-text text-muted">{{ trans("lang.claim_status") }}</div>
  </div>
</div>

<!-- <i class="fa fa-list" aria-hidden="true"></i> -->
<div class="form-group row ">
  {!! Form::label('icon_id', trans("lang.annonce_type"),['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
      {!! Form::select('icon_id', $icons, null, ['class' => 'select2 form-control']) !!}
    <div class="form-text text-muted">Icon</div>
  </div>
</div>









</div>
<div style="flex: 50%;max-width: 50%;padding: 0 4px;" class="column">

</div>

@if(request()->is('claims/create'))
    <div class="container">
      <div class="form-group">
            <div class="alert alert-danger print-error-msg" style="display:none">
            <ul></ul>
            </div>

            <div class="alert alert-success print-success-msg" style="display:none">
            <ul></ul>
            </div>
            <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field">  
                    <tr>  
                        <td><input type="text" name="text[]" placeholder="Enter your Name" class="form-control name_list" /></td>
                        <td><button type="button" name="add" id="add" class="btn btn-success">Add More</button></td>  
                    </tr>  
                </table>  
                <!-- <input type="button" name="submit" id="submit" class="btn btn-info" value="Submit" />   -->
            </div>
      </div> 
    </div>
@else
<table class="table table-bordered data-table">
        <thead>
            <tr>
                <th>Text</th>
                
                <th>Actions</th>
            </tr>
        </thead>
         <tbody>
            @foreach($subClaims as $row)
                <tr>
                    <td>
                      <textarea class="form-control" name="text" cols="2" rows="2" id="text_{{$row->id}}">{{ $row->text }}</textarea>
                    </td>
                    <td>{{$row->nb_orders}} Commandes</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            
                                @can('claims.edit')
                                <a class="btn btn-link text-info" onclick="EditClaim({{$row->id}})">
                                  <i class="fa fa-save"></i>
                                </a>
                                @endcan
                                @can('claims.destroy')
                                <a  class="btn btn-link text-danger" onclick="RemoveClaim({{$row->id}})">
                                    <i class="fa fa-trash"></i>
                                </a>
                                @endcan
                           
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody> 
    </table>
@endif


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
  <button type="submit" class="btn btn-{{setting('theme_color')}}" ><i class="fa fa-save"></i> {{trans('lang.save')}} {{trans('lang.claim')}}</button>
  <a href="{!! route('claims.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.cancel')}}</a>
</div>


@prepend('scripts')
<script type="text/javascript">
</script>
@endprepend



