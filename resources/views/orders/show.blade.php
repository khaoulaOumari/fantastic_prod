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
          <li class="breadcrumb-item active">{{trans('lang.order')}}</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<div class="content">
  <div class="card">
    <div class="card-header d-print-none">
      <ul class="nav nav-tabs align-items-end card-header-tabs w-100">
        <li class="nav-item">
          <a class="nav-link" href="{!! route('orders.index') !!}"><i class="fa fa-list mr-2"></i>{{trans('lang.order_table')}}</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{!! url()->current() !!}"><i class="fa fa-plus mr-2"></i>{{trans('lang.order')}}</a>
        </li>
        <div class="ml-auto d-inline-flex">
          <li class="nav-item">
            <a class="nav-link pt-1" id="printOrder" href="#"><i class="fa fa-print"></i> {{trans('lang.print')}}</a>
          </li>
        </div>
      </ul>
    </div>
    <div class="card-body">

    @if($order['qrcode'])
    <div style="float: right;">
      {!! QrCode::size(200)->generate($order['qrcode']) !!}<br/>
      <h3>{!! $order->qrcode !!}</h3>
    </div>
    @endif
    
      <div class="row">
        @include('orders.show_fields')
      </div>
      @include('food_orders.table')
      <div class="row">
      <div class="col-5 offset-7">
        <div class="table-responsive table-light">
          <table class="table">
            <tbody>
            <tr>
              <th class="text-right">{{trans('lang.order_subtotal')}}</th>
              <td>{!! getPrice($subtotal) !!}</td>
            </tr>
            <tr>
              <th class="text-right">{{trans('lang.order_delivery_fee')}}</th>
              <td>{!! getPrice($order['delivery_fee'])!!}</td>
            </tr>
            <tr>
              <th class="text-right">{{trans('lang.order_total')}}</th>
              <td>{!!getPrice($total)!!}</td>
            </tr>
            @if($order->coupon)
            <tr>
              <th class="text-right">Code Coupon :</th>
              @if($order->coupon['discount_type'] == 'fixed')
                <td>{!! $order->coupon['code'] !!} ( {!! getPrice($order->coupon['discount']) !!} )</td>
              @elseif($order->coupon['discount_type'] == 'percent')
              <td>{!! $order->coupon['code'] !!} ( {!! $order->coupon['discount'] !!} % )</td>
              @endif
            </tr>
            @endif
            </tbody></table>
        </div>
      </div>
      <div style="width: 100%;margin-top: 5%;">
      <h5>Les produits personnalis??s</h5>
        @csrf
        <table class="table table-bordered data-table">
            <thead>
                <tr>
                    <th>Nom Produit</th>
                    <!-- <th>Produit Existant</th> -->
                    <th>Description</th>
                    <th>Quantite</th>
                    <!-- <th>Prix</th> -->
                </tr>
            </thead>
            <tbody>
                @foreach($customorders as $row)
                    <tr>
                        <td>
                            <h4>{{ $row->name }}</h4>
                        </td>
                        <td>
                            {{ $row->description }}
                        </td>
                        <td>
                            <h6>{{ $row->quantite }}</h6>
                        </td>
                        

                    </tr>
                @endforeach
            </tbody>
        </table>
      </div>

      </div>
      <div class="clearfix"></div>
      <div class="row d-print-none">
        <!-- Back Field -->
        <div class="form-group col-12 text-right">
          <a href="{!! route('orders.index') !!}" class="btn btn-default"><i class="fa fa-undo"></i> {{trans('lang.back')}}</a>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
</div>



@endsection

@push('scripts')
  <script type="text/javascript">
    $("#printOrder").on("click",function () {
      window.print();
    });

    $('body').on('click', '.editProduct', function () {
      $('#ajaxModel').modal('show');
      
    });
    
  </script>
@endpush
