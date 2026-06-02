@extends('layouts.client')
@section('title', 'Đặt lại mật khẩu')
@section('breadcrumb', 'Đặt lại mật khẩu')

@section('content')
<div class="ltn__login-area pb-65">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6">
				<div class="account-login-inner">
					<h3 class="mb-3">Đặt lại mật khẩu</h3>

					@if (session('status'))
						<div class="alert alert-success">{{ session('status') }}</div>
					@endif

					<form action="{{ route('post.reset-password') }}" class="ltn__form-box contact-form-box" method="post">
						@csrf
						<input type="hidden" name="token" value="{{ $token }}">

						<input type="email" name="email" placeholder="Email*" value="{{ old('email', $email ?? '') }}" required>
						@error('email')
							<span class="text-danger">{{ $message }}</span>
						@enderror

						<input type="password" name="password" placeholder="Mật khẩu mới*" required>
						@error('password')
							<span class="text-danger">{{ $message }}</span>
						@enderror

						<input type="password" name="password_confirmation" placeholder="Xác nhận mật khẩu mới*" required>

						<div class="btn-wrapper mt-0">
							<button class="theme-btn-1 btn btn-block" type="submit">CẬP NHẬT MẬT KHẨU</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
