<div class='btn-group btn-group-sm'>
  @can('orders.show')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.view_details')}}" href="{{ route('orders.show', $id) }}" class='btn btn-link'>
    <i class="fa fa-eye"></i>
  </a>
  @endcan

  <a data-toggle="tooltip" style="color:#17a2b8;"  title="Voir historique" href="javascript:void(0)" class='btn btn-link showHistory' data-id="{{ $id }}">
    <i class="fa fa-history"></i>
  </a>



  @can('orders.edit')
  <a data-toggle="tooltip" style="color: green;" data-placement="bottom" title="{{trans('lang.order_edit')}}" href="{{ route('orders.edit', $id) }}" class='btn btn-link'>
    <i class="fa fa-edit"></i>
  </a>
  @endcan

  <!-- @can('orders.edit')
  <a data-toggle="tooltip" style="color: #fd7e14;" title="Mofidier Status" href="javascript:void(0)" class='btn btn-link showstatus' data-id="{{ $id }}" data-uid="{{ $order_status_id}}" >
  <i class="fa fa-pencil"></i>
  </a>
  @endcan -->

  @can('orders.destroy')
{!! Form::open(['route' => ['orders.destroy', $id], 'method' => 'delete']) !!}
  {!! Form::button('<i class="fa fa-trash"></i>', [
  'type' => 'submit',
  'class' => 'btn btn-link text-danger',
  'onclick' => "return confirm('Are you sure?')"
  ]) !!}
{!! Form::close() !!}
  @endcan
</div>
