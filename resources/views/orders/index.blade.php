@extends('layouts.app')

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
          <li class="breadcrumb-item active">{{trans('lang.order_table')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<div class="content">
  <div class="clearfix"></div>
  @include('flash::message')
  <div class="card">
    <div class="card-header">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.order_table')}}</a>
        </li>
        @can('orders.create')
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.create') !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.order_create')}}</a>
        </li>
        @endcan
        @include('layouts.right_toolbar', compact('dataTable'))
      </ul>
    </div>
    <div class="card-body">
      @include('orders.table')
      <div class="clearfix"></div>
    </div>
  </div>
</div>


<div class="modal fade" id="ajaxModel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modelHeading"> Historique de la commande</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="tblempinfo">

              
                
            </div>
        </div>
    </div>
</div>

        <div class="modal fade" id="formModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content" style="height: 400px;width: 115%;">
                    <div class="modal-header">
                        <h4 class="modal-title" id="formModalLabel">Modifier le statut</h4>
                    </div>
                    <div class="modal-body">
                             <div class="form-group">
                              <select id='sel_emp' name='sel_emp' class="form-control">
                                <option value=''>Choisir un statut</option>
                              </select>
                            </div> 
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" onclick="EditStatus()">Modifier le statut</button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <script type="text/javascript">
    let odrerId=0;
    $('body').on('click', '.showHistory', function () {
      // console.log('hzelm');
      var id = $(this).attr('data-id');
      if(id){
        var  url = "{{url('order/histories')}}";
             // Empty modal data
             $.ajax({
                  method: "GET",
                 url: url,
                 data: {_token: "{{csrf_token()}}", id: id},
                 dataType: 'json',
                 success: function(response){
                     // Add employee details
                     $('#tblempinfo').html(response.html);
                     // Display Modal
                     $('#ajaxModel').modal('show'); 
                 }
             });
      }
      // console.log(rowId);
      // $('#ajaxModel').modal('show');
      
    });

    $('body').on('click', '.showstatus', function () {
        var id = $(this).attr('data-id');
        var statu_id = $(this).attr('data-uid');

        if(id){
          odrerId = id
          document.getElementById("sel_emp").value = statu_id;
          $('#formModal').modal('show');
        }
    });

    $(document).ready(function(){
      var  url = "{{url('status/fetchStatus')}}";
      // Empty modal data
      $.ajax({
          method: "GET",
          url: url,
          data: {_token: "{{csrf_token()}}"},
          dataType: 'json',
          success: function(response){
              var len = 0;
              if(response != null){
                len = response.length;
              }

              if(len > 0){
                // Read data and create <option >
                for(var i=0; i<len; i++){

                  var id = response[i].id;
                  var name = response[i].status;

                  var option = "<option value='"+id+"'>"+name+"</option>"; 

                  $("#sel_emp").append(option); 
                }
              // $('#formModal').modal('show');
              // $("#status_list").empty().populate(response);
              
          }
        }
      });
    });

    function status_select(id) {
      console.log(document.getElementById('[status_select'+id+']').value);
      swal({
        title: "Modifier statut de la commande", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment modifier cette commande ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
      },function(value){
        if (value){
          var url = "";
          var statut = document.getElementById('[status_select'+id+']').value;
          console.log(statut)
            url = "{{url('order/editStatus')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: id,statut:statut},
                success: function(data){
                  console.log(data)
                  $('#formModal').modal('hide');
                    if(data == "success"){
                        swal({
                            position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Commande est bien modifiée',
                            type: "success",
                            timer: 1500
                        })
                        location.reload();
                    }else{
                      swal({
                            position: 'top-end',
                            title: "erreur", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Vous pouvez pas modifier cette commande',
                            type: "error",
                            timer: 1500
                        })
                    }
                }, 
                error: function(){
                    alert("error");
                }
            })
        }else{
          $('#formModal').modal('hide');
        }
      })
      
      // $("#status_select").change(function(){
      //     });
    };
    
    
    

    
  </script>
@endpush

