<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('backend/img/f_icon.png') }}" alt="Tiny Trails Logo"
                class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">Tiny Trails</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">

                {{-- ── DASHBOARD ──────────────────────────────────────── --}}
                @can('view-dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                @endcan

                {{-- ── BOH LIVE DASHBOARD ─────────────────────────────── --}}
                @can('view-boh-dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.boh.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.boh.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-broadcast-pin"></i>
                        <p>BoH Live Dashboard</p>
                    </a>
                </li>
                @endcan

                {{-- ── USER MANAGEMENT ────────────────────────────────── --}}
                @canany(['list-users', 'list-roles', 'list-permissions'])
                <li class="nav-item {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>
                            User Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('list-users')
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>BOH Users</p>
                            </a>
                        </li>
                        @endcan

                        @can('list-roles')
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        @endcan

                        @can('list-permissions')
                        <li class="nav-item">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- ── KIDS MANAGEMENT ─────────────────────────────────── --}}
                @can('list-kids')
                <li class="nav-item">
                    <a href="{{ route('admin.kids.index') }}"
                        class="nav-link {{ request()->routeIs('admin.kids.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-emoji-smile"></i>
                        <p>Kids Management</p>
                    </a>
                </li>
                @endcan

                {{-- ── DRIVER MANAGEMENT ───────────────────────────────── --}}
                @can('list-drivers')
                <li class="nav-item">
                    <a href="{{ route('admin.drivers.index') }}"
                        class="nav-link {{ request()->routeIs('admin.drivers.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Driver Management</p>
                    </a>
                </li>
                @endcan

                {{-- ── PARENT MANAGEMENT ───────────────────────────────── --}}
                @can('list-parents')
                <li class="nav-item">
                    <a href="{{ route('admin.parents.index') }}"
                        class="nav-link {{ request()->routeIs('admin.parents.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-heart"></i>
                        <p>Parent Management</p>
                    </a>
                </li>
                @endcan

                {{-- ── PICKUP TYPE ──────────────────────────────────────── --}}
                @can('list-pickup')
                <li class="nav-item">
                    <a href="{{ route('admin.pickuptypes.index') }}"
                        class="nav-link {{ request()->routeIs('admin.pickuptypes.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-geo-alt"></i>
                        <p>PickUp Type</p>
                    </a>
                </li>
                @endcan

                {{-- ── LOCATION MANAGEMENT ────────────────────────────── --}}
                @canany(['list-country', 'list-state', 'list-city'])
                <li class="nav-item {{ request()->routeIs(['admin.countries.*', 'admin.states.*', 'admin.cities.*']) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs(['admin.countries.*', 'admin.states.*', 'admin.cities.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-globe"></i>
                        <p>
                            Location Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('list-country')
                        <li class="nav-item">
                            <a href="{{ route('admin.countries.index') }}"
                                class="nav-link {{ request()->routeIs('admin.countries.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Country Lists</p>
                            </a>
                        </li>
                        @endcan

                        @can('list-state')
                        <li class="nav-item">
                            <a href="{{ route('admin.states.index') }}"
                                class="nav-link {{ request()->routeIs('admin.states.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>State</p>
                            </a>
                        </li>
                        @endcan

                        @can('list-city')
                        <li class="nav-item">
                            <a href="{{ route('admin.cities.index') }}"
                                class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>City</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- ── SUBSCRIPTION MANAGEMENT ─────────────────────────── --}}
                @canany(['list-plan', 'list-subscription'])
                <li class="nav-item {{ request()->routeIs(['admin.plans.*', 'admin.subscriptions.*']) ? 'menu-open' : '' }}">
                    <a href="#"
                        class="nav-link {{ request()->routeIs(['admin.plans.*', 'admin.subscriptions.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-card-checklist"></i>
                        <p>
                            Subscription Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('list-plan')
                        <li class="nav-item">
                            <a href="{{ route('admin.plans.index') }}"
                                class="nav-link {{ request()->routeIs('admin.plans.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Plan Lists</p>
                            </a>
                        </li>
                        @endcan

                        @can('list-subscription')
                        <li class="nav-item">
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Subscription</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                {{-- ── RIDE ASSIGNMENT ─────────────────────────────────── --}}
                @can('list-rideassign')
                <li class="nav-item">
                    <a href="{{ route('admin.ride.assign.index') }}"
                        class="nav-link {{ request()->routeIs('admin.ride.assign.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-car-front"></i>
                        <p>Ride Assignment</p>
                    </a>
                </li>
                @endcan

                {{-- ── TIMESHEETS ───────────────────────────────────────── --}}
                @can('list-timesheets')
                <li class="nav-item">
                    <a href="{{ route('admin.timesheets.index') }}"
                        class="nav-link {{ request()->routeIs('admin.timesheets.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-history"></i>
                        <p>Timesheets</p>
                    </a>
                </li>
                @endcan

                {{-- ── ATTENDANCE ───────────────────────────────────────── --}}
                @can('list-attendance')
                <li class="nav-item">
                    <a href="{{ route('admin.attendance.index') }}"
                        class="nav-link {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-check"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                @endcan

                {{-- ── DRIVER WAGES ─────────────────────────────────────── --}}
                @can('list-driver-wages')
                <li class="nav-item">
                    <a href="{{ route('admin.driver.wages.index') }}"
                        class="nav-link {{ request()->routeIs('admin.driver.wages.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-cash-coin"></i>
                        <p>Driver Wages</p>
                    </a>
                </li>
                @endcan

                {{-- ── VEHICLE TYPES ────────────────────────────────────── --}}
                @can('list-vehicle-types')
                <li class="nav-item">
                    <a href="{{ route('admin.vehicle.types.index') }}"
                        class="nav-link {{ request()->routeIs('admin.vehicle.types.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bus-front"></i>
                        <p>Vehicle Types</p>
                    </a>
                </li>
                @endcan

                {{-- ── DRIVER SHIFTS ────────────────────────────────────── --}}
                @can('list-driver-shifts')
                <li class="nav-item">
                    <a href="{{ route('admin.driver.shifts.index') }}"
                        class="nav-link {{ request()->routeIs('admin.driver.shifts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-calendar2-week"></i>
                        <p>Driver Shifts</p>
                    </a>
                </li>
                @endcan

                {{-- ── SHIFT BROADCASTS ─────────────────────────────────── --}}
                @can('list-shift-broadcast')
                <li class="nav-item">
                    <a href="{{ route('admin.shift.broadcast.index') }}"
                        class="nav-link {{ request()->routeIs('admin.shift.broadcast.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-megaphone"></i>
                        <p>Shift Broadcasts</p>
                    </a>
                </li>
                @endcan

                {{-- ── FACE VERIFICATION ────────────────────────────────── --}}
                @can('list-face-verification')
                <li class="nav-item">
                    <a href="{{ route('admin.face.verification.index') }}"
                        class="nav-link {{ request()->routeIs('admin.face.verification.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>Face Verification</p>
                    </a>
                </li>
                @endcan

                {{-- ── TRASH (Super-Admin only) ─────────────────────────── --}}
                @can('view-trash')
                {{-- <li class="nav-item">
                    <a href="{{ route('admin.trash.index') }}"
                        class="nav-link {{ request()->routeIs('admin.trash.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-trash3"></i>
                        <p>Trash</p>
                    </a>
                </li> --}}
                @endcan

                {{-- ── TWILIO CREDENTIALS ──────────────────────────────── --}}
                @canany(['view-twilio-credentials', 'manage-twilio-credentials'])
                <li class="nav-item">
                    <a href="{{ route('admin.twilio.index') }}"
                        class="nav-link {{ request()->routeIs('admin.twilio.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-phone"></i>
                        <p>Twilio Credentials</p>
                    </a>
                </li>
                @endcanany

            </ul>
        </nav>
    </div>
</aside>
