<div class='btn-group btn-group-sm'>


  @can('foods.edit')
  <a data-toggle="tooltip" data-placement="bottom" title="{{trans('lang.food_edit')}}" href="{{ route('foods.edit', $id) }}" class='btn btn-link'>
  <i class="fa fa-eye"></i>
  </a>
  @endcan


</div>
