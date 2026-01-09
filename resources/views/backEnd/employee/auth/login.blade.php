<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Employee Login</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="{{asset('/')}}backEnd/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/libs/css/style.css">
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.css">
    <style>
        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #0e0c28;
        }
    </style>
</head>

<body>
<!-- ============================================================== -->
<!-- login page  -->
<!-- ============================================================== -->
<div class="splash-container">
    <div class="card ">
        <div class="card-header text-center">
            <a href="{{route('employee.home')}}">
                <img width="250" class="logo-img"
                     src="{{$web_settings->get_logo ? asset($web_settings->get_logo->file_url) : asset('frontEnd/images/no_image.png')}}" alt="logo">
            </a>
            <hr>
            <h3 class="text-uppercase mb-0">Employee Sign In</h3>
        </div>
        <div class="card-body">
            <form method="post" action="{{route('employee.login')}}">
                @csrf
                <div class="form-group">
                    <input class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" type="text" placeholder="Email"
                           value="{{old('email')}}" required>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">
                    <input class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" type="password"
                           placeholder="Password" required>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                {{--<div class="form-group">
                    <label class="custom-control custom-checkbox">
                        <input class="custom-control-input" type="checkbox"><span class="custom-control-label">Remember Me</span>
                    </label>
                </div>--}}
                <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in</button>
            </form>
        </div>
        {{--<div class="card-footer bg-white p-0  ">
            <div class="card-footer-item card-footer-item-bordered">
                <a href="#" class="footer-link">Create An Account</a></div>
            <div class="card-footer-item card-footer-item-bordered">
                <a href="#" class="footer-link">Forgot Password</a>
            </div>
        </div>--}}
    </div>
</div>

<!-- ============================================================== -->
<!-- end login page  -->
<!-- ============================================================== -->
<!-- Optional JavaScript -->
<script src="{{asset('/')}}backEnd/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="{{asset('/')}}backEnd/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<script src="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.js"></script>

<script>
    @if(session()->has('success'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.success("{{ session('success') }}");
    @endif

        @if(Session::has('error'))
        toastr.options = {
        "positionClass": "toast-bottom-left"
    };
    toastr.error("{{ session('error') }}");
    @endif
</script>
</body>

</html>
