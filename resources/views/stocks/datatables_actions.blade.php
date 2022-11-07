<div class='btn-group btn-group-sm'>
  @can('stocks.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('stocks.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  <!-- @can('stocks.edit') -->

  <!-- <a href="#" onclick="edit_partner(this)" data-target="#edit_partner" data-toggle="modal" data-id="{{$id}}" data-full_name="abc" data-code="1">abc</a> -->
  <!-- <a href="#"  onclick="editStock(this)" data-id="{{ $id }}"><i class="fa fa-edit"></i></a> -->
  <!-- <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.stock_edit')}}" href="{{ route('stocks.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a> -->
<!-- 
  {!! Form::open(['route' => ['stocks.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-edit"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-primary',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan -->

  @can('stocks.destroy')
{!! Form::open(['route' => ['stocks.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>





