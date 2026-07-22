@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Hóa đơn</h3>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Thông tin hóa đơn</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <section class="content invoice">
                                <!-- title row -->
                                <div class="row">
                                    <div class="  invoice-header">
                                        <h1>
                                            <i class="fa fa-globe"></i> Hóa đơn.
                                            <small class="pull-right">Ngày tạo: {{ $order->created_at }}</small>
                                        </h1>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- info row -->
                                <div class="row invoice-info">
                                    <div class="col-sm-4 invoice-col">
                                        Từ
                                        <address>
                                            <strong>{{ $order->shippingAddress->full_name }}</strong>
                                            <br>{{ $order->shippingAddress->address }}
                                            <br>Email: {{ $order->user->email }}
                                            <br>Số điện thoại: {{ $order->shippingAddress->phone }}
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        Đến
                                        <address>
                                            <strong>Veggie Store</strong>
                                            <br>Cà mau, Việt Nam
                                            <br>Số điện thoại: 0312345678
                                            <br>Email: mathghack@gmail.com
                                        </address>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-sm-4 invoice-col">
                                        <b>Order ID: {{ $order->id }}</b>
                                        <br>
                                        <b>Email: {{ $order->user->email }}</b>
                                        <br>
                                        <b>Account: {{ $order->user->name }}</b>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- Table row -->
                                <div class="row">
                                    <div class="  table">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Ảnh</th>
                                                    <th>Sản phẩm</th>
                                                    <th>Giá</th>
                                                    <th>Số lượng</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderItems as $item)
                                                    <tr>
                                                        <td>
                                                            @if ($item->product->image_url)
                                                                <img src="{{ $item->product->image_url }}"
                                                                    alt="{{ $item->product->name }}" class="image-product"
                                                                    style="width: 100px;">
                                                            @else
                                                                <span>Không có ảnh</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $item->product->name }}</td>
                                                        <td>{{ number_format($item->product->price, 0, ',', '.') }}VNĐ
                                                        </td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}VNĐ
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <div class="row">
                                    <!-- accepted payments column -->
                                    <div class="col-md-6">
                                        <p class="lead">Phương thức thanh toán:</p>
                                        @if ($order->payment && $order->payment->payment_method == 'paypal')
                                            <img src="{{ asset('assets/admin/images/paypal.png') }}" alt="Paypal"
                                                style="max-height: 80px; max-width: 200px;">
                                        @else
                                            <img src="{{ asset('assets/admin/images/Delivery.jpg') }}"
                                                alt="Thanh toán tiền mặt (COD)"
                                                style="max-height: 80px; max-width: 200px; object-fit: contain;">
                                        @endif
                                        <p class="text-muted well well-sm no-shadow" style="margin-top: 10px;">
                                            Đây là phương thức thanh toán khách hàng đã chọn cho đơn hàng này. Nếu là
                                            PayPal thì đơn hàng sẽ được xử lý thông qua cổng thanh toán PayPal. Nếu là tiền
                                            mặt thì đơn hàng sẽ được xử lý thông qua COD. Giao hàng tận nơi.
                                        </p>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-md-6">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th style="width:50%">Tiền hàng:</th>
                                                        <td>{{ number_format($order->total_price - 25000, 0, ',', '.') }}VNĐ
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Phí giao hàng</th>
                                                        <td>{{ number_format(25000, 0, ',', '.') }}VNĐ</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Tổng tiền</th>
                                                        <td>{{ number_format($order->total_price, 0, ',', '.') }}VNĐ</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                </div>
                                <!-- /.row -->

                                <!-- this row will not appear when printing -->
                                <div class="row no-print">
                                    <div class=" ">
                                        @if ($order->status != 'canceled')

                                            <button class="btn btn-default" onclick="window.print();"><i
                                                    class="fa fa-print"></i>In hóa đơn</button>
                                            <button class="btn btn-success pull-right send-invoice-mail"
                                                data-id="{{ $order->id }}"><i class="fa fa-send"></i>Gửi
                                                hóa đơn</button>
                                            @if ($order->status == 'pending')
                                                <button class="btn btn-danger pull-right cancel-order" data-id="{{ $order->id }}"><i
                                                        class="fa fa-remove"></i>Hủy đơn hàng</button>
                                            @endif
                                        @else
                                            <button class="btn btn-danger pull-right" style="cursor: not-allowed;"><i
                                                    class="fa fa-download"></i>Đơn hàng đã hủy</button>
                                        @endif
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
