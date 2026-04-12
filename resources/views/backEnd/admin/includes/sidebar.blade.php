<div class="nav-left-sidebar sidebar-dark">
    <div class="menu-list">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="d-xl-none d-lg-none" href="#">Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav flex-column">

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin') ? 'active' : '' }}"
                                href="{{ route('admin.home') }}">
                                <i class="fas fa-fw fa-desktop"></i>
                                Dashboard
                            </a>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager') ? 'active' : '' }}"
                                href="{{ route('manager.home') }}">
                                <i class="fas fa-fw fa-desktop"></i>
                                Dashboard
                            </a>
                        @elseif(Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee') ? 'active' : '' }}"
                                href="{{ route('employee.home') }}">
                                <i class="fas fa-fw fa-desktop"></i>
                                Dashboard
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-customers*') ? 'active' : '' }}"
                                href="{{ route('admin.customers') }}">
                                <i class="fas fa-fw fa-users"></i>
                                Customers
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-orders*') || request()->is('admin-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.orders') }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Orders
                            </a>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-orders*') || request()->is('manager-orders*') ? 'active' : '' }}"
                                href="{{ route('manager.orders') }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Orders
                            </a>
                        @elseif(Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee-orders*') || request()->is('employee-orders*') ? 'active' : '' }}"
                                href="{{ route('employee.orders') }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Orders
                            </a>
                        @endif
                    </li>
                    <li class="nav-item" style="position: relative;">
                        @php
                            $incomplete_orders = \App\Models\AbandonedCart::count();
                        @endphp
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-incomplete-orders*') ? 'active' : '' }}"
                                href="{{ route('admin.incomplete.orders') }}">
                                <i class="fas fa-fw fa-exclamation-circle"></i>
                                Inc. Orders
                                @if ($incomplete_orders > 0)
                                    <span class="badge badge-danger" style="position: absolute; right: 10px;">
                                        {{ $incomplete_orders }}
                                    </span>
                                @endif
                            </a>
                        @elseif (Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-incomplete-orders*') ? 'active' : '' }}"
                                href="{{ route('manager.incomplete.orders') }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Inc. Orders
                                @if ($incomplete_orders > 0)
                                    <span class="badge badge-danger" style="position: absolute; right: 10px;">
                                        {{ $incomplete_orders }}
                                    </span>
                                @endif
                            </a>
                        @elseif (Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee-incomplete-orders*') ? 'active' : '' }}"
                                href="{{ route('employee.incomplete.orders') }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Inc. Orders
                                @if ($incomplete_orders > 0)
                                    <span class="badge badge-danger" style="position: absolute; right: 10px;">
                                        {{ $incomplete_orders }}
                                    </span>
                                @endif
                            </a>

                        @endif
                    </li>
                    {{-- Order Return --}}
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-order-return-receive*') ? 'active' : '' }}"
                                href="{{ route('admin.orders.return.receive') }}">
                                <i class="fas fa-fw fa-undo-alt"></i>
                                Return Received
                            </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-order-parcel-handover*') ? 'active' : '' }}"
                                href="{{ route('admin.orders.parcel.handover') }}">
                                <i class="fas fa-fw fa-hand-holding"></i>
                                Parcel Handover
                            </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-orders*') && request('only') == 'Issue' ? 'active' : '' }}"
                                href="{{ route('admin.orders', ['only' => 'Issue']) }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                Entry Issue
                            </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-orders*') && request('only') == 'TimeOver' ? 'active' : '' }}"
                                href="{{ route('admin.orders', ['only' => 'TimeOver']) }}">
                                <i class="fas fa-fw fa-cart-plus"></i>
                                TimeOver
                            </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-product*') || request()->is('admin-landing-pages*') || request()->is('admin/reviews*') || request()->is('admin-category*') || request()->is('admin-sections*') || request()->is('admin-settings-attribute') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true" data-target="#submenu-product"
                                aria-controls="submenu-product">
                                <i class="fas fa-fw fa-box"></i>
                                Product
                            </a>
                            <div id="submenu-product"
                                class="collapse submenu {{ request()->is('admin-product*') || request()->is('admin-landing-pages*') || request()->is('admin/reviews*') || request()->is('admin-category*') || request()->is('admin-sections*') || request()->is('admin-settings-attribute') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-product*') ? 'active' : '' }}"
                                            href="{{ route('admin.product') }}">Product List</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-category*') ? 'active' : '' }}"
                                            href="{{ route('admin.category') }}">Category</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-landing-pages*') ? 'active' : '' }}"
                                            href="{{ route('landing-pages.index') }}">Landing Pages</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin/reviews*') ? 'active' : '' }}"
                                            href="{{ route('admin.reviews.index') }}">
                                            Reviews
                                            @if (
                                                $review_count = cache()->remember('pending_reviews_count', now()->addMinutes(10), function () {
                                                    return \Codebyray\ReviewRateable\Models\Review::where('approved', false)->count();
                                                }))
                                                <span class="badge badge-danger" style="position: absolute; right: 10px;">
                                                    {{ $review_count }}
                                                </span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-sections*') ? 'active' : '' }}"
                                            href="{{ route('admin.sections') }}">Sections</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-attribute') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.attribute') }}">Attributes</a>
                                    </li>
                                </ul>
                            </div>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-product*') ? 'active' : '' }}"
                                href="{{ route('manager.product') }}">
                                <i class="fas fa-fw fa-box"></i>
                                Product
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin/stock*') ? 'active' : '' }}"
                                href="{{ route('admin.stock') }}">
                                <i class="fas fa-fw fa-boxes"></i>
                                Stock
                            </a>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager/stock*') ? 'active' : '' }}"
                                href="{{ route('manager.stock') }}">
                                <i class="fas fa-fw fa-boxes"></i>
                                Stock
                            </a>
                        @elseif(Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee/stock*') ? 'active' : '' }}"
                                href="{{ route('employee.stock') }}">
                                <i class="fas fa-fw fa-boxes"></i>
                                Stock
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin/ip*') ? 'active' : '' }}"
                                href="{{ route('admin.ip') }}">
                                <i class="fas fa-fw fa-globe"></i>
                                IPs
                            </a>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager/ip*') ? 'active' : '' }}"
                                href="{{ route('manager.ip') }}">
                                <i class="fas fa-fw fa-globe"></i>
                                IPs
                            </a>
                        @elseif(Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee/ip*') ? 'active' : '' }}"
                                href="{{ route('employee.ip') }}">
                                <i class="fas fa-fw fa-globe"></i>
                                IPs
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-slider*') ? 'active' : '' }}"
                                href="{{ route('admin.sliders') }}">
                                <i class="fas fa-fw fa-film"></i>
                                Sliders
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-media*') ? 'active' : '' }}"
                                href="{{ route('admin.media') }}">
                                <i class="fas fa-fw fa-images"></i>
                                Media
                            </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-courier') ? 'active' : '' }}"
                                href="{{ route('admin.courier') }}">
                                <i class="fas fa-fw fa-truck"></i>
                                Courier
                            </a>
                        @endif
                    </li>

                    {{-- <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-courier*') ? 'active' : '' }}" href="#"
                                data-toggle="collapse" aria-expanded="true" data-target="#submenu-1"
                                aria-controls="submenu-1">
                                <i class="fas fa-fw fa-truck"></i>
                                Couriers
                            </a>
                            <div id="submenu-1"
                                class="collapse submenu {{ request()->is('admin-courier*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-courier') ? 'active' : '' }}"
                                            href="{{ route('admin.courier') }}">Courier</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-courier-city*') ? 'active' : '' }}"
                                            href="{{ route('admin.courier.city') }}">City</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-courier-zone*') ? 'active' : '' }}"
                                            href="{{ route('admin.courier.zone') }}">Zone</a>
                                    </li>
                                </ul>
                            </div>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-courier*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true" data-target="#submenu-1"
                                aria-controls="submenu-1">
                                <i class="fas fa-fw fa-truck"></i>
                                Courier
                            </a>
                            <div id="submenu-1"
                                class="collapse submenu {{ request()->is('manager-courier*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-courier') ? 'active' : '' }}"
                                            href="{{ route('manager.courier') }}">Courier</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-courier-city*') ? 'active' : '' }}"
                                            href="{{ route('manager.courier.city') }}">City</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-courier-zone*') ? 'active' : '' }}"
                                            href="{{ route('manager.courier.zone') }}">Zone</a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </li> --}}

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-shipping_methods*') ? 'active' : '' }}"
                                href="{{ route('admin.shipping_methods') }}">
                                <i class="fas fa-fw fa-truck-moving"></i>
                                Shipping Methods
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-roles*') ? 'active' : '' }}"
                                href="{{ route('admin.roles') }}">
                                <i class="fas fa-fw fa-user"></i>
                                User
                            </a>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-roles*') ? 'active' : '' }}"
                                href="{{ route('manager.roles') }}">
                                <i class="fas fa-fw fa-user"></i>
                                Users
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-user-product*') ? 'active' : '' }}"
                                href="{{ route('admin.user.product') }}">
                                <i class="fas fa-fw fa-user"></i>
                                User Products
                            </a>
                        @endif
                    </li>

                    <li class="nav-item">
                        @if (Auth::guard('admin')->check())
                            <a class="nav-link {{ request()->is('admin-attendance*') || request()->is('admin-payroll*') || request()->is('admin-my-attendance*') || request()->is('admin-my-payroll*') || request()->is('admin-my-advances*') || request()->is('admin-salary-advances*') || request()->is('admin-user-bonuses*') || request()->is('admin-holidays*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true"
                                data-target="#submenu-attendance-payroll" aria-controls="submenu-attendance-payroll">
                                <i class="fas fa-fw fa-clock"></i>
                                Att. & Payroll
                            </a>
                            <div id="submenu-attendance-payroll"
                                class="collapse submenu {{ request()->is('admin-attendance*') || request()->is('admin-payroll*') || request()->is('admin-my-attendance*') || request()->is('admin-my-payroll*') || request()->is('admin-my-advances*') || request()->is('admin-salary-advances*') || request()->is('admin-user-bonuses*') || request()->is('admin-holidays*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-my-attendance*') ? 'active' : '' }}"
                                            href="{{ route('admin.my_attendance.index') }}">My Attendance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-my-payrolls*') ? 'active' : '' }}"
                                            href="{{ route('admin.my_payroll.index') }}">My Payroll</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-my-advances*') ? 'active' : '' }}"
                                            href="{{ route('admin.my_advances') }}">My Advances</a>
                                    </li>
                                    <li class="nav-item px-3 my-2">
                                        <hr class="m-0" style="border-color: rgba(255,255,255,0.2);">
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-attendance') ? 'active' : '' }}"
                                            href="{{ route('admin.attendance.index') }}">Daily Attendance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-attendance-history') ? 'active' : '' }}"
                                            href="{{ route('admin.attendance.history') }}">Attendance History</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-payroll-settings') ? 'active' : '' }}"
                                            href="{{ route('admin.payroll.settings') }}">Payroll Settings</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-payrolls*') ? 'active' : '' }}"
                                            href="{{ route('admin.payroll.index') }}">Monthly Payroll</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-salary-advances*') ? 'active' : '' }}"
                                            href="{{ route('admin.salary_advances.index') }}">Salary Advances</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-user-bonuses*') ? 'active' : '' }}"
                                            href="{{ route('admin.user_bonuses.index') }}">Special Bonuses</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-holidays*') ? 'active' : '' }}"
                                            href="{{ route('admin.holidays.index') }}">Holidays</a>
                                    </li>
                                </ul>
                            </div>
                        @elseif(Auth::guard('manager')->check())
                            <a class="nav-link {{ request()->is('manager-my-attendance*') || request()->is('manager-my-payrolls*') || request()->is('manager-my-advances*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true"
                                data-target="#submenu-attendance-payroll" aria-controls="submenu-attendance-payroll">
                                <i class="fas fa-fw fa-clock"></i>
                                Att. & Payroll
                            </a>
                            <div id="submenu-attendance-payroll"
                                class="collapse submenu {{ request()->is('manager-my-attendance*') || request()->is('manager-my-payrolls*') || request()->is('manager-my-advances*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-my-attendance*') ? 'active' : '' }}"
                                            href="{{ route('manager.my_attendance.index') }}">My Attendance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-my-payrolls*') ? 'active' : '' }}"
                                            href="{{ route('manager.my_payroll.index') }}">My Payroll</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-my-advances*') ? 'active' : '' }}"
                                            href="{{ route('manager.my_advances') }}">My Advances</a>
                                    </li>
                                </ul>
                            </div>
                        @elseif(Auth::guard('employee')->check())
                            <a class="nav-link {{ request()->is('employee-my-attendance*') || request()->is('employee-my-payrolls*') || request()->is('employee-my-advances*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true"
                                data-target="#submenu-attendance-payroll" aria-controls="submenu-attendance-payroll">
                                <i class="fas fa-fw fa-clock"></i>
                                Att. & Payroll
                            </a>
                            <div id="submenu-attendance-payroll"
                                class="collapse submenu {{ request()->is('employee-my-attendance*') || request()->is('employee-my-payrolls*') || request()->is('employee-my-advances*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('employee-my-attendance*') ? 'active' : '' }}"
                                            href="{{ route('employee.my_attendance.index') }}">My Attendance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('employee-my-payrolls*') ? 'active' : '' }}"
                                            href="{{ route('employee.my_payroll.index') }}">My Payroll</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('employee-my-advances*') ? 'active' : '' }}"
                                            href="{{ route('employee.my_advances') }}">My Advances</a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </li>

                    {{-- <li class="nav-item">
                        <a class="nav-link {{request()->is('admin-request*') ? "active" : ""}}" href="{{route('admin.request_index')}}">
                            <i class="fas fa-fw fa-paper-plane"></i>
                            {{translate('Requests')}}
                            @php
                                $unseen = \App\CustomerQuery::where('is_seen',0)->get()->count();
                            @endphp
                            @if ($unseen > 0)
                                <span class="badge badge-danger">{{$unseen}}</span>
                            @endif
                        </a>

                    </li> --}}
                    @if (Auth::guard('admin')->check())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin-reports*') ? 'active' : '' }}" href="#"
                                data-toggle="collapse" aria-expanded="true" data-target="#submenu-3"
                                aria-controls="submenu-3">
                                <i class="fas fa-file-excel"></i>
                                Reports
                            </a>
                            <div id="submenu-3"
                                class="collapse submenu {{ request()->is('admin-reports*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/employee-orders') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.employee_orders') }}">Employee Orders</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/order-status-p') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.order_status_p') }}">Order Status
                                            (Products)</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/orders-product') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.orders_product') }}">Orders (Products)</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/sales') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.sales', 'custom_range=today') }}">Sales</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/profit-loss') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.profit.loss', 'custom_range=today') }}">Profit/Loss</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-reports/employee-performance*') ? 'active' : '' }}"
                                            href="{{ route('admin.reports.employee_performance') }}">Performance
                                            Rank</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @elseif(Auth::guard('manager')->check())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('manager-reports*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true" data-target="#submenu-3"
                                aria-controls="submenu-3">
                                <i class="fas fa-file-excel"></i>
                                Reports
                            </a>
                            <div id="submenu-3"
                                class="collapse submenu {{ request()->is('manager-reports*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-reports/employee-orders') ? 'active' : '' }}"
                                            href="{{ route('manager.reports.employee_orders') }}">Employee Orders</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-reports/order-status-p') ? 'active' : '' }}"
                                            href="{{ route('manager.reports.order_status_p') }}">Order Status
                                            (Products)</a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-reports/orders-product') ? 'active' : '' }}"
                                            href="{{ route('manager.reports.orders_product') }}">Orders
                                            (Products)</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('manager-reports/orders-product') ? 'active' : '' }}"
                                            href="{{ route('manager.reports.orders_product') }}">Orders
                                            (Products)</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if (Auth::guard('admin')->check())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin-settings*') ? 'active' : '' }}"
                                href="#" data-toggle="collapse" aria-expanded="true" data-target="#submenu-2"
                                aria-controls="submenu-2">
                                <i class="fas fa-cogs"></i>
                                Settings
                            </a>
                            <div id="submenu-2"
                                class="collapse submenu {{ request()->is('admin-settings*') ? 'show' : '' }}"
                                style="">
                                <ul class="nav flex-column">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-sms') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.sms') }}">SMS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-whatsapp') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.whatsapp') }}">WhatsApp SMS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-web') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.web') }}">Web</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-color') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.color') }}">Color</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-page') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.page') }}">Page</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-pathao-api') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.pathao.api') }}">Pathao
                                            API</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-redx-api') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.redx.api') }}">RedX
                                            API</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-stead-fast-api') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.stead_fast.api') }}">Stead Fast API</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-carrybee-api') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.carrybee.api') }}">CarryBee API</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->is('admin-settings-notes') ? 'active' : '' }}"
                                            href="{{ route('admin.settings.notes') }}">Notes</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if (Auth::guard('admin')->check())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('admin-device-approvals*') ? 'active' : '' }}"
                                href="{{ route('admin.device.approvals') }}">
                                <i class="fas fa-fw fa-shield-alt"></i>
                                Device Approval
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
</div>
