<div class="dashboard-header">
    @php
        $attendanceToggleRoute = null;
        $attendanceInitialState = [
            'is_checked_in' => false,
            'is_checked_out' => false,
            'allow_self_checkout' => true,
            'check_in' => null,
            'check_out' => null,
        ];

        if (Auth::guard('admin')->check()) {
            $attendanceToggleRoute = route('admin.my_attendance.toggle');
        } elseif (Auth::guard('manager')->check()) {
            $attendanceToggleRoute = route('manager.my_attendance.toggle');
        } elseif (Auth::guard('employee')->check()) {
            $attendanceToggleRoute = route('employee.my_attendance.toggle');
        }

        if ($attendanceToggleRoute) {
            $staffUser = app(\App\Services\StaffUserResolver::class)->resolveAuthenticatedStaffUser();

            if ($staffUser) {
                $todayAttendance = $staffUser->todayAttendance();
                $settings = \App\Models\PayrollSetting::current();

                $attendanceInitialState = [
                    'is_checked_in' => (bool) $todayAttendance?->check_in,
                    'is_checked_out' => (bool) $todayAttendance?->check_out,
                    'allow_self_checkout' => (bool) $settings->allow_self_checkout,
                    'check_in' => $todayAttendance?->check_in
                        ? \Carbon\Carbon::parse($todayAttendance->check_in)->format('h:i A')
                        : null,
                    'check_out' => $todayAttendance?->check_out
                        ? \Carbon\Carbon::parse($todayAttendance->check_out)->format('h:i A')
                        : null,
                ];
            }
        }
    @endphp
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
                @if ($attendanceToggleRoute)
                    <li class="nav-item d-flex align-items-center mr-2 pr-2">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="attendance-switch">
                            <label class="custom-control-label" for="attendance-switch"
                                id="attendance-switch-label">Check In</label>
                        </div>
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
                    <a class="nav-link nav-user-img mr-1 p-2" href="#" id="navbarDropdownMenuLink2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img
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

@if ($attendanceToggleRoute)
    <script>
        (function() {
            const switchInput = document.getElementById('attendance-switch');
            const switchLabel = document.getElementById('attendance-switch-label');

            if (!switchInput || !switchLabel) {
                return;
            }

            const toggleUrl = @json($attendanceToggleRoute);
            let state = @json($attendanceInitialState);

            const updateSwitchUI = () => {
                if (state.is_checked_out) {
                    switchInput.checked = true;
                    switchInput.disabled = true;
                    switchLabel.innerText = state.check_out ? `Checked Out (${state.check_out})` : 'Checked Out';

                    return;
                }

                if (state.is_checked_in) {
                    switchInput.checked = true;
                    switchInput.disabled = !state.allow_self_checkout;
                    switchLabel.innerText = state.check_in ? `Checked In (${state.check_in})` : 'Checked In';

                    return;
                }

                switchInput.checked = false;
                switchInput.disabled = false;
                switchLabel.innerText = 'Check In';
            };

            switchInput.addEventListener('change', async (event) => {
                event.preventDefault();

                if (state.is_checked_in && !state.allow_self_checkout) {
                    updateSwitchUI();

                    return;
                }

                try {
                    const response = await fetch(toggleUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '',
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json();
                    state = {
                        is_checked_in: Boolean(data.is_checked_in),
                        is_checked_out: Boolean(data.is_checked_out),
                        allow_self_checkout: Boolean(data.allow_self_checkout),
                        check_in: data.check_in || null,
                        check_out: data.check_out || null,
                    };
                    updateSwitchUI();
                } catch (error) {
                    console.error(error);
                    updateSwitchUI();
                }
            });

            updateSwitchUI();
        })();
    </script>
@endif
