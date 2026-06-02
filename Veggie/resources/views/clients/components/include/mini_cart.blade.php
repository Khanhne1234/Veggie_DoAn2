<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close">×</button>
</div>
<div class="mini-cart-product-area ltn__scrollbar">
    @php
        $subtotal = collect($cartItems)->sum('subtotal');
    @endphp
    @if (!empty($cartItems) && count($cartItems) > 0)
        @foreach ($cartItems as $item)
            @php
                $product = $item['product'];
                $quantity = $item['quantity'];
            @endphp
            <div class="mini-cart-item clearfix">
                <div class="mini-cart-img">
                    <a href="javascript:void(0)">
                        <img src="{{ $product->image_url }}" alt="Image"></a>
                    <span class="mini-cart-item-delete" data-id="{{ $product->id }}">
                        <i class="icon-cancel"></i></span>
                </div>
                <div class="mini-cart-info">
                    <h6><a href="#">{{ $product->name }}</a></h6>
                    <span class="mini-cart-quantity">{{ $quantity }} x
                        {{ number_format($product->price, 0, '', '.') }} VNĐ</span>
                </div>
            </div>
        @endforeach
    @else
        <p>Giỏ hàng của bạn đang trống.</p>
    @endif

</div>
<div class="mini-cart-footer">
    <div class="mini-cart-sub-total">
        <h5>Subtotal: <span>{{ number_format($subtotal, 0, '', '.') }} VNĐ</span></h5>
    </div>
    <div class="btn-wrapper">
        <a href="{{ route('cart') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a>
        <a href="{{ route('checkout') }}" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
    </div>
</div>
