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
        title: 'Vous voulez vraiment modifier cette supérette?',
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
            url = "{{url('restaurant/editActive')}}";
            $.ajax({
                method: "POST",
                url: url,
                data: {_token: "{{csrf_token()}}", supId: row},
                success: function(data){
                    if(data=="success"){
                        swal({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Supérette bien modifiée',
                            showConfirmButton: false,
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