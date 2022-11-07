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
        <h1 class="m-0 text-dark">{{trans('lang.order_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.order_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('orders.index') !!}">{{trans('lang.order_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.order_edit')}}</li>
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
        @can('orders.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.order_table')}}</a>
        </li>
        @endcan
        @can('orders.create')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.order_create')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-pencil mr-2"></i>{{trans('lang.order_edit')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::model($order, ['route' => ['orders.update', $order->id], 'method' => 'patch']) !!}
      <div class="row">
        @include('orders.fields')
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
<script>$.fn.poshytip={defaults:null}</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/jquery-editable/js/jquery-editable-poshytip.min.js"></script>
<script type="text/javascript">
    $.fn.editable.defaults.mode = 'inline';
  
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        }
    }); 
  
    // $('.update').editable({
    //        url: "{{ url('customorder/edit') }}",
    //        type: 'text',
    //        pk: 1,
    //        name: 'name',
    //        title: 'Enter name'
    // });
</script>
<script>
    function RemoveOrder(id){
      swal({
        title: 'Vous voulez vraiment supprimer ce produit?',
        // text: 'This record and it`s details will be permanantly deleted!',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
        },function(value) {
        if (value) {
            var url = "";
            url = "{{url('foodorder/remove')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: id},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            icon: 'success',
                            title: 'produit bien supprimé',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                            position: 'top-end',
                            icon: 'danger',
                            title: 'Vous pouvez pas supprimer ce produit',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                    // alert("Produit bien modifié");
                    // 

                }, 
                error: function(){
                    alert("error");
                }
            })
        }
      });  
    }

    // $('.select').change(function() {
    //   let val = $(this).val();
    //   let myName = $(this).data('num');
    //   console.log(`new val is ${val} from ${myName}`);
    // })

    function EditOrder(id,qnty){
      let new_qnty = document.getElementById(id).value;
      if(new_qnty){
        swal({
        title: 'Vous voulez vraiment modifier ce produit?',
        // text: 'This record and it`s details will be permanantly deleted!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui',
        cancelButtonText: 'Annuler'
        },function(value) {
        if (value) {
            var url = "";
            url = "{{url('foodorder/edit')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", foodId: id,qnty:new_qnty},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            icon: 'success',
                            title: 'produit bien modifié',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                            position: 'top-end',
                            icon: 'danger',
                            title: 'Vous pouvez pas modifié ce produit',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }
                    // alert("Produit bien modifié");
                    // 

                }, 
                error: function(){
                    alert("error");
                }
            })
        }
      }); 
      }
    }

    function AddFood(id) {
         let foodId = $('#food_id').val();
         let qnty = $('#qnty').val();
         let orderId = id;
      if(!qnty || !foodId){
        swal({
                position: 'top-end',
                title: "erreur", 
                confirmButtonText: "{{trans('lang.ok')}}",
                text: 'Merci de remplire tous les champs',
                type: "error",
                timer: 1500
            })
      }else{
        swal({
                title: 'Vous voulez vraiment ajouter ce produit?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui',
                cancelButtonText: 'Annuler'
                },function(value) {
                  if (value) {
                      var url = "";
                      url = "{{url('order/new_food')}}";
                      $.ajax({
                          method: "POST",
                          url: url,
                          data: {_token: "{{csrf_token()}}", 
                          foodId: $('#food_id').val(),
                          qnty: $('#qnty').val(),
                          orderId: id},
                          success: function(data){
                              if(data=="success"){
                                  swal({
                                      position: 'top-end',
                                      icon: 'success',
                                      title: 'produit bien ajouté',
                                      showConfirmButton: false,
                                      timer: 1500
                                  })
                                  location.reload();
                              }else{
                                swal({
                                      position: 'top-end',
                                      icon: 'danger',
                                      title: 'Vous pouvez pas ajouté ce produit',
                                      showConfirmButton: false,
                                      timer: 1500
                                  })
                              }
                              // alert("Produit bien modifié");
                              // 

                          }, 
                          error: function(){
                              alert("error");
                          }
                      })
                  }
              });  
      }
      
    }
      
    // });

</script>
@endpush