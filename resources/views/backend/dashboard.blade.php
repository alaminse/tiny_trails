@extends('backend.app')
@section('title', 'Dashboard')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous" />
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Dashboard', 'subTitle' => null])

    <div class="app-content">
        <div class="container-fluid">

            {{-- ══════════════════════════════════════════════════════════
                 ROW 1 — Primary stat cards
                 Variables differ by role — use ?? 0 fallback so every
                 role-scoped view is safe.
            ══════════════════════════════════════════════════════════ --}}
            <div class="row g-3 mb-3">

                {{-- Parents --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-primary shadow-sm">
                            <i class="bi bi-people-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Parents</span>
                            <span class="info-box-number">{{ $totalParents ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Active Parents --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-success shadow-sm">
                            <i class="bi bi-person-check-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Parents</span>
                            <span class="info-box-number">{{ $activeParents ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Kids --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-warning shadow-sm">
                            <i class="bi bi-person-hearts"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Kids</span>
                            <span class="info-box-number">{{ $totalKids ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- New Parents 30d --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-danger shadow-sm">
                            <i class="bi bi-person-plus-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">New (30 Days)</span>
                            <span class="info-box-number">{{ $newParents30d ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Drivers --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-info shadow-sm">
                            <i class="bi bi-person-badge-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Drivers</span>
                            <span class="info-box-number">{{ $totalDrivers ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Active Subscriptions --}}
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-secondary shadow-sm">
                            <i class="bi bi-file-earmark-check-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Subs</span>
                            <span class="info-box-number">{{ $activeSubscriptions ?? 0 }}</span>
                        </div>
                    </div>
                </div>

            </div>{{-- /ROW 1 --}}

            {{-- ══════════════════════════════════════════════════════════
                 ROW 2 — Secondary stat cards (Super-Admin / BOH-IT only)
            ══════════════════════════════════════════════════════════ --}}
            @if(isset($totalRides) || isset($todayRides))
            <div class="row g-3 mb-3">

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-dark shadow-sm">
                            <i class="bi bi-car-front-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Rides</span>
                            <span class="info-box-number">{{ $totalRides ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon shadow-sm" style="background:#6f42c1;color:#fff">
                            <i class="bi bi-calendar-check-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Today's Rides</span>
                            <span class="info-box-number">{{ $todayRides ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon shadow-sm" style="background:#fd7e14;color:#fff">
                            <i class="bi bi-hourglass-split"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Assigns</span>
                            <span class="info-box-number">{{ $pendingAssignments ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-warning shadow-sm">
                            <i class="bi bi-clock-history"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Pending Timesheets</span>
                            <span class="info-box-number">{{ $pendingTimesheets ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-success shadow-sm">
                            <i class="bi bi-person-check"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Present Today</span>
                            <span class="info-box-number">{{ $presentToday ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-danger shadow-sm">
                            <i class="bi bi-camera-video-off-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Face Verify Pending</span>
                            <span class="info-box-number">{{ $pendingVerifications ?? 0 }}</span>
                        </div>
                    </div>
                </div>

            </div>{{-- /ROW 2 --}}
            @endif

            {{-- ══════════════════════════════════════════════════════════
                 ROW 3 — Revenue cards (Super-Admin / BOH-Marketing only)
            ══════════════════════════════════════════════════════════ --}}
            @if(isset($monthlyRevenue) || isset($totalRevenue))
            <div class="row g-3 mb-3">

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-success shadow-sm">
                            <i class="bi bi-currency-dollar"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Monthly Revenue</span>
                            <span class="info-box-number">${{ number_format($monthlyRevenue ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-primary shadow-sm">
                            <i class="bi bi-bank2"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Revenue</span>
                            <span class="info-box-number">${{ number_format($totalRevenue ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-warning shadow-sm">
                            <i class="bi bi-clock-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Expiring (7 Days)</span>
                            <span class="info-box-number">{{ $expiringSoon ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                @isset($totalPlans)
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-info shadow-sm">
                            <i class="bi bi-list-ul"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Active Plans</span>
                            <span class="info-box-number">{{ $activePlans ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($openBroadcasts)
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon shadow-sm" style="background:#17a2b8;color:#fff">
                            <i class="bi bi-broadcast"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Open Broadcasts</span>
                            <span class="info-box-number">{{ $openBroadcasts }}</span>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($shiftsToday)
                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                    <div class="info-box mb-0">
                        <span class="info-box-icon text-bg-dark shadow-sm">
                            <i class="bi bi-calendar2-week-fill"></i>
                        </span>
                        <div class="info-box-content">
                            <span class="info-box-text">Shifts Today</span>
                            <span class="info-box-number">{{ $shiftsToday }}</span>
                        </div>
                    </div>
                </div>
                @endisset

            </div>{{-- /ROW 3 --}}
            @endif

            {{-- ══════════════════════════════════════════════════════════
                 CHARTS ROW
            ══════════════════════════════════════════════════════════ --}}
            <div class="row g-3 mb-3">

                {{-- Registration / Subscription trend --}}
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">
                                @isset($registrationTrend) Monthly Parent Registrations
                                @elseif(isset($subscriptionTrend)) Subscription Growth
                                @else Trend
                                @endisset
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="trend-chart" style="min-height:280px"></div>
                        </div>
                    </div>
                </div>

                {{-- Gender / Plan distribution --}}
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                @isset($genderStats) Gender Distribution
                                @elseif(isset($planDistribution)) Plan Distribution
                                @else Distribution
                                @endisset
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="donut-chart" style="min-height:280px"></div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Revenue + Ride Trend (Super-Admin / BOH-IT / Marketing) --}}
            @if(isset($revenueTrend) || isset($rideTrend))
            <div class="row g-3 mb-3">

                @isset($revenueTrend)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Revenue Trend (6 months)</h5>
                        </div>
                        <div class="card-body">
                            <div id="revenue-chart" style="min-height:240px"></div>
                        </div>
                    </div>
                </div>
                @endisset

                @isset($rideTrend)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Ride Trend (30 days)</h5>
                        </div>
                        <div class="card-body">
                            <div id="ride-chart" style="min-height:240px"></div>
                        </div>
                    </div>
                </div>
                @endisset

            </div>
            @endif

            {{-- Driver Shift Stats (Super-Admin / BOH-IT) --}}
            @isset($driverShiftStats)
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Driver Shift Capacity — This Week</h5>
                        </div>
                        <div class="card-body">
                            <div id="shift-chart" style="min-height:220px"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endisset

            {{-- Onboarding Funnel (BOH-Sales) --}}
            @isset($onboardingFunnel)
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Onboarding Funnel</h5>
                        </div>
                        <div class="card-body">
                            <div id="funnel-chart" style="min-height:240px"></div>
                        </div>
                    </div>
                </div>

                @isset($kidsWithoutSub)
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Kids Without Subscription</h5>
                        </div>
                        <div class="card-body d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <div class="display-3 fw-bold text-danger">{{ $kidsWithoutSub }}</div>
                                <p class="text-muted mt-2">Kids not yet assigned to any active plan</p>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-primary mt-1">
                                    <i class="bi bi-plus-circle"></i> Assign Subscription
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endisset
            </div>
            @endisset

            {{-- ══════════════════════════════════════════════════════════
                 TABLES ROW
            ══════════════════════════════════════════════════════════ --}}
            <div class="row g-3 mb-3">

                {{-- Country Stats --}}
                @isset($countryStats)
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Top 5 Countries</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover table-sm mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Country</th>
                                        <th class="text-end pe-3">Parents</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($countryStats as $stat)
                                        <tr>
                                            <td class="ps-3">{{ $stat->country }}</td>
                                            <td class="text-end pe-3">
                                                <span class="badge bg-primary rounded-pill">{{ $stat->count }}</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endisset

                {{-- Recent Parents --}}
                @isset($recentParents)
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Recent Registrations</h5>
                            <a href="{{ route('admin.parents.index') }}" class="btn btn-sm btn-outline-primary">
                                View All
                            </a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Status</th>
                                            <th class="pe-3">Joined</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentParents as $parent)
                                            <tr>
                                                <td class="ps-3 fw-semibold">
                                                    {{ $parent->first_name }} {{ $parent->last_name }}
                                                </td>
                                                <td class="text-muted small">{{ $parent->email }}</td>
                                                <td class="text-muted small">{{ $parent->phone ?? '—' }}</td>
                                                <td>
                                                    <span class="badge {{ $parent->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                                        {{ ucfirst($parent->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-3 text-muted small">
                                                    {{ \Carbon\Carbon::parse($parent->created_at)->format('d M Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="5" class="text-center text-muted py-3">No registrations yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endisset

            </div>{{-- /TABLES ROW --}}

            {{-- Recent Rides --}}
            @isset($recentRides)
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Recent Rides</h5>
                            <a href="{{ route('admin.ride.assign.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">#</th>
                                            <th>Type</th>
                                            <th>Driver</th>
                                            <th>Parent</th>
                                            <th>Date</th>
                                            <th class="pe-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentRides as $ride)
                                            <tr>
                                                <td class="ps-3 text-muted small">{{ $ride->id }}</td>
                                                <td>
                                                    <span class="badge bg-info text-dark">
                                                        {{ ucfirst(str_replace('_', ' ', $ride->ride_type)) }}
                                                    </span>
                                                </td>
                                                <td class="small">{{ $ride->driver_first ?? '—' }} {{ $ride->driver_last ?? '' }}</td>
                                                <td class="small">{{ $ride->parent_first ?? '—' }} {{ $ride->parent_last ?? '' }}</td>
                                                <td class="text-muted small">
                                                    {{ \Carbon\Carbon::parse($ride->date)->format('d M Y') }}
                                                </td>
                                                <td class="pe-3">
                                                    @php
                                                        $rideStatusClass = match($ride->status) {
                                                            'completed'   => 'bg-success',
                                                            'cancelled'   => 'bg-danger',
                                                            'in_progress' => 'bg-warning text-dark',
                                                            default       => 'bg-secondary',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $rideStatusClass }} rounded-pill">
                                                        {{ ucfirst(str_replace('_', ' ', $ride->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="6" class="text-center text-muted py-3">No rides yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endisset

            {{-- Recent Subscriptions --}}
            @isset($recentSubscriptions)
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0">Recent Subscriptions</h5>
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-3">User</th>
                                            <th>Plan</th>
                                            <th>Status</th>
                                            <th class="pe-3">Ends At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentSubscriptions as $sub)
                                            <tr>
                                                <td class="ps-3 fw-semibold small">
                                                    {{ $sub->first_name ?? '—' }} {{ $sub->last_name ?? '' }}
                                                </td>
                                                <td class="small">{{ $sub->plan_name ?? '—' }}</td>
                                                <td>
                                                    <span class="badge {{ $sub->status === 'active' ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                                                        {{ ucfirst($sub->status) }}
                                                    </span>
                                                </td>
                                                <td class="pe-3 text-muted small">
                                                    {{ $sub->ends_at ? \Carbon\Carbon::parse($sub->ends_at)->format('d M Y') : '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted py-3">No subscriptions yet</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endisset

        </div>{{-- /.container-fluid --}}
    </div>{{-- /.app-content --}}
@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>

<script>
(function () {
    'use strict';

    // ── Helpers ────────────────────────────────────────────────────────
    function monthLabel(ym) {
        // "2025-09" → "Sep 2025"
        try {
            const d = new Date(ym + '-01');
            return d.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        } catch { return ym; }
    }

    function statusColor(status) {
        return { completed: '#1cc88a', cancelled: '#e74a3b', in_progress: '#ffc107' }[status] ?? '#858796';
    }

    // ── Trend chart (registrations OR subscriptions) ───────────────────
    @if(isset($registrationTrend))
    (function buildTrendChart() {
        const data = @json($registrationTrend);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#trend-chart'), {
            series: [{ name: 'Registrations', data: data.map(r => r.count) }],
            chart:  { height: 280, type: 'area', toolbar: { show: false } },
            colors: ['#4e73df'],
            stroke: { curve: 'smooth', width: 2 },
            fill:   { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
            dataLabels: { enabled: false },
            xaxis:  { categories: data.map(r => monthLabel(r.month)) },
            tooltip: { y: { formatter: v => v + ' registrations' } },
        }).render();
    })();
    @elseif(isset($subscriptionTrend))
    (function buildTrendChart() {
        const data = @json($subscriptionTrend);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#trend-chart'), {
            series: [{ name: 'Subscriptions', data: data.map(r => r.count) }],
            chart:  { height: 280, type: 'area', toolbar: { show: false } },
            colors: ['#1cc88a'],
            stroke: { curve: 'smooth', width: 2 },
            fill:   { type: 'gradient', gradient: { opacityFrom: 0.6, opacityTo: 0.1 } },
            dataLabels: { enabled: false },
            xaxis:  { categories: data.map(r => monthLabel(r.month)) },
            tooltip: { y: { formatter: v => v + ' subscriptions' } },
        }).render();
    })();
    @endif

    // ── Donut chart (gender OR plan distribution) ──────────────────────
    @if(isset($genderStats))
    (function buildDonutChart() {
        const data = @json($genderStats);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#donut-chart'), {
            series: data.map(r => r.count),
            labels: data.map(r => r.gender.charAt(0).toUpperCase() + r.gender.slice(1)),
            chart:  { type: 'donut', height: 280 },
            colors: ['#4e73df', '#d63384', '#6f42c1', '#20c997'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
        }).render();
    })();
    @elseif(isset($planDistribution))
    (function buildDonutChart() {
        const data = @json($planDistribution);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#donut-chart'), {
            series: data.map(r => r.count),
            labels: data.map(r => r.plan),
            chart:  { type: 'donut', height: 280 },
            colors: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
            legend: { position: 'bottom' },
            dataLabels: { enabled: true },
        }).render();
    })();
    @endif

    // ── Revenue trend ──────────────────────────────────────────────────
    @isset($revenueTrend)
    (function buildRevenueChart() {
        const data = @json($revenueTrend);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#revenue-chart'), {
            series: [
                { name: 'Revenue',    data: data.map(r => parseFloat(r.revenue   || 0).toFixed(2)) },
                { name: 'Principal',  data: data.map(r => parseFloat(r.principal || 0).toFixed(2)) },
            ],
            chart:  { height: 240, type: 'bar', toolbar: { show: false } },
            colors: ['#1cc88a', '#4e73df'],
            xaxis:  { categories: data.map(r => monthLabel(r.month)) },
            yaxis:  { labels: { formatter: v => '$' + Number(v).toLocaleString() } },
            tooltip:{ y: { formatter: v => '$' + Number(v).toLocaleString() } },
            dataLabels: { enabled: false },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
        }).render();
    })();
    @endisset

    // ── Ride trend ─────────────────────────────────────────────────────
    @isset($rideTrend)
    (function buildRideChart() {
        const data = @json($rideTrend);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#ride-chart'), {
            series: [
                { name: 'Total',     data: data.map(r => r.count) },
                { name: 'Completed', data: data.map(r => r.completed) },
                { name: 'Cancelled', data: data.map(r => r.cancelled) },
            ],
            chart:  { height: 240, type: 'line', toolbar: { show: false } },
            colors: ['#4e73df', '#1cc88a', '#e74a3b'],
            stroke: { curve: 'smooth', width: [2, 2, 2] },
            xaxis:  { categories: data.map(r => r.date), labels: { rotate: -45, style: { fontSize: '10px' } } },
            legend: { position: 'top' },
            dataLabels: { enabled: false },
        }).render();
    })();
    @endisset

    // ── Driver shift capacity chart ─────────────────────────────────────
    @isset($driverShiftStats)
    (function buildShiftChart() {
        const data = @json($driverShiftStats);
        if (!data.length) return;
        new ApexCharts(document.querySelector('#shift-chart'), {
            series: [
                { name: 'Max Seats',    data: data.map(r => r.capacity) },
                { name: 'Booked Seats', data: data.map(r => r.booked) },
            ],
            chart:  { height: 220, type: 'bar', toolbar: { show: false } },
            colors: ['#36b9cc', '#f6c23e'],
            xaxis:  { categories: data.map(r => r.date) },
            plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
            dataLabels: { enabled: false },
        }).render();
    })();
    @endisset

    // ── Onboarding funnel ──────────────────────────────────────────────
    @isset($onboardingFunnel)
    (function buildFunnelChart() {
        const data = @json($onboardingFunnel);
        new ApexCharts(document.querySelector('#funnel-chart'), {
            series: [{ name: 'Count', data: data.map(r => r.count) }],
            chart:  { height: 240, type: 'bar', toolbar: { show: false } },
            colors: ['#4e73df'],
            plotOptions: { bar: { borderRadius: 4, horizontal: true } },
            xaxis:  { categories: data.map(r => r.stage) },
            dataLabels: { enabled: true },
            tooltip: { y: { formatter: v => v + ' users' } },
        }).render();
    })();
    @endisset

})();
</script>
@endpush
