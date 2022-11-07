@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}
@endpush

<link href="{{ asset('css/checkbox.css') }}" rel="stylesheet">
<script>
  function activeSup(row){
    swal({
        title: "Modifier", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment modifier cette supérette ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
        },function(value) {
        if (value) {
            var url = "";
            url = "{{url('restaurant/editActive')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", supId: row},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Supérette bien modifié',
                            type: "success",
                            timer: 1500
                            })
                    }
                    // alert("Produit bien modifié");
                    // location.reload();

                }, 
                error: function(){
                    alert("error");
                }
            })
        }else{
            document.getElementById('active['+row+']').checked = !document.getElementById('active['+row+']').checked;
        }
    });
  }


    function closeSup(row){
        swal({
            title: "Modifier", 
            confirmButtonText: "{{trans('lang.ok')}}",
            cancelButtonText: 'Annuler',
            text: 'Vous voulez vraiment modifier cette supérette ?',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            cancelButtonClass: "btn-danger"
            },function(value) {
            if (value) {
                var url = "";
                url = "{{url('restaurant/editClose')}}";
                $.ajax({
                    method: "POST",
                    url: url,
                    data: {_token: "{{csrf_token()}}", supId: row},
                    success: function(data){
                        if(data=="success"){
                            swal({
                                position: 'top-end',
                                title: "succès", 
                                confirmButtonText: "{{trans('lang.ok')}}",
                                text: 'Supérette bien modifié',
                                type: "success",
                                timer: 1500
                                })
                        }
                        // alert("Produit bien modifié");
                        // location.reload();

                    }, 
                    error: function(){
                        alert("error");
                    }
                })
            }else{
                document.getElementById('active['+row+']').checked = !document.getElementById('active['+row+']').checked;
            }
        });
    }

    function deliverySup(row){
        swal({
            title: "Modifier", 
            confirmButtonText: "{{trans('lang.ok')}}",
            cancelButtonText: 'Annuler',
            text: 'Vous voulez vraiment modifier cette supérette ?',
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-success",
            cancelButtonClass: "btn-danger"
            },function(value) {
            if (value) {
                var url = "";
                url = "{{url('restaurant/editDelivery')}}";
                $.ajax({
                    method: "POST",
                    url: url,
                    data: {_token: "{{csrf_token()}}", supId: row},
                    success: function(data){
                        if(data=="success"){
                            swal({
                                position: 'top-end',
                                title: "succès", 
                                confirmButtonText: "{{trans('lang.ok')}}",
                                text: 'Supérette bien modifié',
                                type: "success",
                                timer: 1500
                                })
                        }
                        // alert("Produit bien modifié");
                        // location.reload();

                    }, 
                    error: function(){
                        alert("error");
                    }
                })
            }else{
                document.getElementById('active['+row+']').checked = !document.getElementById('active['+row+']').checked;
            }
        });
    }
</script>