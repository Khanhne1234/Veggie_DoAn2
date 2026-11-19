<!-- Utilize Mobile Menu Start -->
<div id="ltn__utilize-mobile-menu" class="ltn__utilize ltn__utilize-mobile-menu" role="dialog" aria-modal="true" aria-label="Menu điều hướng" tabindex="-1" inert>
    <div class="ltn__utilize-menu-inner ltn__scrollbar">
        <div class="ltn__utilize-menu-head">
            <div class="site-logo">
                <a href="{{ route('home') }}"><img src="{{ asset('assets/clients/img/logo.png') }}" alt="Logo"></a>
            </div>
            <button type="button" class="ltn__utilize-close" aria-label="Đóng menu">×</button>
        </div>
        <div class="ltn__utilize-menu-search-form">
            <form action="{{ route('search') }}" method="GET" role="search">
                <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm" value="{{ request('keyword') }}">
                <button type="submit" aria-label="Tìm kiếm"><i class="fas fa-search" aria-hidden="true"></i></button>
            </form>
        </div>
        <div class="ltn__utilize-menu">
            <ul>
                <li><a href="{{ route('home') }}">Trang chủ</a> </li>
                <li><a href="{{ url('/about') }}">Về chúng tôi</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/about') }}">Về chúng tôi</a></li>
                        <li><a href="{{ url('/service') }}">Dịch vụ</a></li>
                        <li><a href="{{ url('/team') }}">Team</a></li>
                        <li><a href="{{ url('/faq') }}">FAQ</a></li>
                    </ul>
                </li>
                <li><a href="{{ route('products.index') }}">Cửa hàng</a></li>
                <li><a href="{{ route('contact.index') }}">Liên hệ</a></li>
            </ul>
        </div>
        <div class="ltn__utilize-buttons ltn__utilize-buttons-2">
            <ul>
                <li>
                    <a href="{{ route('account') }}" title="Tài khoản">
                        <span class="utilize-btn-icon">
                            <i class="far fa-user"></i>
                        </span>
                        Tài khoản
                    </a>
                </li>
                <li>
                    <a href="{{ route('wishlist') }}" title="Yêu thích">
                        <span class="utilize-btn-icon">
                            <i class="far fa-heart"></i>
                        </span>
                        Yêu thích
                    </a>
                </li>
                <li>
                    <a href="{{ route('cart') }}" title="Giỏ hàng">
                        <span class="utilize-btn-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </span>
                        Giỏ hàng
                    </a>
                </li>
            </ul>
        </div>
        <div class="ltn__social-media-2">
            <ul>
                <li><a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                <li><a href="#" title="Twitter"><i class="fab fa-twitter"></i></a></li>
                <li><a href="#" title="Linkedin"><i class="fab fa-linkedin"></i></a></li>
                <li><a href="#" title="Instagram"><i class="fab fa-instagram"></i></a></li>
            </ul>
        </div>
    </div>
</div>
<!-- Utilize Mobile Menu End -->
