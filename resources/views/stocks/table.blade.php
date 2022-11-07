@push('css_lib')
@include('layouts.datatables_css')
@endpush

{!! $dataTable->table(['width' => '100%']) !!}

@push('scripts_lib')
@include('layouts.datatables_js')
{!! $dataTable->scripts() !!}


<script>
  function EditStock(id,qty){
    console.log(id)
    // var id="initial_qty"+id;
    var new_qty='';
    new_qty = document.getElementById('initial_qty['+id+']').value;
    // console.log(new_qty)
    var url = "";
        url = "{{url('stocks/EditStock')}}";
        $.ajax({
            method: "POST",
            url: url,
            data: {_token: "{{csrf_token()}}", stockId: id,qty:new_qty},
            success: function(data){
                alert("Produit bien modifié");
                // location.reload();

            }, 
            error: function(){
                  alert("error ");
            }
        })

  }
</script>

@endpush