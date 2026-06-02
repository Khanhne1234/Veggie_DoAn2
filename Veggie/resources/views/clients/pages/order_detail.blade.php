@extends('layouts.client')
@section('title', 'Chi tiết đơn hàng')
@section('breadcrumb', 'Chi tiết đơn hàng')

@section('content')
    <section class="liton_shoping-cart-area mb-120 order-detail-page">
        <div class="container mt-4">
            <div class="order-detail-card">
                <div class="order-detail-header">
                    <h3>Chi tiết đơn hàng #{{ $order->id }}</h3>
                    <p class="order-detail-date">Ngày đặt: {{ $order->created_at->format('d/m/Y') }}</p>
                </div>

                <div class="order-summary-grid">
                    <div class="order-summary-item">
                        <span class="summary-label">Trạng thái</span>
                        <div class="summary-value">
                            @if ($order->status == 'pending')
                                <span class="badge bg-warning">Đang chờ xử lý</span>
                            @elseif ($order->status == 'processing')
                                <span class="badge bg-info">Đang xử lý</span>
                            @elseif ($order->status == 'completed')
                                <span class="badge bg-success">Hoàn thành</span>
                            @elseif ($order->status == 'cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="order-summary-item">
                        <span class="summary-label">Thanh toán</span>
                        <div class="summary-value">
                            @if ($order->payment && $order->payment->payment_method == 'cod')
                                <span class="badge bg-primary">Thanh toán khi nhận hàng (COD)</span>
                            @elseif ($order->payment && $order->payment->payment_method == 'paypal')
                                <span class="badge bg-primary">Thanh toán qua PayPal</span>
                            @else
                                <span class="badge bg-secondary">Không xác định</span>
                            @endif
                        </div>
                    </div>

                    <div class="order-summary-item total">
                        <span class="summary-label">Tổng tiền</span>
                        <div class="summary-value total-price">{{ number_format($order->total_price, 0, ',', '.') }} VNĐ
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-detail-card mt-4">
                <div class="order-section-head">
                    <h4>Sản phẩm trong đơn hàng</h4>
                </div>
                <div class="table-responsive">
                    <table class="table order-items-table">
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
                                        <img class="order-item-thumb"
                                            src="{{ $item->product?->image_url ?? asset('storage/uploads/products/default_product.webp') }}"
                                            alt="{{ $item->product?->name ?? 'Sản phẩm' }}">
                                    </td>
                                    <td class="order-item-name">{{ $item->product->name }}</td>
                                    <td>{{ number_format($item->product->price, 0, ',', '.') }} VNĐ</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->price * $item->quantity, 0, ',', '.') }} VNĐ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="order-detail-card mt-4">
                <div class="order-section-head">
                    <h4>Thông tin giao hàng</h4>
                </div>
                <div class="shipping-grid">
                    <div class="shipping-item"><span>Người nhận:</span> {{ $order->shippingAddress->full_name }}</div>
                    <div class="shipping-item"><span>Số điện thoại:</span> {{ $order->shippingAddress->phone }}</div>
                    <div class="shipping-item shipping-full"><span>Địa chỉ:</span> {{ $order->shippingAddress->address }}
                    </div>
                    <div class="shipping-item"><span>Thành phố:</span> {{ $order->shippingAddress->city }}</div>
                </div>

                @if ($order->status == 'pending')
                    <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="cancel-order-form"
                        onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?');">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-danger mt-3">Hủy đơn hàng</button>
                    </form>
                @endif
            </div>

            @if ($order->status == 'completed')
                <div class="order-detail-card mt-4">
                    <div class="order-section-head">
                        <h4>Đánh giá sản phẩm</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table review-table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>
                                            <a href="{{ route('product.detail', $item->product->slug) }}"
                                                class="btn theme-btn-1 btn-effect-1 review-btn">Đánh giá</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
