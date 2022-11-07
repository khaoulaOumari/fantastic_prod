    <div style="width: 100%;margin-top: 5%;">
    <h3>Historique du stock</h3>
        @csrf
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Nom Produit</th>
                    <th>Supèrette</th>
                    <th>Quantite</th>
                    <th>Action</th>
                    <th>Action par</th>
                    <th>Date</th>


                </tr>
            </thead>
            <tbody>
                @foreach($histories as $row)
                    <tr>
                        <td> {{$row->food_name}}</td>
                        <td> {{$row->restaurant_name}}</td> 
                        <td> {{$row->quantity}}</td>
                        <td> {{$row->task}}</td>
                        <td> {{$row->user->name}}</td>
                        <td> {{$row->created_at}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>