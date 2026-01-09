<div class="dashboard-header">
    <nav class="navbar navbar-expand-lg bg-white fixed-top">
        <a class="navbar-brand"
           href="{{Auth::guard('admin')->check() ? route('admin.home') : (Auth::guard('manager')->check() ? route('manager.home') : (Auth::guard('employee')->check() ? route('employee.home') : ""))}}"><img
                width="160" style="max-height: 43px"
                src="{{$web_settings->get_logo ? asset($web_settings->get_logo->file_url) : asset('frontEnd/images/no_image.png')}}"
                alt=""></a>

        <a class="nav-link nav-user-img d-lg-none d-block" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                src="{{asset('/')}}backEnd/assets/images/default_avatar.jpg" alt="" class="user-avatar-md rounded-circle"></a>
        <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
            <div class="nav-user-info">
                <h5 class="mb-0 text-white nav-user-name">
                    {{Auth::user()->name}}
                </h5>
            </div>
            @if(Auth::guard('admin')->check())
                <a href="{{route('clear.cache')}}" class="dropdown-item">Clear Cache</a>
            @endif
            <a class="dropdown-item"
               href="{{Auth::guard('admin')->check() ? route('admin.change_pass') : (Auth::guard('manager')->check() ? route('manager.change_pass') : (Auth::guard('employee')->check() ? route('employee.change_pass') : ""))}}"><i
                    class="fas fa-key mr-2"></i>Change Password</a>
            <a class="dropdown-item"
               href="{{Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : ""))}}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                    class="fas fa-power-off mr-2"></i>
                Logout
            </a>
            <form id="logout-form"
                  action="{{Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : ""))}}"
                  method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        <div class="collapse navbar-collapse " id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto navbar-right-top">
                @if(Auth::guard('admin')->check())
                    <li>
                        <a href="{{route('clear.cache')}}" class="btn btn-outline-danger btn-sm my-3 mr-4">Clear Cache</a>
                    </li>
                @endif
                <li class="nav-item dropdown nav-user">
                    <a class="nav-link nav-user-img" href="#" id="navbarDropdownMenuLink2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
                            src="{{asset('/')}}backEnd/assets/images/default_avatar.jpg" alt="" class="user-avatar-md rounded-circle"></a>
                    <div class="dropdown-menu dropdown-menu-right nav-user-dropdown" aria-labelledby="navbarDropdownMenuLink2">
                        <div class="nav-user-info">
                            <h5 class="mb-0 text-white nav-user-name">
                                @if(Auth::guard('admin')->check())
                                    {{Auth::guard('admin')->user()->name}}
                                @elseif(Auth::guard('manager')->check())
                                    {{Auth::guard('manager')->user()->name}}
                                @elseif(Auth::guard('employee')->check())
                                    {{Auth::guard('employee')->user()->name}}
                                @endif
                            </h5>
                        </div>
                        <a class="dropdown-item"
                           href="{{Auth::guard('admin')->check() ? route('admin.change_pass') : (Auth::guard('manager')->check() ? route('manager.change_pass') : (Auth::guard('employee')->check() ? route('employee.change_pass') : ""))}}"><i
                                class="fas fa-key mr-2"></i>Change Password</a>
                        <a class="dropdown-item"
                           href="{{Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : ""))}}"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i
                                class="fas fa-power-off mr-2"></i>
                            Logout
                        </a>
                        <form id="logout-form"
                              action="{{Auth::guard('admin')->check() ? route('admin.logout') : (Auth::guard('manager')->check() ? route('manager.logout') : (Auth::guard('employee')->check() ? route('employee.logout') : ""))}}"
                              method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</div>
