@extends('layouts.client')
@section('title', 'Quên mật khẩu')
@section('breadcrumb', 'Quên mật khẩu')

@section('content')
<div class="ltn__login-area pb-65">
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-lg-6">
				<div class="account-login-inner">
					<h3 class="mb-3">Lấy lại mật khẩu</h3>

					@if (session('status'))
						<div class="alert alert-success">{{ session('status') }}</div>
					@endif

					<form action="{{ route('post.forgot-password') }}" class="ltn__form-box contact-form-box" method="post">
						@csrf
						<input type="email" name="email" placeholder="Email*" value="{{ old('email') }}" required>

						@error('email')
							<span class="text-danger">{{ $message }}</span>
						@enderror

						<div class="btn-wrapper mt-0">
							<button class="theme-btn-1 btn btn-block" type="submit">GỬI LIÊN KẾT ĐẶT LẠI</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection
