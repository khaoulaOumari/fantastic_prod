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
  function activeOrder(row){

    swal({
        title: "Modifier une commande", 
        confirmButtonText: "{{trans('lang.ok')}}",
        cancelButtonText: 'Annuler',
        text: 'Vous voulez vraiment modifier cette commande ?',
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-success",
        cancelButtonClass: "btn-danger"
        },function(value) {
        if (value) {
            var url = "";
            url = "{{url('order/activeOrder')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", id: row},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            title: "succès", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Commande est bien modifiée',
                            type: "success",
                            timer: 1500
                        })
                    }else{
                        document.getElementById('active['+row+']').checked = !document.getElementById('active['+row+']').checked;
                        swal({
                            position: 'top-end',
                            title: "erreur", 
                            confirmButtonText: "{{trans('lang.ok')}}",
                            text: 'Vous pouvez pas modifier cette commande',
                            type: "error",
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