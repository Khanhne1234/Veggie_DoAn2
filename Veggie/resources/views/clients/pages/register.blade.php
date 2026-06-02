@extends('layouts.client')
@section('title', 'Đăng ký')
@section('breadcrumb', 'Đăng ký')

@section('content')

    <!-- KHU VỰC ĐĂNG KÝ BẮT ĐẦU -->
    <div class="ltn__login-area pb-110">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-title-area text-center">
                        <h1 class="section-title">Đăng ký <br>Tài khoản</h1>
                        <p>Tạo tài khoản để theo dõi đơn hàng, lưu địa chỉ giao nhận và nhận ưu đãi mới nhất từ Veggie.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="account-login-inner">
                        <form action="{{ route('post.register') }}" class="ltn__form-box contact-form-box" method="post" id="register-form" novalidate>
                            @csrf
                            <input type="text" name="name" placeholder="Họ và Tên*" value="{{ old('name') }}" required>
                            <!-- Hiển thị lỗi nếu có -->
                            @error('name')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <input type="email" name="email" placeholder="Email*" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <input type="password" name="password" placeholder="Mật khẩu*" required>
                            @error('password')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <input type="password" name="confirmpassword" placeholder="Xác nhận mật khẩu*" required>
                            <label class="checkbox-inline">
                                <input type="checkbox" name="checkbox1" value="" required>
                                Tôi đồng ý cho Veggie xử lý dữ liệu cá nhân để gửi thông tin ưu đãi và nội dung tiếp thị
                                phù hợp theo điều khoản đồng ý và chính sách bảo mật.
                            </label>
                            @error('checkbox1')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <label class="checkbox-inline">
                                <input type="checkbox" name="checkbox2" value="" required>
                                Bằng cách nhấn "Tạo tài khoản", tôi đồng ý với chính sách bảo mật.
                            </label>
                            @error('checkbox2')
                                <div class="alert alert-danger">{{ $message }}</div>
                            @enderror
                            <div class="btn-wrapper">
                                <button class="theme-btn-1 btn reverse-color btn-block" type="submit">TẠO
                                    TÀI KHOẢN</button>
                            </div>
                        </form>
                        <div class="by-agree text-center">
                            <p>Khi tạo tài khoản, bạn đồng ý với:</p>
                            <p><a href="#">ĐIỀU KHOẢN SỬ DỤNG &nbsp; &nbsp; | &nbsp; &nbsp; CHÍNH SÁCH BẢO MẬT</a></p>
                            <div class="go-to-btn mt-50">
                                <a href="{{ route('login') }}">BẠN ĐÃ CÓ TÀI KHOẢN?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
