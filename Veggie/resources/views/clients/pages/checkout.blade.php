@extends('layouts.client')
@section('title', 'Đặt hàng')
@section('breadcrumb', 'Đặt hàng')

@section('content')

    <div class="ltn__checkout-area mb-105">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ltn__checkout-inner">
                        <div class="ltn__checkout-single-content mt-50">
                            <h4 class="title-2">Chi tiết thanh toán</h4>
                            <div class="select-address mb-30 p-4" style="border: 1px solid #e8eef5; border-radius: 8px;">
                                <div class="row align-items-end g-3">
                                    <div class="col-lg-7">
                                        <h6 class="mb-2">Chọn địa chỉ khác</h6>
                                        <select name="address_id" id="list_address" class="input-item w-100">
                                            @forelse ($addresses as $address)
                                                <option value="{{ $address->id }}"
                                                    {{ $address->default ? 'selected' : '' }}>
                                                    {{ $address->full_name }} - {{ $address->city }}
                                                </option>
                                            @empty
                                                <option value="">Chưa có địa chỉ giao hàng</option>
                                            @endforelse
                                        </select>
                                    </div>
                                    <div class="col-lg-5 text-lg-end">
                                        <a href="{{ route('account') }}"
                                            class="btn theme-btn-1 btn-effect-1 text-uppercase px-4 py-2"
                                            style="width: fit-content; min-width: 180px;">
                                            Thêm địa chỉ mới
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="ltn__checkout-single-content-info p-4"
                                style="border: 1px solid #e8eef5; border-radius: 8px;">
                                <h6 class="mb-4">Thông tin cá nhân</h6>
                                <div class="row g-4">
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Họ và tên</h6>
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" name="full_name" placeholder="Họ và tên"
                                                value="{{ old('full_name', $defaultAddress->full_name ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Số điện thoại</h6>
                                        <div class="input-item input-item-name ltn__custom-icon">
                                            <input type="text" name="phone" placeholder="Số điện thoại"
                                                value="{{ old('phone', $defaultAddress->phone ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Địa chỉ</h6>
                                        <div class="input-item">
                                            <input type="text" name="address" placeholder="Số nhà và tên đường"
                                                value="{{ old('address', $defaultAddress->address ?? '') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <h6>Thành phố</h6>
                                        <div class="input-item">
                                            <input type="text" name="city" placeholder="Thành phố"
                                                value="{{ old('city', $defaultAddress->city ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ltn__checkout-payment-method mt-50">
                        <h4 class="title-2">Phương thức thanh toán</h4>
                        <form action="{{ route('checkout.placeOrder') }}" method="POST">
                            @csrf
                            <input type="hidden" name="address_id" value="{{ $defaultAddress->id ?? '' }}">
                            <div id="checkout_patment">
                                <div class="card">
                                    <h5 class="ltn__card-title">
                                        <input type="radio" name="payment_method" value="cash" id="payment_cod">
                                        <label for="payment_cod">
                                            Thanh toán khi nhận hàng <img
                                                src="{{ asset('assets/clients/img/icons/cash.png') }}"">
                                        </label>
                                    </h5>
                                </div>
                                <div class="card">
                                    <h5 class="collapsed ltn__card-title">
                                        <input type="radio" name="payment_method" value="paypal" id="payment_paypal">
                                        <label for="payment_paypal">
                                            PayPal <img src="{{ asset('assets/clients/img/icons/payment-3.png') }}"
                                                alt="#">
                                        </label>
                                    </h5>
                                </div>
                            </div>
                            <div id="payment-method-warning" class="text-danger mt-2 d-none">
                                Vui lòng chọn phương thức thanh toán.
                            </div>
                            <div class="ltn__payment-note mt-30 mb-30">
                                <p>Dữ liệu cá nhân của bạn sẽ được sử dụng để xử lý đơn hàng, hỗ trợ trải nghiệm của bạn
                                    trên
                                    website này và cho các mục đích khác được mô tả trong chính sách bảo mật của chúng tôi.
                                </p>
                            </div>
                            <button class="btn theme-btn-1 btn-effect-1 text-uppercase" type="submit"
                                id="order_button_cash" disabled>Đặt hàng</button>
                            <div id="paypal-button-container"></div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="shoping-cart-total mt-50">
                        <h4 class="title-2">Tổng sản phẩm</h4>
                        <table class="table">
                            <tbody>
                                @foreach ($cartItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }} <strong>× {{ $item->quantity }}</strong></td>
                                        <td>{{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}VNĐ
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td>Phí vận chuyển</td>
                                    <td>{{ number_format(15000, 0, ',', '.') }}VNĐ</td>
                                </tr>
                                <tr>
                                    <td><strong>Tổng tiền</strong></td>
                                    <td><strong class="totalPrice_Checkout"
                                            data-total-vnd="{{ $totalPrice + 15000 }}">{{ number_format($totalPrice + 15000, 0, ',', '.') }}VNĐ</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
