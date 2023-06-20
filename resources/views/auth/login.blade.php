<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PORTAL | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet"
        href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <style>
        .login-box, .register-box {
            width: 100%!important;
        }
    </style>
</head>

<body class="hold-transition login-page">
    <div class="d-flex flex-row col-md-6 info-box">
        <div class="d-flex justify-content-center align-items-center col-md-6">
            <img src="{{ asset('images/logo-login.png') }}" class="img-fluid p-3">
        </div>
        <div class="d-flex col-md-6 justify-content-center align-items-center">
            <form action="#" method="POST">
                <div class="{{ $auth_type ?? 'login' }}-box">
                    <div class="card {{ config('card-outline card-primary') }}">
                        <div class="card-header">
                            <h3 class="card-title">Selamat Datang</h3>
                        </div>
                        <div class="card-body">
                            <form id="formData" name="formData" method="POST" action="{{ route('login') }}">
                                {{ csrf_field() }}

                                @if(Session::has('error'))
                                    <div class="alert alert-danger alert-message">
                                        {{Session::get('error')}}
                                    </div>
                                @endif

                                @if($errors->any())
                                    <div class="alert alert-danger alert-message">
                                        {{$errors->first()}}
                                    </div>
                                @endif
                                
                                <div class="input-group mb-3">
                                    <input type="text" id="userid" name="userid" class="form-control {{ $errors->has('userid') ? 'is-invalid' : '' }}"
                                        value="{{ old('userid') }}" placeholder="NIK" autofocus>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-at"></span>
                                        </div>
                                    </div>
                                    @if($errors->has('userid'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('userid') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="input-group mb-3">
                                    <input type="password" id="password" name="password"
                                        class="form-control {{ $errors->has('pass') ? 'is-invalid' : '' }}"
                                        placeholder="Password">
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span
                                                class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                        </div>
                                    </div>
                                    @if($errors->has('pass'))
                                        <div class="invalid-feedback">
                                            <strong>{{ $errors->first('pass') }}</strong>
                                        </div>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type=submit
                                            class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}"
                                            style="background-color: #00a150; border-color:#00a150">
                                            <span class="fas fa-sign-in-alt"></span>
                                            Login
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    <!-- /.login-box -->

    <!-- jQuery -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap 4 -->
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/adminlte.min.js') }}"></script>

    <script>
    window.setTimeout(function() {
    $(".alert-message").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
    }, 5000);
    </script>

</body>

</html>
