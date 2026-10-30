<div class="ltn__utilize-menu-head">
    <span class="ltn__utilize-menu-title">Giỏ hàng</span>
    <button type="button" class="ltn__utilize-close" aria-label="Đóng giỏ hàng">×</button>
</div>
<div class="mini-cart-product-area ltn__scrollbar">
    @forelse ($cartItems as $item)
        @php
            $product = $item['product'];
            $outOfStock = $item['is_out_of_stock'];
            $unit = trim($product->unit ?? '') ?: 'sản phẩm';
        @endphp
        <div data-product-id="{{ $product->id }}" @class(['mini-cart-item', 'clearfix', 'cart-item-out-of-stock' => $outOfStock])>
            <div class="mini-cart-img">
                <a href="{{ route('product.detail', $product->slug) }}" class="cart-product-thumbnail">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                    @if ($outOfStock)
                        <span class="cart-stock-badge">Hết hàng</span>
                    @endif
                </a>
                <button type="button" class="mini-cart-item-delete" data-id="{{ $product->id }}"
                    aria-label="Xóa {{ $product->name }} khỏi giỏ hàng"><i class="icon-cancel" aria-hidden="true"></i></button>
            </div>
            <div class="mini-cart-info">
                <h6><a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a></h6>
                <span class="mini-cart-quantity">{{ $item['quantity'] }} × {{ number_format($product->price, 0, ',', '.') }} VNĐ</span>
                @if ($item['stock_issue'] && !$outOfStock)
                    <p class="cart-stock-note">Chỉ còn {{ $item['stock'] }} {{ $unit }}</p>
                    <button type="button" class="cart-adjust-stock" data-id="{{ $product->id }}"
                        data-quantity="{{ $item['stock'] }}">Giảm còn {{ $item['stock'] }}</button>
                @endif
            </div>
        </div>
    @empty
        <p>Giỏ hàng của bạn đang trống.</p>
    @endforelse
</div>
<div class="mini-cart-footer">
    @if ($hasStockIssues)
        <div class="cart-stock-warning" role="status">
            Vui lòng xóa sản phẩm hết hàng hoặc điều chỉnh số lượng trước khi thanh toán.
        </div>
    @endif
    <div class="mini-cart-sub-total">
        <h5>Tạm tính: <span>{{ number_format($cartTotal, 0, ',', '.') }} VNĐ</span></h5>
    </div>
    <div class="btn-wrapper">
        <a href="{{ route('cart') }}" class="theme-btn-1 btn btn-effect-1">Xem giỏ hàng</a>
        <a href="{{ route('checkout') }}" class="theme-btn-2 btn btn-effect-2 cart-checkout"
            @if (!$canCheckout) data-cart-checkout-blocked="1" aria-disabled="true" tabindex="-1" @endif>Thanh toán</a>
    </div>
</div>
