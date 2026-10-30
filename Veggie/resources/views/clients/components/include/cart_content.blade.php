<div class="liton__shoping-cart-area mb-120" id="cart-page-content">
    <div class="container">
        <div class="row"><div class="col-lg-12">
            <div class="shoping-cart-inner">
                @if ($hasStockIssues)
                    <div class="alert alert-warning cart-stock-warning" role="alert">
                        Có sản phẩm không đủ hàng trong kho. Vui lòng xóa sản phẩm hết hàng hoặc điều chỉnh số lượng trước khi thanh toán.
                    </div>
                @endif
                <div class="shoping-cart-table table-responsive">
                    <table class="table"><tbody>
                        @forelse ($cartItems as $item)
                            @php($product = $item['product'])
                            <tr data-product-id="{{ $product->id }}" class="{{ $item['is_out_of_stock'] ? 'cart-item-out-of-stock' : '' }}">
                                <td class="cart-product-remove"><button type="button" class="remove-from-cart" data-id="{{ $product->id }}" aria-label="Xóa {{ $product->name }} khỏi giỏ hàng">×</button></td>
                                <td class="cart-product-image">
                                    <a href="javascript:void(0)" class="cart-product-image-link"><img src="{{ $product->image_url }}" alt="{{ $product->name }}">
                                        @if ($item['is_out_of_stock'])<span class="cart-stock-badge">Hết hàng</span>@endif
                                    </a>
                                </td>
                                <td class="cart-product-info"><h4><a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a></h4>
                                    @if ($item['stock_issue'] && !$item['is_out_of_stock'])
                                        <p class="cart-stock-note">Chỉ còn {{ $item['stock'] }} {{ $product->unit ?: 'sản phẩm' }}.</p>
                                        <button type="button" class="btn btn-sm btn-outline-warning cart-adjust-stock" data-id="{{ $product->id }}" data-quantity="{{ $item['stock'] }}">Giảm còn {{ $item['stock'] }}</button>
                                    @elseif ($item['is_out_of_stock'])<p class="cart-stock-note">Sản phẩm hiện đã hết hàng.</p>@endif
                                </td>
                                <td class="cart-product-price">{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                                <td class="cart-product-quantity"><div class="cart-plus-minus">
                                    <button type="button" class="dec qtybutton" aria-label="Giảm số lượng" @disabled($item['is_out_of_stock'])>−</button>
                                    <input type="text" value="{{ $item['quantity'] }}" name="qtybutton" class="cart-plus-minus-box" readonly data-cart-quantity="1" data-max="{{ $item['stock'] }}" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-unit="{{ $product->unit ?: 'sản phẩm' }}" @disabled($item['is_out_of_stock'])>
                                    <button type="button" class="inc qtybutton" aria-label="Tăng số lượng" @disabled($item['is_out_of_stock'])>+</button>
                                </div></td>
                                <td class="cart-product-subtotal">{{ number_format($item['subtotal'], 0, ',', '.') }} VNĐ</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">Giỏ hàng của bạn đang trống</td></tr>
                        @endforelse
                    </tbody></table>
                </div>
                <div class="shoping-cart-total mt-50">
                    <h4>Tổng giỏ hàng</h4><table class="table"><tbody>
                        <tr><td>Tổng tiền hàng</td><td><span class="cart-total">{{ number_format($cartTotal, 0, ',', '.') }} VNĐ</span></td></tr>
                        <tr><td>Phí vận chuyển</td><td>{{ number_format($cartItems ? 15000 : 0, 0, ',', '.') }} VNĐ</td></tr>
                        <tr><td><strong>Tổng thanh toán</strong></td><td><strong><span class="cart-grand-total">{{ number_format($cartTotal + ($cartItems ? 15000 : 0), 0, ',', '.') }} VNĐ</span></strong></td></tr>
                    </tbody></table>
                    <div class="btn-wrapper text-right text-end">
                        @if ($canCheckout)<a href="{{ route('checkout') }}" class="theme-btn-1 btn btn-effect-1">Thanh toán</a>
                        @else<button type="button" class="theme-btn-1 btn btn-effect-1" data-cart-checkout-blocked="1" aria-disabled="true">Thanh toán</button>@endif
                    </div>
                </div>
            </div>
        </div></div>
    </div>
</div>
