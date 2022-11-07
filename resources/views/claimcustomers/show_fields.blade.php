
<div class="form-group row col-6">
  {!! Form::label('title', 'Titre:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $claim->title !!}</p>
  </div>
</div>


<div class="form-group row col-6">
  {!! Form::label('status_id', 'Type du statut:', ['class' => 'col-3 control-label text-right']) !!}
  <div class="col-9">
    <p>{!! $claim->orderStatus['status'] !!}</p>
  </div>
</div>


