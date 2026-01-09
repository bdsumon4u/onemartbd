@extends('backEnd.admin.layouts.master')

@section('title')
    Product Videos
@endsection

@section('body')
    <div class="dashboard-wrapper">
        <div class="dashboard-ecommerce">
            <div class="container-fluid dashboard-content ">
                <!-- ============================================================== -->
                <!-- pageheader  -->
                <!-- ============================================================== -->
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <div class="page-header">
                            <h2 class="pageheader-title">Product Videos</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a
                                                href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}"
                                                class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Product Videos</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ============================================================== -->
                <!-- end pageheader  -->
                <!-- ============================================================== -->

                <div class="row">
                    <div class="col-12">
                        {{--<embed style="height: 950px; width: 100%" type="text/html" src="https://docs.google.com/presentation/d/e/2PACX-1vSeLNQVSCFPd6WDRHdu0XY9--A8FPdzCtRcBK2A4fA-NO-mWr4BOHJ550LhSsP1KKeQO9k2ZWX66Fnk/embed?start=false&loop=false&delayms=3000&slide=id.p"  --}}{{--width="1980" height="950"--}}{{-->--}}
                        <iframe
                            src="https://docs.google.com/presentation/d/e/2PACX-1vSeLNQVSCFPd6WDRHdu0XY9--A8FPdzCtRcBK2A4fA-NO-mWr4BOHJ550LhSsP1KKeQO9k2ZWX66Fnk/embed?start=false&loop=false&delayms=3000"
                            frameborder="0" width="960" height="569" allowfullscreen="true" mozallowfullscreen="true" webkitallowfullscreen="true"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
