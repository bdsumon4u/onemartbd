<div class="dashboard-header">
    <nav class="navbar navbar-expand-lg bg-white fixed-top">
        <a class="navbar-brand p-2"
            href="{{ Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : '')) }}"><img
                width="160" style="max-height: 43px"
                src="{{ $web_settings->get_logo ? asset($web_settings->get_logo->file_url) : asset('frontEnd/images/no_image.png') }}"
                alt=""></a>

        <div class="push-toggles-mobile d-lg-none d-flex align-items-center mr-2" style="gap: 3px;">
            <button type="button" id="btn-toggle-notification-mobile" class="btn btn-sm" title="Toggle Notifications"
                onclick="PushNotificationManager.toggleNotification()">
                <i class="fas fa-bell" id="icon-notification-mobile"></i>
            </button>
            <button type="button" id="btn-toggle-sound-mobile" class="btn btn-sm" title="Toggle Sound"
                onclick="PushNotificationManager.toggleSound()">
                <i class="fas fa-volume-up" id="icon-sound-mobile"></i>
            </button>
        </div>
        <a class="nav-link nav-user-img d-lg-none d-block p-2" href="#" id="navbarDropdownMenuLink2"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                src="{{ asset('/') }}backEnd/assets/images/default_avatar.jpg" alt=""
                class="user-avatar-md rounded-circle"></a>
        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
            <div class="nav-user-info">
                <h5 class="mb-0 text-white nav-user-name">
                    {{ Auth::user()->name }}
                </h5>
            </div>
            @if (Auth::guard('admin')->check())
                <a href="{{ route('clear.cache') }}" class="dropdown-item">Clear Cache</a>
            @endif
            <a class="dropdown-item"
                href="{{ Auth::guard('admin')->check() ? route('admin.change_pass') : (Auth::guard('manager')->check() ? route('manager.change_pass') : (Auth::guard('employee')->check() ? route('employee.change_pass') : '')) }}"><i
                    class="fas fa-key mr-2"></i>Change Password</a>
            <a class="dropdown-item"
                href="{{ Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : '')) }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                    class="fas fa-power-off mr-2"></i>
                Logout
            </a>
            <form id="logout-form"
                action="{{ Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : '')) }}"
                method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto navbar-right-top">
                @if (Auth::guard('admin')->check())
                    <li>
                        <a href="{{ route('clear.cache') }}" class="btn btn-outline-danger btn-sm my-3 mr-4">Clear
                            Cache</a>
                    </li>
                @endif
                <li class="nav-item d-flex align-items-center mr-1">
                    <div class="push-toggles d-flex align-items-center mr-1" style="gap: 10px;">
                        <button type="button" id="btn-toggle-notification" class="btn btn-sm"
                            title="Toggle Notifications" onclick="PushNotificationManager.toggleNotification()">
                            <i class="fas fa-bell" id="icon-notification"></i>
                        </button>
                        <button type="button" id="btn-toggle-sound" class="btn btn-sm" title="Toggle Sound"
                            onclick="PushNotificationManager.toggleSound()">
                            <i class="fas fa-volume-up" id="icon-sound"></i>
                        </button>
                    </div>
                </li>
                <li class="nav-item dropdown nav-user d-flex align-items-center">
                    <a class="nav-link nav-user-img mr-1 p-2" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"><img
                            src="{{ asset('/') }}backEnd/assets/images/default_avatar.jpg" alt=""
                            class="user-avatar-md rounded-circle"></a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown"
                        aria-labelledby="navbarDropdownMenuLink2">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name">
                                @if (Auth::guard('admin')->check())
                                    {{ Auth::guard('admin')->user()->name }}
                                @elseif(Auth::guard('manager')->check())
                                    {{ Auth::guard('manager')->user()->name }}
                                @elseif(Auth::guard('employee')->check())
                                    {{ Auth::guard('employee')->user()->name }}
                                @endif
                            </h5>
                        </div>
                        <a class="dropdown-item"
                            href="{{ Auth::guard('admin')->check() ? route('admin.change_pass') : (Auth::guard('manager')->check() ? route('manager.change_pass') : (Auth::guard('employee')->check() ? route('employee.change_pass') : '')) }}"><i
                                class="fas fa-key mr-2"></i>Change Password</a>
                        <a class="dropdown-item"
                            href="{{ Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : '')) }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fas fa-power-off mr-2"></i>
                            Logout
                        </a>
                        <form id="logout-form"
                            action="{{ Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : '')) }}"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
