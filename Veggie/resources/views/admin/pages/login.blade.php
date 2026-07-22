<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Đăng nhập Admin</title>

    <!-- Bootstrap -->
    <link href="{{ asset('assets/admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ asset('assets/admin/vendors/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ asset('assets/admin/vendors/nprogress/nprogress.css') }}" rel="stylesheet">
    <!-- Animate.css -->
    <link href="{{ asset('assets/admin/vendors/animate.css/animate.min.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ asset('assets/admin/build/css/custom.css') }}" rel="stylesheet">

    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="login">
    <div>
        <a class="hiddenanchor" id="signin"></a>

        <div class="login_wrapper">
            <div class="animate form login_form">
                <section class="login_content">
                    <form action="{{ route('admin.login.post') }}" method="POST">
                        @csrf
                        <h1>Đăng nhập</h1>
                        <div>
                            <input type="email" class="form-control" name="email" placeholder="Email" required />
                        </div>
                        <div>
                            <input type="password" class="form-control" name="password" placeholder="Password"
                                required />
                        </div>
                        <div>
                            <button type="submit" class="btn btn-default submit">Đăng nhập</button>
                        </div>
                        <div class="separator">
                            <div class="clearfix"></div>
                            <br />
                            <div>
                                <h1><i class="fa fa-paw"></i>Veggie Shop</h1>
                                <p>©2016 All Rights Reserved. Khanh Dev is a Bootstrap 4 template. Privacy and
                                    Terms</p>
                            </div>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="{{ asset('assets/admin/vendors/jquery/dist/jquery.min.js') }}"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" crossorigin="anonymous"
        referrerpolicy="no-referrer"></script>

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
