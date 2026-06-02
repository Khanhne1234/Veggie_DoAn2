@extends('layouts.client')
@section('title', 'Đăng nhập')
@section('breadcrumb', 'Đăng nhập')

@section('content')


        <!-- LOGIN AREA START -->
        <div class="ltn__login-area pb-65">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="section-title-area text-center">
                            <h1 class="section-title">Đăng nhập <br>Vào tài khoản của bạn</h1>
                            <p>Vui lòng đăng nhập để tiếp tục mua sắm và quản lý đơn hàng của bạn. <br>
                                Nhanh chóng, an toàn và tiện lợi hơn mỗi ngày.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="account-login-inner">
                            <form action="{{ route('post.login') }}" class="ltn__form-box contact-form-box" method="post" id="login-form">
                                @csrf
                                <input type="email" name="email" placeholder="Email*" value="{{ old('email') }}" required>
                                @if ($errors->has('email'))
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                @endif
                                <input type="password" name="password" placeholder="Mật khẩu*" required>
                                @if ($errors->has('password'))
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                @endif
                                <div class="btn-wrapper mt-0">
                                    <button class="theme-btn-1 btn btn-block" type="submit">ĐĂNG NHẬP</button>
                                </div>
                                <div class="go-to-btn mt-20">
                                    <a href="{{ route('password.request') }}"><small>BẠN QUÊN MẬT KHẨU?</small></a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="account-create text-center pt-50">
                            <h4>BẠN CHƯA CÓ TÀI KHOẢN?</h4>
                            <p>Thêm sản phẩm vào danh sách yêu thích, nhận gợi ý phù hợp hơn. <br>
                                Thanh toán nhanh hơn và theo dõi đơn hàng dễ dàng khi đăng ký.</p>
                            <div class="btn-wrapper">
                                <a href="{{ route('register') }}" class="theme-btn-1 btn black-btn">TẠO TÀI KHOẢN</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- LOGIN AREA END -->

@endsection
