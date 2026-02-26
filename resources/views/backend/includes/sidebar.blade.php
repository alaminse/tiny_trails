<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <!--begin::Brand Link-->
        <a href="./index.html" class="brand-link">
            <!--begin::Brand Image-->
            <img src="{{ asset('backend/img/f_icon.png') }}" alt="Tiny Trails Logo"
                class="brand-image opacity-75 shadow" />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">Tiny Trails</span>
            <!--end::Brand Text-->
        </a>
        <!--end::Brand Link-->
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation"
                aria-label="Main navigation" data-accordion="false" id="navigation">

                @can('dashboard')
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}"
                            class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-speedometer"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endcan

                {{-- ── BOH LIVE DASHBOARD ── --}}
                @can('boh-dashboard')
                <li class="nav-item">
                    <a href="{{ route('admin.boh.dashboard') }}"
                        class="nav-link {{ request()->routeIs('admin.boh.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-broadcast-pin"></i>
                        <p>BoH Live Dashboard</p>
                    </a>
                </li>
                @endcan

                @canany(['list-users', 'list-roles', 'list-permissions'])
                <li class="nav-item {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs(['admin.users.*', 'admin.roles.*', 'admin.permissions.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-people"></i>
                        <p>
                            User Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('admin.users.index') }}"
                                class="nav-link {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>BOH</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.roles.index') }}"
                                class="nav-link {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Roles</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.permissions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Permissions</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan

                @canany('list-kids')
                <li class="nav-item">
                    <a href="{{ route('admin.kids.index') }}"
                        class="nav-link {{ request()->routeIs('admin.kids.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-emoji-smile"></i>
                        <p>Kids Management</p>
                    </a>
                </li>
                @endcan

                @canany('list-drivers')
                <li class="nav-item">
                    <a href="{{ route('admin.drivers.index') }}"
                        class="nav-link {{ request()->routeIs('admin.drivers.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-badge"></i>
                        <p>Driver Management</p>
                    </a>
                </li>
                @endcan

                @canany('list-parents')
                <li class="nav-item">
                    <a href="{{ route('admin.parents.index') }}"
                        class="nav-link {{ request()->routeIs('admin.parents.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-person-heart"></i>
                        <p>Parent Management</p>
                    </a>
                </li>
                @endcan

                @canany('list-pickup')
                <li class="nav-item">
                    <a href="{{ route('admin.pickuptypes.index') }}"
                        class="nav-link {{ request()->routeIs('admin.pickuptypes.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-geo-alt"></i>
                        <p>PickUp Type</p>
                    </a>
                </li>
                @endcan

                @canany(['list-country', 'list-state', 'list-city'])
                <li class="nav-item {{ request()->routeIs(['admin.countries.*', 'admin.states.*', 'admin.cities.*']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs(['admin.countries.*', 'admin.states.*', 'admin.cities.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-globe"></i>
                        <p>
                            Location Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @canany('list-country')
                        <li class="nav-item">
                            <a href="{{ route('admin.countries.index') }}"
                                class="nav-link {{ request()->routeIs('admin.countries.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Country Lists</p>
                            </a>
                        </li>
                        @endcan
                        @canany('list-state')
                        <li class="nav-item">
                            <a href="{{ route('admin.states.index') }}"
                                class="nav-link {{ request()->routeIs('admin.states.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>State</p>
                            </a>
                        </li>
                        @endcan
                        @canany('list-city')
                        <li class="nav-item">
                            <a href="{{ route('admin.cities.index') }}"
                                class="nav-link {{ request()->routeIs('admin.cities.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>City</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @canany(['list-plan', 'list-subscription'])
                <li class="nav-item {{ request()->routeIs(['admin.plans.*', 'admin.subscriptions.*']) ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs(['admin.plans.*', 'admin.subscriptions.*']) ? 'active' : '' }}">
                        <i class="nav-icon bi bi-card-checklist"></i>
                        <p>
                            Subscription Management
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @canany('list-plan')
                        <li class="nav-item">
                            <a href="{{ route('admin.plans.index') }}"
                                class="nav-link {{ request()->routeIs('admin.plans.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Plan Lists</p>
                            </a>
                        </li>
                        @endcan
                        @canany('list-subscription')
                        <li class="nav-item">
                            <a href="{{ route('admin.subscriptions.index') }}"
                                class="nav-link {{ request()->routeIs('admin.subscriptions.index') ? 'active' : '' }}">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Subscription</p>
                            </a>
                        </li>
                        @endcan
                    </ul>
                </li>
                @endcan

                @can('list-rideassign')
                <li class="nav-item">
                    <a href="{{ route('admin.ride.assign.index') }}"
                        class="nav-link {{ request()->routeIs('admin.ride.assign.index') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-car-front"></i>
                        <p>Ride Assignment</p>
                    </a>
                </li>
                @endcan

                {{-- ══════════════════════════════════════
                     NEW MODULES
                ══════════════════════════════════════ --}}

                {{-- ── SHIFT BROADCASTS ── --}}
                {{-- @can('list-shift-broadcast')
                <li class="nav-item">
                    <a href="{{ route('admin.shift.broadcast.index') }}"
                        class="nav-link {{ request()->routeIs('admin.shift.broadcast.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-megaphone"></i>
                        <p>Shift Broadcasts</p>
                    </a>
                </li>
                @endcan --}}

                {{-- ── TIMESHEETS ── --}}
                @can('list-timesheets')
                <li class="nav-item">
                    <a href="{{ route('admin.timesheets.index') }}"
                        class="nav-link {{ request()->routeIs('admin.timesheets.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-clock-history"></i>
                        <p>Timesheets</p>
                    </a>
                </li>
                @endcan

                {{-- ── DRIVER WAGES ── --}}
                @can('list-driver-wages')
                <li class="nav-item">
                    <a href="{{ route('admin.driver.wages.index') }}"
                        class="nav-link {{ request()->routeIs('admin.driver.wages.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-cash-coin"></i>
                        <p>Driver Wages</p>
                    </a>
                </li>
                @endcan

                {{-- ── VEHICLE TYPES ── --}}
                @can('list-vehicle-types')
                <li class="nav-item">
                    <a href="{{ route('admin.vehicle.types.index') }}"
                        class="nav-link {{ request()->routeIs('admin.vehicle.types.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bus-front"></i>
                        <p>Vehicle Types</p>
                    </a>
                </li>
                @endcan
                <li class="nav-item">
                    <a href="{{ route('admin.driver.shifts.index') }}"
                        class="nav-link {{ request()->routeIs('admin.driver.shifts.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-bus-front"></i>
                        <p>Driver Shifts</p>
                    </a>
                </li>

                {{-- ── FACE VERIFICATION ── --}}
                {{-- @can('list-face-verification')
                <li class="nav-item">
                    <a href="{{ route('admin.face.verification.index') }}"
                        class="nav-link {{ request()->routeIs('admin.face.verification.*') ? 'active' : '' }}">
                        <i class="nav-icon bi bi-shield-lock"></i>
                        <p>Face Verification</p>
                    </a>
                </li>
                @endcan --}}

            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
</aside>
