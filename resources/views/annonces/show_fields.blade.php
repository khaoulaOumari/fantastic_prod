<!-- Id Field -->
<div class="form-group row col-6">
  {!! Form::label('id', 'Id:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->id !!}</p>
  </div>
</div>

<!-- Name Field -->
<div class="form-group row col-6">
  {!! Form::label('name', 'Name:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->name !!}</p>
  </div>
</div>


<div class="form-group row col-6">
  {!! Form::label('type', 'Name:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->type !!}</p>
  </div>
</div>

<!-- Description Field -->
<div class="form-group row col-6">
  {!! Form::label('text', 'Description:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->text !!}</p>
  </div>
</div>


<div class="form-group row col-6">
  {!! Form::label('start_date', 'start_date', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->start_date !!}</p>
  </div>
</div>

<!-- Image Field -->
<div class="form-group row col-6">
  {!! Form::label('image', 'Image:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->image !!}</p>
  </div>
</div>


<!-- {!! Form::label('active', trans('lang.annonce_active'), ['class' => 'col-4 control-label']) !!}
    <div class="col-8">
    @if($annonce->active)
      <p><span class='badge badge-success'> Oui</span></p>
      @else
      <p><span class='badge badge-danger'>Non</span></p>
      @endif
  </div> -->

<!-- Created At Field -->
<div class="form-group row col-6">
  {!! Form::label('created_at', 'Created At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->created_at !!}</p>
  </div>
</div>

<!-- Updated At Field -->
<div class="form-group row col-6">
  {!! Form::label('updated_at', 'Updated At:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $annonce->updated_at !!}</p>
  </div>
</div>

