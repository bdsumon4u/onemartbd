<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{config('app.name')}} - @yield('title')</title>
    <link rel="icon" href="{{$web_settings->get_fav ? asset($web_settings->get_fav->file_url) : asset('frontEnd/images/no_image.png')}}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="{{asset('/')}}backEnd/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}backEnd/assets/vendor/select2/css/select2.css">

    <link rel="stylesheet" href="{{asset('/')}}backEnd/assets/libs/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{asset('/')}}backEnd/assets/libs/css/custom_style.css">


    @yield('css')
</head>

<body>
<!-- ============================================================== -->
<!-- main wrapper -->
<!-- ============================================================== -->
<div class="dashboard-main-wrapper">
    <!-- ============================================================== -->
    <!-- navbar -->
    <!-- ============================================================== -->
@include('backEnd.admin.includes.header')
<!-- ============================================================== -->
    <!-- end navbar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- left sidebar -->
    <!-- ============================================================== -->
@include('backEnd.admin.includes.sidebar')
<!-- ============================================================== -->
    <!-- end left sidebar -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- wrapper  -->
    <!-- ============================================================== -->
@yield('body')
<!-- ============================================================== -->
    <!-- end wrapper  -->
    <!-- ============================================================== -->
    @include('backEnd.admin.includes.footer')
</div>

<!-- ============================================================== -->
<!-- end main wrapper  -->
<!-- ============================================================== -->
<!-- Optional JavaScript -->
<!-- jquery 3.3.1 -->
<script src="{{asset('/')}}backEnd/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<!-- bootstap bundle js -->
<script src="{{asset('/')}}backEnd/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<!-- slimscroll js -->
<script src="{{asset('/')}}backEnd/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<script src="{{asset('/')}}backEnd/assets/vendor/select2/js/select2.min.js"></script>
<!-- main js -->
<script src="{{asset('/')}}backEnd/assets/libs/js/main-js.js"></script>
<script src="{{asset('/')}}backEnd/assets/vendor/toastr/toastr.min.js"></script>
<script>
    @if(session()->has('success'))
        toastr.options = {
        "positionClass": "toast-bottom-right"
    };
    toastr.success("{{ session('success') }}");
    @endif

        @if(Session::has('error'))
        toastr.options = {
        "positionClass": "toast-bottom-right"
    };
    toastr.error("{{ session('error') }}");
    @endif


        @if(Session::has('info'))
        toastr.options = {
        "positionClass": "toast-bottom-right"
    };
    toastr.info("{{ session('info') }}");
    @endif

        @if(Session::has('warning'))
        toastr.options = {
        "positionClass": "toast-bottom-right"
    };
    toastr.warning("{{ session('warning') }}");
    @endif
</script>

@yield('js')
</body>
</html>
