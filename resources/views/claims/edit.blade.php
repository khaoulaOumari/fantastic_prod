@extends('layouts.app')
@push('css_lib')
<!-- iCheck -->
<link rel="stylesheet" href="{{asset('plugins/iCheck/flat/blue.css')}}">
<!-- select2 -->
<link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
<!-- bootstrap wysihtml5 - text editor -->
<!-- <link rel="stylesheet" href="{{asset('plugins/summernote/summernote-bs4.css')}}"> -->
{{--dropzone--}}
<link rel="stylesheet" href="{{asset('plugins/dropzone/bootstrap.min.css')}}">
@endpush
@section('content')
<!-- Content Header (Page header) -->
<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">{{trans('lang.claim_plural')}}<small class="ml-3 mr-3">|</small><small>{{trans('lang.claim_desc')}}</small></h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="{{url('/dashboard')}}"><i class="fa fa-dashboard"></i> {{trans('lang.dashboard')}}</a></li>
          <li class="breadcrumb-item"><a href="{!! route('claims.index') !!}">{{trans('lang.claim_plural')}}</a>
          </li>
          <li class="breadcrumb-item active">{{trans('lang.claim_edit')}}</li>
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
        @can('claims.index')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('claims.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.claim_table')}}</a>
        </li>
        @endcan
        @can('claims.create')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('claims.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.claim_create')}}</a>
        </li>
        @endcan
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-pencil mr-2"></i>{{trans('lang.claim_edit')}}</a>
        </li>
      </ul>
    </div>
    <div class="card-body">
      {!! Form::model($claim, ['route' => ['claims.update', $claim->id], 'method' => 'patch']) !!}
      <div class="row">
        @include('claims.fields')
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
<!-- <script src="{{asset('plugins/summernote/summernote-bs4.min.js')}}"></script> -->
{{--dropzone--}}
<script src="{{asset('plugins/dropzone/dropzone.js')}}"></script>
<script type="text/javascript">
    Dropzone.autoDiscover = false;
    var dropzoneFields = [];


    function RemoveClaim(id){
      swal({
        title: "supprimer", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment supprimer sous réclamation ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
        },function(value) {
        if (value) {
            var url = "";
            url = "{{url('subclaims/remove')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: id},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'sous-réclamation bien supprimée',
                            type: "success",
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                        position: 'top-end',
                            title: "erreur", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Vous pouvez pas supprimer cette sous-réclamation',
                            type: "error",
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

    function EditClaim(id){
      swal({
        title: "Modifier", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment modifier sous réclamation ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
        },function(value) {
        if (value) {
          var text = document.getElementById('text_'+id).value;
            var url = "";
            url = "{{url('subclaims/edit')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: id,text:text},
                success: function(data){
                    if(data=="success"){
                        swal({
                          position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'sous-réclamation bien modifiée',
                            type: "success",
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                        position: 'top-end',
                            title: "erreur", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Vous pouvez pas supprimer cette sous-réclamation',
                            type: "error",
                            timer: 1500
                        })
                    }
                }, 
                error: function(){
                    alert("error");
                }
            })
        }
      });  
    }

    function AddSubClaim(id){

      swal({
        title: "Modifier", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment modifier sous réclamation ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
        },function(value) {
        if (value) {
          var text = document.getElementById('sub_claim').value;
            var url = "";
            url = "{{url('subclaims/new')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: id,text:text},
                success: function(data){
                    if(data=="success"){
                        swal({
                          position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'sous-réclamation bien ajoutée',
                            type: "success",
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                        position: 'top-end',
                            title: "erreur", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Vous pouvez pas effectuer cette tâche',
                            type: "error",
                            timer: 1500
                        })
                    }
                }, 
                error: function(){
                    alert("error");
                }
            })
        }
      });  

      
    }
</script>

@endpush