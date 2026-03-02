<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <link rel="icon"
        href="{{ $web_settings->get_fav ? asset($web_settings->get_fav->file_url) : asset('frontEnd/images/no_image.png') }}">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link href="{{ asset('/') }}backEnd/assets/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet"
        href="{{ asset('/') }}backEnd/assets/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('/') }}backEnd/assets/vendor/toastr/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('/') }}backEnd/assets/vendor/select2/css/select2.css">

    <link rel="stylesheet" href="{{ asset('/') }}backEnd/assets/libs/css/style.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('/') }}backEnd/assets/libs/css/custom_style.css">


    <style>
        .push-toggles .btn,
        .push-toggles-mobile .btn {
            width: 34px;
            height: 34px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #dee2e6;
            background: #fff;
            color: #495057;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .push-toggles .btn:hover,
        .push-toggles-mobile .btn:hover {
            background: #f1f3f5;
        }

        .push-toggles .btn.active,
        .push-toggles-mobile .btn.active {
            background: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .push-toggles .btn.muted,
        .push-toggles-mobile .btn.muted {
            background: #f8f9fa;
            border-color: #dee2e6;
            color: #adb5bd;
        }

        .push-toggles .btn.audio-locked,
        .push-toggles-mobile .btn.audio-locked {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
    </style>
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
    <script src="{{ asset('/') }}backEnd/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
    <!-- bootstap bundle js -->
    <script src="{{ asset('/') }}backEnd/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <!-- slimscroll js -->
    <script src="{{ asset('/') }}backEnd/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="{{ asset('/') }}backEnd/assets/vendor/select2/js/select2.min.js"></script>
    <!-- main js -->
    <script src="{{ asset('/') }}backEnd/assets/libs/js/main-js.js"></script>
    <script src="{{ asset('/') }}backEnd/assets/vendor/toastr/toastr.min.js"></script>
    <script>
        @if (session()->has('success'))
            toastr.options = {
                "positionClass": "toast-bottom-right"
            };
            toastr.success("{{ session('success') }}");
        @endif

        @if (Session::has('error'))
            toastr.options = {
                "positionClass": "toast-bottom-right"
            };
            toastr.error("{{ session('error') }}");
        @endif


        @if (Session::has('info'))
            toastr.options = {
                "positionClass": "toast-bottom-right"
            };
            toastr.info("{{ session('info') }}");
        @endif

        @if (Session::has('warning'))
            toastr.options = {
                "positionClass": "toast-bottom-right"
            };
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>

    @yield('js')

    <script src="{{ asset('backEnd/assets/js/push-notifications.js') }}"></script>
    <script>
        (function() {
            @php
                $pushSubscribeUrl = '';
                if (Auth::guard('admin')->check()) {
                    $pushSubscribeUrl = route('admin.push.subscribe');
                } elseif (Auth::guard('manager')->check()) {
                    $pushSubscribeUrl = route('manager.push.subscribe');
                } elseif (Auth::guard('employee')->check()) {
                    $pushSubscribeUrl = route('employee.push.subscribe');
                }

                $pushUnsubscribeUrl = '';
                if (Auth::guard('admin')->check()) {
                    $pushUnsubscribeUrl = route('admin.push.unsubscribe');
                } elseif (Auth::guard('manager')->check()) {
                    $pushUnsubscribeUrl = route('manager.push.unsubscribe');
                } elseif (Auth::guard('employee')->check()) {
                    $pushUnsubscribeUrl = route('employee.push.unsubscribe');
                }
                $userRole = 'guest';
                if (Auth::guard('admin')->check()) {
                    $userRole = 'admin';
                } elseif (Auth::guard('manager')->check()) {
                    $userRole = 'manager';
                } elseif (Auth::guard('employee')->check()) {
                    $userRole = 'employee';
                }
            @endphp

            var subscribeUrl = "{{ $pushSubscribeUrl }}";
            if (subscribeUrl) {
                PushNotificationManager.init({
                    vapidPublicKey: "{{ config('webpush.vapid.public_key') }}",
                    subscribeUrl: subscribeUrl,
                    unsubscribeUrl: "{{ $pushUnsubscribeUrl }}",
                    csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    userRole: "{{ $userRole }}"
                });
            }
        })();
    </script>
</body>

</html>
