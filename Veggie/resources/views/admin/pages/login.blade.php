<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <meta name='theme-color' content='#2f7d4a'>
    <title>Đăng nhập quản trị - Veggie Shop</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/clients/img/favicon.png') }}">
    <link href="{{ asset('assets/admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/admin/css/login.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="admin-login-page">
    <main class="auth-page">
        <div class="auth-shell">
            <section class="auth-showcase"
                style="--showcase-image: url('{{ asset('assets/clients/img/bg/15.jpg') }}')"
                aria-label="Giới thiệu Veggie Shop">
                <div class="showcase-top">
                    <a class="brand brand-light" href="{{ route('home') }}" aria-label="Về trang chủ Veggie Shop">
                        <span class="brand-mark" aria-hidden="true"><i class="fa fa-leaf"></i></span>
                        <span class="brand-copy">
                            <strong>Veggie Shop</strong>
                            <small>Fresh food market</small>
                        </span>
                    </a>
                    <span class="secure-badge"><i class="fa fa-shield"></i> Hệ thống nội bộ</span>
                </div>
                <div class="showcase-content">
                    <span class="eyebrow">Quản lý cửa hàng thông minh</span>
                    <h1>Tươi mỗi ngày,<br><span>chuẩn từng đơn.</span></h1>
                    <p>Theo dõi hoạt động bán hàng, sản phẩm và khách hàng trong một không gian quản trị tập trung.</p>
                    <div class="feature-grid" aria-label="Các chức năng quản trị">
                        <div class="feature-item">
                            <span><i class="fa fa-shopping-basket"></i></span>
                            <div><strong>Đơn hàng</strong><small>Xử lý nhanh chóng</small></div>
                        </div>
                        <div class="feature-item">
                            <span><i class="fa fa-cubes"></i></span>
                            <div><strong>Kho hàng</strong><small>Kiểm soát chính xác</small></div>
                        </div>
                        <div class="feature-item">
                            <span><i class="fa fa-line-chart"></i></span>
                            <div><strong>Doanh thu</strong><small>Theo dõi trực quan</small></div>
                        </div>
                    </div>
                </div>
                <div class="showcase-footer">
                    <span><i class="fa fa-check-circle"></i> Thực phẩm tươi sạch</span>
                    <span><i class="fa fa-check-circle"></i> Vận hành an toàn</span>
                </div>
            </section>
            <section class="auth-panel">
                <div class="auth-panel-inner">
                    <div class="mobile-header">
                        <a class="brand" href="{{ route('home') }}" aria-label="Về trang chủ Veggie Shop">
                            <span class="brand-mark" aria-hidden="true"><i class="fa fa-leaf"></i></span>
                            <span class="brand-copy">
                                <strong>Veggie Shop</strong>
                                <small>Fresh food market</small>
                            </span>
                        </a>
                    </div>
                    <div class="auth-heading">
                        <span class="admin-label"><i class="fa fa-lock"></i> Khu vực quản trị</span>
                        <h2>Chào mừng trở lại</h2>
                        <p>Đăng nhập để tiếp tục quản lý cửa hàng của bạn.</p>
                    </div>
                    <form class="auth-form" action="{{ route('admin.login.post') }}" method="POST"
                        id="admin-login-form">
                        @csrf
                        <div class="form-group">
                            <label for="email">Địa chỉ email</label>
                            <div class="input-wrap @error('email') has-error @enderror">
                                <i class="fa fa-envelope-o field-icon" aria-hidden="true"></i>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder="admin@veggieshop.vn" autocomplete="email" required autofocus>
                            </div>
                            @error('email')
                                <span class="field-error" role="alert">
                                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="label-row">
                                <label for="password">Mật khẩu</label>
                                <span>Tối thiểu 6 ký tự</span>
                            </div>
                            <div class="input-wrap @error('password') has-error @enderror">
                                <i class="fa fa-key field-icon" aria-hidden="true"></i>
                                <input id="password" type="password" name="password" placeholder="Nhập mật khẩu"
                                    autocomplete="current-password" minlength="6" required>
                                <button class="password-toggle" type="button" aria-label="Hiện mật khẩu"
                                    aria-pressed="false" data-password-toggle>
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="field-error" role="alert">
                                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <button type="submit" class="login-button" data-submit-button>
                            <span data-button-label>Đăng nhập hệ thống</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                        <div class="security-note">
                            <i class="fa fa-shield" aria-hidden="true"></i>
                            <span>Chỉ quản trị viên và nhân viên được cấp quyền mới có thể truy cập.</span>
                        </div>
                    </form>
                    <div class="auth-footer">
                        <a href="{{ route('home') }}">
                            <i class="fa fa-long-arrow-left"></i> Quay lại cửa hàng
                        </a>
                        <span>&copy; {{ date('Y') }} Veggie Shop</span>
                    </div>
                </div>
            </section>
        </div>
    </main>
    <script src="{{ asset('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.querySelector('[data-password-toggle]');
            const loginForm = document.getElementById('admin-login-form');
            const submitButton = document.querySelector('[data-submit-button]');
            const buttonLabel = document.querySelector('[data-button-label]');

            passwordToggle.addEventListener('click', function () {
                const isVisible = passwordInput.type === 'text';
                passwordInput.type = isVisible ? 'password' : 'text';
                this.setAttribute('aria-label', isVisible ? 'Hiện mật khẩu' : 'Ẩn mật khẩu');
                this.setAttribute('aria-pressed', String(!isVisible));
                this.querySelector('i').className = isVisible ? 'fa fa-eye' : 'fa fa-eye-slash';
                passwordInput.focus();
            });

            loginForm.addEventListener('submit', function () {
                submitButton.disabled = true;
                submitButton.classList.add('is-loading');
                buttonLabel.textContent = 'Đang đăng nhập...';
            });
        })();
    </script>

    @if (session('toastr'))
        @php
            $toast = session('toastr');
            $toastType = in_array($toast['type'] ?? '', ['success', 'error', 'info', 'warning'], true)
                ? $toast['type']
                : 'info';
        @endphp
        <script>
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: 5000,
                extendedTimeOut: 1500,
                positionClass: 'toast-top-right'
            };
            toastr["{{ $toastType }}"](@json($toast['message'] ?? ''));
        </script>
    @endif
</body>
</html>
