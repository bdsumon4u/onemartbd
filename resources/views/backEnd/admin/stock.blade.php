@extends('backEnd.admin.layouts.master')

@section('title')
    Stock
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
                            <h2 class="pageheader-title">Stock</h2>
                            <div class="page-breadcrumb">
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}" class="breadcrumb-link">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Stock</li>
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
                        <embed style="height: 950px; width: 100%" type="text/html" src="https://docs.google.com/document/d/e/2PACX-1vT20OCs4SOQ9dn2aqUFIOrp6Z-nyfbiQnB4qCfk9ryw0ky63IdHVcDVI9xzrUFIWw/pub"  {{--width="1980" height="950"--}}>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
