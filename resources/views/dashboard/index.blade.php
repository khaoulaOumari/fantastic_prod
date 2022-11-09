@extends('layouts.app')
@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header content-header{{setting('fixed_header')}}">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{trans('lang.dashboard')}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">{{trans('lang.dashboard')}}</a></li>
                        <li class="breadcrumb-item active">{{trans('lang.dashboard')}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <div class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">

            <div class="col-12">
                <!-- small box -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{$profit}}</h3>

                        <p>{{trans('lang.dashboard_total_earnings')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-medal"></i>
                    </div>
                    <a href="{!! route('orders.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}}
                        <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3>{{$ordersCount}}</h3>

                        <p>{{trans('lang.dashboard_total_orders')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-shopping-bag"></i>
                    </div>
                    <a href="{!! route('orders.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}}
                        <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        @if(setting('currency_right',false) != false)
                            <h3>{{$earning}}{{setting('default_currency')}}</h3>
                        @else
                            <h3>{{$earning}}{{setting('default_currency')}}</h3>
                        @endif

                        <p>{{trans('lang.dashboard_total_earnings')}} <span style="font-size: 11px">({{trans('lang.dashboard_after taxes')}})</span></p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-money"></i>
                    </div>
                    <a href="{!! route('payments.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}}
                        <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{$restaurantsCount}}</h3>
                        <p>{{trans('lang.restaurant_plural')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-home"></i>
                    </div>
                    <a href="{!! route('superettes.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{$drivers}}</h3>
                        <p>{{trans('lang.restaurant_drivers')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-motorcycle"></i>
                    </div>
                    <a href="{!! route('drivers.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{$membersCount}}</h3>

                        <p>{{trans('lang.dashboard_total_clients')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-group"></i>
                    </div>
                    <a href="{!! route('users.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3>{{$categories}}</h3>

                        <p>{{trans('lang.dashboard_total_categories')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-th-large"></i>
                    </div>
                    <a href="{!! route('supcategories.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning" style="background-color: #e83e8c!important;">
                    <div class="inner">
                        <h3>{{$sub_categories}}</h3>

                        <p>{{trans('lang.dashboard_total_sub_categories')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-th-list"></i>
                    </div>
                    <a href="{!! route('categories.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning" style="background-color: #E88D20!important;">
                    <div class="inner">
                        <h3>{{$claims}}</h3>

                        <p>{{trans('lang.dashboard_total_claims')}}</p>
                    </div>
                    <div class="icon">
                        <i class="fa fa-question"></i>
                    </div>
                    <a href="{!! route('claimcustomers.index') !!}" class="small-box-footer">{{trans('lang.dashboard_more_info')}} <i class="fa fa-arrow-circle-right"></i></a>
                </div>
            </div>


        </div>
        <!-- /.row -->

        <div class="row">

        <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <h3 class="card-title">Les 10 supérette avec des comandes </h3>
                        <div class="card-tools">
                            <a href="{{route('restaurants.index')}}" class="btn btn-tool btn-sm"><i class="fa fa-bars"></i> </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-valign-middle" style="max-height: 625px;overflow: auto;height: 625px;">
                            <thead>
                            <tr>
                                <th>{{trans('lang.restaurant_image')}}</th>
                                <th>{{trans('lang.restaurant')}}</th>
                                <th>{{trans('lang.restaurant_address')}}</th>
                                <th>Comanndes</th>
                                <th>{{trans('lang.actions')}}</th>
                               

                            </tr>
                            </thead>
                            <tbody>
                            @foreach($restaurants as $restaurant)
                                <tr>
                                    <td>
                                        {!! getMediaColumn($restaurant, 'image','img-circle img-size-32 mr-2') !!}
                                    </td>
                                    <td>{!! $restaurant->name !!}</td>
                                    <td>
                                        {!! $restaurant->address !!}
                                    </td>
                                    <th>{!! $restaurant->count !!} Commandes</th>
                                    <td class="text-center">
                                        <a href="{!! route('restaurants.edit',$restaurant->id) !!}" class="text-muted"> <i class="fa fa-edit"></i> </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <h3 class="card-title">Les 10 Produits plus commandés</h3>
                        <div class="card-tools">
                            <a href="{{route('topfoods.index')}}" class="btn btn-tool btn-sm"><small>Voir plus</small> </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped table-valign-middle" style="max-height: 625px;overflow: auto;height: 625px;">
                            <thead>
                            <tr>
                                <th>{{trans('lang.restaurant_image')}}</th>
                                <th>{{trans('lang.cart_food_id')}}</th>
                                <th>{{trans('lang.food_order_price')}}</th>
                                <!-- <th>{{trans('lang.food_discount_price')}}</th> -->
                                <th>Comanndes</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($weekProducts as $food)
                                <tr>
                                    <td>
                                        {!! getMediaColumn($food, 'image','img-circle img-size-32 mr-2') !!}
                                    </td>
                                    <td>{!! $food->name !!}</td>
                                    <td>
                                        @if($food->discount_price != 0)
                                        <span style="text-decoration: line-through;">{!! getPrice($food->price) !!}</span><br>{!! getPrice($food->discount_price) !!}</br>
                                        @else
                                        <span >{!! getPrice($food->price) !!}</span>
                                        @endif
                                    </td>
                                    
                                    <th>{!! $food->food_count !!} Commandes</th>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">{{trans('lang.earning_plural')}}</h3>
                            <a href="{!! route('payments.index') !!}">{{trans('lang.dashboard_view_all_payments')}}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                @if(setting('currency_right',false) != false)
                                    <span class="text-bold text-lg">{{$earning}}{{setting('default_currency')}}</span>
                                @else
                                    <span class="text-bold text-lg">{{$earning}}{{setting('default_currency')}}</span>
                                @endif
                                <!-- <span>{{trans('lang.dashboard_earning_over_time')}}</span> -->
                            </p>
                            <p class="ml-auto d-flex flex-column text-right">
                                <span class="text-success"> {{$ordersCount}}</span></span>
                                <span class="text-muted">{{trans('lang.dashboard_total_orders')}}</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="sales-chart" height="200"></canvas>
                        </div>

                        <div class="d-flex flex-row justify-content-end">
                            <span class="mr-2"> <i class="fa fa-square text-primary"></i> {{trans('lang.dashboard_this_year')}} </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Les Catégories les plus vendues</h3>
                            <a href="{!! route('categories.index') !!}">Tous les sous catégories</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <canvas id="categories-chart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Statistique des commandes</h3>
                            <a href="{!! route('orders.index') !!}">Tous les commandes</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <canvas id="orders-chart" height="120"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header no-border">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Statistique des livreurs (Commandes Livrées)</h3>
                            <a href="{!! route('drivers.index') !!}">Tous mes livreurs</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="position-relative mb-4">
                            <canvas id="drivers-chart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header no-border">
                        <h3 class="card-title">Localisation des commandes</h3>
                    </div>
                    <div class="card-body p-0">
                    <div id="map" style="height: 600px;"></div>

                    <div></div>
                </div>
            </div>

            
        </div>
    </div>

@endsection
@push('scripts_lib')
    <script src="{{asset('plugins/chart.js/Chart.min.js')}}"></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&callback" ></script>

@endpush
@push('scripts')

    <script type="text/javascript">
        var data = [1000, 2000, 3000, 2500, 2700, 2500, 3000];
        var labels = ['JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];

        function renderChart(chartNode, data, labels) {
            var ticksStyle = {
                fontColor: '#495057',
                fontStyle: 'bold'
            };

            var mode = 'index';
            var intersect = true;
            return new Chart(chartNode, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            backgroundColor: '#007bff',
                            borderColor: '#007bff',
                            data: data
                        }
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        mode: mode,
                        intersect: intersect
                    },
                    hover: {
                        mode: mode,
                        intersect: intersect
                    },
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            // display: false,
                            gridLines: {
                                display: true,
                                lineWidth: '4px',
                                color: 'rgba(0, 0, 0, .2)',
                                zeroLineColor: 'transparent'
                            },
                            ticks: $.extend({
                                beginAtZero: true,

                                // Include a dollar sign in the ticks
                                callback: function (value, index, values) {
                                    @if(setting('currency_right', '0') == '0')
                                        return "{{setting('default_currency')}} "+value;
                                    @else
                                        return value+" {{setting('default_currency')}}";
                                        @endif

                                }
                            }, ticksStyle)
                        }],
                        xAxes: [{
                            display: true,
                            gridLines: {
                                display: false
                            },
                            ticks: ticksStyle
                        }]
                    }
                }
            })
        }

        $(function () {
            'use strict'

            var $salesChart = $('#sales-chart')
            $.ajax({
                url: "{!! $ajaxEarningUrl !!}",
                success: function (result) {
                    $("#loadingMessage").html("");
                    var data = result.data[0];
                    var labels = result.data[1];
                    renderChart($salesChart, data, labels)
                },
                error: function (err) {
                    $("#loadingMessage").html("Error");
                }
            });
            //var salesChart = renderChart($salesChart, data, labels);
        })

        $(function () {
            'use strict'
            var ctx = document.getElementById("categories-chart").getContext('2d');
            var clrs = ['#BE7A0E','#0EBCB3','#BC0E9D','#0BAC47','#180BAC','#AC0B2F','#EBE127'];
            $.ajax({
                method: "GET",
                url: "{{url('ajaxCatgeories')}}",
                success: function (result) {
                    // $("#loadingMessage").html("");
                    if(result){
                        var data = result.data;
                        var labels = result.labels;
                        var colors = [];
                        for(var i = 0; i < labels.length; i++) {
                            // colors.push(clrs[Math.floor(Math.random()*clrs.length)])
                            if(!colors.includes(clrs[Math.floor(Math.random()*clrs.length)])){
                                colors.push(clrs[Math.floor(Math.random()*clrs.length)])
                            }
                        }
                        var myChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels:labels,
                                datasets: [{
                                    label: 'Catégories les plus vendus',
                                    data: data,
                                    borderWidth: 1,
                                    backgroundColor:colors
                                }]
                            },
                            options: {
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero:true
                                        }
                                    }]
                                }
                            }
                        });
                    }
                },
                error: function (err) {
                    // $("#loadingMessage").html("Error");
                }
            });
        })

        $(function () {
            'use strict'
            var ctx = document.getElementById("orders-chart").getContext('2d');
            $.ajax({
                method: "GET",
                url: "{{url('ajaxOrders')}}",
                success: function (result) {
                    // $("#loadingMessage").html("");
                    if(result){
                        var data = result.data;
                        var labels = [ "Jan","Fév","Mar","Avr","Mai","Juin","Juil","Aout","Sept","Oct","Nov","Déc"];
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels:labels,
                                datasets: [{
                                    label: 'Statistique des commandes',
                                    data: data,
                                    fill: true,
                                    backgroundColor:'#EB27D1'
                                }]
                            },
                            // options: {
                            //     scales: {
                            //         yAxes: [{
                            //             ticks: {
                            //                 beginAtZero:true
                            //             }
                            //         }]
                            //     }
                            // }
                        });
                    }
                },
                error: function (err) {
                    // $("#loadingMessage").html("Error");
                }
            });
        })

        $(function () {
            'use strict'
            var ctx = document.getElementById("drivers-chart").getContext('2d');
            var clrs = ['#BE7A0E','#0EBCB3','#BC0E9D','#0BAC47','#180BAC','#AC0B2F','#EBE127'];
            $.ajax({
                method: "GET",
                url: "{{url('ajaxDrivers')}}",
                success: function (result) {
                    // $("#loadingMessage").html("");
                    if(result){
                        var data = result.data;
                        var labels = result.labels;
                        var colors = [];
                        for(var i = 0; i < labels.length; i++) {
                            if(!colors.includes(clrs[Math.floor(Math.random()*clrs.length)])){
                                colors.push(clrs[Math.floor(Math.random()*clrs.length)])
                            }
                            
                        }
                        var myChart = new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels:labels,
                                datasets: [{
                                    label: 'Commandes livré par chaque livreur',
                                    data: data,
                                    backgroundColor:colors,
                                    hoverOffset: 4
                                }]
                            },
                           
                        });
                    }
                },
                error: function (err) {
                    // $("#loadingMessage").html("Error");
                }
            });
        })
        
        
        function initMap() {
            // var locations =[];
            url = "{{url('google_map_orders')}}";
            $.ajax({
                method: "GET",
                url: url,
                data: {_token: "{{csrf_token()}}"},
                success: function(data){
                    const myLatLng = { lat: 30.427755, lng: -9.598107 };
                    const map = new google.maps.Map(document.getElementById("map"), {
                        zoom: 12,
                        center: myLatLng,
                    });
                    var locations = data

                    var infowindow = new google.maps.InfoWindow();
  
                    var marker, i;
                    
                    for (i = 0; i < locations.length; i++) {  
                        if(locations[i][3] == 5){
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                // icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                                icon :'{{ env("APP_URL") }}storage/app/public/codeQR/cart.png'
                            });  
                        }else if(locations[i][3] == 6){
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                            });
                        }else{
                            marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                map: map,
                                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                            });
                        }
                            
                        google.maps.event.addListener(marker, 'click', (function(marker, i) {
                            return function() {
                            infowindow.setContent(locations[i][0]);
                            infowindow.open(map, marker);
                            }
                        })(marker, i));
        
                    }

                }, 
                error: function(){
                    alert("error");
                }
            })
  
            
        }
  
        initMap()
 
  
      
  
    </script>

@endpush