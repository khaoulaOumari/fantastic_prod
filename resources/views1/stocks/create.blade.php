@extends('layouts.app')
@push('css_lib')
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap wysihtml5 - text editor -->
<link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}">
{{--dropzone--}}
<link rel="stylesheet" href="{{asset('plugins/dropzone/bootstrap.min.css')}}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.stock_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.stock_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('stocks.index') !!}">{{trans('lang.stock_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.stock_create')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="clearfix"></div>
  @include('flash::message')
  @include('adminlte-templates::common.errors')
  <div class="clearfix"></div>
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        @can('stocks.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('stocks.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.stock_table')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.stock_create')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::open(['route' => 'stocks.store']) !!}
      <div class="row">
        @include('stocks.fields')
      </div>
      {!! Form::close() !!}
      <div class="clearfix"></div>
    </div>
  </div>
</div>
@include('layouts.media_modal')
@endsection
@push('scripts_lib')
<!-- iCheck -->
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<!-- select2 -->
<script src="{{asset('plugins/select2/select2.min.js')}}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script>
{{--dropzone--}}
<script src="{{asset('plugins/dropzone/dropzone.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    var dropzoneFields = [];
</script>

<script>
  $(document).ready(function() {
  $('.form-control[name="food_id"]').on('change', RestauSelected);
  
  function RestauSelected(event) {
    var target = $(event.target);
    if(target.val()){
      document.getElementById('restaurant_id').disabled=false;
      var url = "";
      url = "{{url('stocks/GetRestaurants')}}";
      $.ajax({
          method: "GET",
          url: url,
          data: {_token: "{{csrf_token()}}", foodId: target.val()},
          // dataType:"json",
          success: function(data){
            // alert("Produit bien modifié");
              // location.reload();

          }, 
          error: function(){
                alert("error ");
          }
      })
    }
    //  + " = " + target.find('option:selected').text()
    // console.log(target.val());
  }
});
  // function RestauSelected(event){
  //     var foodId = '';
  //     var target = $(event.target);
  //     // foodId = elem.options[elem.selectedIndex].getAttribute('name');
  //     console.log(event)

  //     // if(foodId){
  //     //   var url = "";
  //     //   url = "{{url('customorder/edit')}}";
  //     //   $.ajax({
  //     //       method: "POST",
  //     //       url: url,
  //     //       data: {_token: "{{csrf_token()}}", foodId: foodId,id:row.id},
  //     //       success: function(data){
  //     //           // alert("Produit bien modifié");
  //     //           location.reload();

  //     //       }, 
  //     //       error: function(){
  //     //             alert("error ");
  //     //       }
  //     //   })
  //     // }
  //   }
</script>
@endpush