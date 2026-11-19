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
            <section class="auth-panel" aria-labelledby="login-title">
                <div class="auth-panel-inner">
                    <a class="brand" href="{{ route('home') }}" aria-label="Về trang chủ Broccoli Shop">
                        <span class="brand-mark" aria-hidden="true"><i class="fa fa-leaf"></i></span>
                        <strong>Broccoli Shop</strong>
                    </a>
                    <div class="auth-heading">
                        <h1 id="login-title">Đăng nhập quản trị</h1>
                        <p>Chào mừng bạn quay lại</p>
                    </div>
                    <form class="auth-form" action="{{ route('admin.login.post') }}" method="POST"
                        id="admin-login-form">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrap @error('email') has-error @enderror">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    placeholder="admin@example.com" autocomplete="username" required autofocus>
                            </div>
                            @error('email')
                                <span class="field-error" role="alert">
                                    <i class="fa fa-exclamation-circle"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Mật khẩu</label>
                            <div class="input-wrap @error('password') has-error @enderror">
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
                            <span data-button-label>Đăng nhập</span>
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>
                    <div class="auth-footer">
                        <a href="{{ route('home') }}">
                            <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Về cửa hàng
                        </a>
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
