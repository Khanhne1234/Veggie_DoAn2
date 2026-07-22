@extends('layouts.admin')

@section('title', 'Danh sách đơn hàng')

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Quản lí đơn hàng <small>Danh sách tât cả đơn hàng</small></h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 ">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Danh sách đơn hàng<small></small></h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                </li>
                                <li><a class="close-link"><i class="fa fa-close"></i></a>
                                </li>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <div class="row">

                                <div class="col-md-12 col-sm-12 ">
                                    <div class="x_panel">
                                        <div class="x_content">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="card-box table-responsive">
                                                        <p class="text-muted font-13 m-b-30">
                                                            Trang quản lí đơn hàng cho phép admin theo dõi và quản lí các
                                                            đơn hàng.
                                                            Các đơn hàng được tổ chức theo từng trạng thái, giúp khách hàng
                                                            dễ dàng theo dõi và lựa chọn.
                                                            Dữ liệu được hiển thị dưới dang bảng, hỗ trợ tìm kiếm, sắp xếp
                                                            và thao tác nhanh.
                                                        </p>
                                                        <table id="datatable-buttons"
                                                            class="table table-striped table-bordered"
                                                            style="width:100%; text-align: center;">
                                                            <thead>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <th>Tài khoản</th>
                                                                    <th>Thông tin người mua</th>
                                                                    <th>Tổng tiền</th>
                                                                    <th>Trạng thái đơn hàng</th>
                                                                    <th>Trạng thái thanh toán</th>
                                                                    <th>Chi tiết đơn hàng</th>
                                                                    <th>Hành động</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($order as $item)
                                                                    <tr>
                                                                        <td>{{ $item->id }}</td>
                                                                        <td>{{ $item->user->name ?? 'N/A' }}</td>
                                                                        <td>
                                                                            <a href="javascript:void(0)" data-toggle="modal"
                                                                                data-target="#addressShippingModal-{{ $item->id }}">{{ $item->shippingAddress->address ?? 'N/A' }}</a>
                                                                        </td>
                                                                        <td>{{ number_format($item->total_price, 0, ',', '.') }}VNĐ
                                                                        </td>
                                                                        <td class="status-order">
                                                                            @if ($item->status == 'pending')
                                                                                <span
                                                                                    class=" custom-badge badge badge-warning">Chờ
                                                                                    xử lí</span>
                                                                            @elseif($item->status == 'processing')
                                                                                <span
                                                                                    class=" custom-badge badge badge-info">Đang
                                                                                    vận chuyển</span>
                                                                            @elseif($item->status == 'completed')
                                                                                <span

                                                                                    class=" custom-badge badge badge-success">Hoàn
                                                                                    thành</span>
                                                                            @elseif($item->status == 'canceled' || $item->status == 'cancelled')
                                                                                <span
                                                                                    class=" custom-badge badge badge-danger">Đã
                                                                                    hủy</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @if ($item->status == 'completed' || ($item->payment && $item->payment->status == 'completed'))
                                                                                <span
                                                                                    class=" custom-badge badge badge-success">Đã
                                                                                    thanh toán</span>
                                                                            @else
                                                                                <span
                                                                                    class=" custom-badge badge badge-warning">Chờ
                                                                                    thanh toán</span>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            <a href="javascript:void(0)"
                                                                                class="btn btn-info" data-toggle="modal"
                                                                                data-target="#orderItemsModal-{{ $item->id }}">
                                                                                <i class="fa fa-list"></i> Xem
                                                                            </a>
                                                                        </td>
                                                                        <td>

                                                                            <div class="btn-group">
                                                                                <button type="button"
                                                                                    class="btn btn-danger dropdown-toggle dropdown-toggle-split"
                                                                                    data-toggle="dropdown"
                                                                                    aria-haspopup="true"
                                                                                    aria-expanded="false">
                                                                                </button>
                                                                                <div class="dropdown-menu">
                                                                                    @if ($item->status == 'pending')
                                                                                        <a class="dropdown-item confirm-order"
                                                                                            href="javascript:void(0)"
                                                                                            data-id="{{ $item->id }}">Xác
                                                                                            nhận</a>
                                                                                    @endif
                                                                                    <a class="dropdown-item" target="_blank"
                                                                                        href="{{ route('admin.order_detail', ['id' => $item->id]) }}">Xem
                                                                                        chi tiết</a>
                                                                                </div>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                        @foreach ($order as $item)
                                                            {{-- Modal Address --}}
                                                            <div class="modal fade"
                                                                id="addressShippingModal-{{ $item->id }}" tabindex="-1"
                                                                tabindex="-1" aria-labelledby="addressShippingModalLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="addressShippingModalLabel">
                                                                                Thông tin giao hàng
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <p>
                                                                                Người nhận:
                                                                                {{ $item->shippingAddress->full_name }}
                                                                            </p>
                                                                            <p>
                                                                                Địa chỉ:
                                                                                {{ $item->shippingAddress->address }}
                                                                            </p>
                                                                            <p>
                                                                                Thành phố:
                                                                                {{ $item->shippingAddress->city }}
                                                                            </p>
                                                                            <p>
                                                                                Số điện thoại:
                                                                                {{ $item->shippingAddress->phone }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Modal Items-->
                                                            <div class="modal fade"
                                                                id="orderItemsModal-{{ $item->id }}" tabindex="-1"
                                                                tabindex="-1" aria-labelledby="orderItemsModalLabel"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title"
                                                                                id="orderItemsModalLabel">
                                                                                Chi tiết hóa đơn
                                                                            </h5>
                                                                            <button type="button" class="btn-close"
                                                                                data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <table class="table table-bordered">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>STT</th>
                                                                                        <th>Tên sản phẩm</th>
                                                                                        <th>Số lượng</th>
                                                                                        <th>Đơn giá</th>
                                                                                        <th>Thành tiền</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    @php $index = 1; @endphp
                                                                                    @foreach ($item->orderItems as $orderItem)
                                                                                        <tr>
                                                                                            <td>{{ $index++ }}</td>
                                                                                            <td>{{ $orderItem->product->name ?? 'N/A' }}
                                                                                            </td>
                                                                                            <td>{{ $orderItem->quantity }}
                                                                                            </td>
                                                                                            <td>{{ number_format($orderItem->price, 0, ',', '.') }}VNĐ
                                                                                            </td>
                                                                                            <td>{{ number_format($orderItem->quantity * $orderItem->price, 0, ',', '.') }}VNĐ
                                                                                            </td>
                                                                                        </tr>
                                                                                    @endforeach
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
