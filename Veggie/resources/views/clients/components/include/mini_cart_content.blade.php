<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button class="ltn__utilize-close" aria-label="Đóng giỏ hàng">×</button>
</div>
<div class="mini-cart-product-area ltn__scrollbar">
    @if (count($cartItems))
        @foreach ($cartItems as $item)
            @php($product = $item['product'])
            <div class="mini-cart-item clearfix {{ $item['is_out_of_stock'] ? 'cart-item-out-of-stock' : '' }}">
                <div class="mini-cart-img">
                    <a href="{{ route('product.detail', $product->slug) }}" class="cart-product-image-link"><img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                        @if ($item['is_out_of_stock'])<span class="cart-stock-badge">Hết hàng</span>@endif
                    </a>
                    <button type="button" class="mini-cart-item-delete" data-id="{{ $product->id }}" aria-label="Xóa {{ $product->name }} khỏi giỏ hàng"><i class="icon-cancel"></i></button>
                </div>
                <div class="mini-cart-info">
                    <h6><a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a></h6>
                    <span class="mini-cart-quantity">{{ $item['quantity'] }} × {{ number_format($product->price, 0, '', '.') }} VNĐ</span>
                    @if ($item['stock_issue'] && !$item['is_out_of_stock'])
                        <p class="cart-stock-note">Chỉ còn {{ $item['stock'] }} {{ $product->unit ?: 'sản phẩm' }}.</p>
                        <button type="button" class="btn btn-sm btn-outline-warning cart-adjust-stock" data-id="{{ $product->id }}" data-quantity="{{ $item['stock'] }}">Giảm còn {{ $item['stock'] }}</button>
                    @elseif ($item['is_out_of_stock'])<p class="cart-stock-note">Sản phẩm hiện đã hết hàng.</p>@endif
                </div>
            </div>
        @endforeach
    @else
        <p>Giỏ hàng của bạn đang trống.</p>
    @endif
</div>
<div class="mini-cart-footer">
    @if ($hasStockIssues)
        <p class="cart-stock-warning" role="alert">Xóa sản phẩm hết hàng hoặc điều chỉnh số lượng để thanh toán.</p>
    @endif
    <div class="mini-cart-sub-total"><h5>Tổng tiền: <span>{{ number_format($cartTotal, 0, '', '.') }} VNĐ</span></h5></div>
    <div class="btn-wrapper">
        <a href="{{ route('cart') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a>
        @if ($canCheckout)<a href="{{ route('checkout') }}" class="theme-btn-2 btn btn-effect-2">Thanh toán</a>
        @else<button type="button" class="theme-btn-2 btn btn-effect-2" data-cart-checkout-blocked="1" aria-disabled="true">Thanh toán</button>@endif
    </div>
</div>
