@extends('backend.app')
@section('title', 'BoH Live Dashboard')

@section('css')
<style>
    .driver-status-card {
        border-left: 4px solid #dee2e6;
        transition: all 0.2s;
    }
    .driver-status-card.on-trip    { border-left-color: #0d6efd; }
    .driver-status-card.ready      { border-left-color: #198754; }
    .driver-status-card.delayed    { border-left-color: #dc3545; }
    .driver-status-card.next-batch { border-left-color: #0dcaf0; }
    .driver-status-card.offline    { border-left-color: #6c757d; opacity: 0.7; }

    .capacity-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
    .capacity-fill { height: 100%; border-radius: 4px; transition: width 0.3s; }
    .capacity-fill.low  { background: #198754; }
    .capacity-fill.mid  { background: #ffc107; }
    .capacity-fill.full { background: #dc3545; }

    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #198754;
        font-weight: 600;
    }
    .live-dot {
        width: 8px; height: 8px;
        background: #198754;
        border-radius: 50%;
        animation: livePulse 1.5s infinite;
    }
    @keyframes livePulse {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:0.4; transform:scale(1.4); }
    }

    .timeline { position: relative; padding-left: 20px; }
    .timeline::before {
        content: '';
        position: absolute;
        left: 6px; top: 0; bottom: 0;
        width: 1px; background: #dee2e6;
    }
    .timeline-item { position: relative; padding-bottom: 16px; }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -17px; top: 5px;
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #0d6efd;
        border: 2px solid #fff;
    }
    .timeline-item.success::before { background: #198754; }
    .timeline-item.warning::before { background: #ffc107; }
    .timeline-item.danger::before  { background: #dc3545; }
    .timeline-time { font-size: 11px; color: #6c757d; font-family: monospace; }
</style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'BoH Live Dashboard'])

    <div class="app-content">
        @can('view-boh-dashboard')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Live Operations Centre</h3>
                            <div class="d-flex align-items-center gap-3">
                                <span class="live-indicator">
                                    <span class="live-dot"></span> LIVE
                                </span>
                                <button class="btn btn-sm btn-outline-secondary" onclick="refreshDashboard()">
                                    <i class="bi bi-arrow-clockwise"></i> Refresh
                                </button>
                            </div>
                        </div>

                        <div class="card-body">

                            {{-- ── ALERT BANNER ── --}}
                            @if($delayedAlert)
                            <div class="alert alert-warning d-flex align-items-center gap-2 mb-4">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div>{{ $delayedAlert }}</div>
                            </div>
                            @endif

                            {{-- ── STAT CARDS ── --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="text-uppercase small fw-semibold opacity-75 mb-1">Active Drivers</div>
                                            <div class="fs-2 fw-bold">{{ $activeDrivers }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="text-uppercase small fw-semibold opacity-75 mb-1">Rides Today</div>
                                            <div class="fs-2 fw-bold">{{ $ridesToday }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-dark shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="text-uppercase small fw-semibold opacity-75 mb-1">Pending Broadcasts</div>
                                            <div class="fs-2 fw-bold">{{ $pendingBroadcasts }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-danger text-white shadow-sm border-0">
                                        <div class="card-body">
                                            <div class="text-uppercase small fw-semibold opacity-75 mb-1">Delayed Drivers</div>
                                            <div class="fs-2 fw-bold">{{ $delayedCount }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- ── DRIVER STATUS GRID ── --}}
                            <h5 class="fw-bold mb-3">Live Driver Status</h5>
                            <div class="row g-3 mb-4">
                                @forelse($drivers as $driver)
                                    @php
                                        $statusClass = match($driver->availability_status) {
                                            'on_trip'          => 'on-trip',
                                            'ready_next_batch' => 'next-batch',
                                            'delayed'          => 'delayed',
                                            'available'        => 'ready',
                                            default            => 'offline',
                                        };
                                        $statusBadge = match($driver->availability_status) {
                                            'on_trip'          => '<span class="badge bg-primary">🔵 On Trip</span>',
                                            'ready_next_batch' => '<span class="badge bg-info text-dark">⚡ Next Batch</span>',
                                            'delayed'          => '<span class="badge bg-danger">🔴 Delayed</span>',
                                            'available'        => '<span class="badge bg-success">✅ Ready</span>',
                                            default            => '<span class="badge bg-secondary">⚫ Offline</span>',
                                        };
                                        $usedCapacity = $driver->today_rides_count ?? 0;
                                        $maxCapacity  = $driver->vehicleType->max_capacity ?? 1;
                                        $pct          = $maxCapacity > 0 ? ($usedCapacity / $maxCapacity) * 100 : 0;
                                        $barClass     = $pct >= 100 ? 'full' : ($pct >= 60 ? 'mid' : 'low');
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="card driver-status-card {{ $statusClass }} shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <div>
                                                        <div class="fw-bold">{{ $driver->user->first_name }} {{ $driver->user->last_name }}</div>
                                                        <div class="small text-muted">
                                                            🚐 {{ $driver->vehicleType->name ?? 'No Vehicle' }} · {{ $driver->car_plate_number }}
                                                        </div>
                                                    </div>
                                                    {!! $statusBadge !!}
                                                </div>
                                                <div class="mb-1">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span>Capacity</span>
                                                        <span>{{ $usedCapacity }} / {{ $maxCapacity }} kids</span>
                                                    </div>
                                                    <div class="capacity-bar">
                                                        <div class="capacity-fill {{ $barClass }}" style="width: {{ min($pct, 100) }}%"></div>
                                                    </div>
                                                </div>
                                                <div class="d-flex gap-2 flex-wrap mt-2">
                                                    @if($driver->isFaceVerified())
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle">✅ Face Verified</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">❌ Not Verified</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 mt-3">
                                                    <button class="btn btn-sm btn-outline-secondary">📍 Track</button>
                                                    @if(in_array($driver->availability_status, ['on_trip', 'delayed']))
                                                        <button class="btn btn-sm btn-danger">📞 Call</button>
                                                    @else
                                                        @can('create-rideassign')
                                                        <a href="{{ route('admin.ride.assign.create', ['driver' => $driver->id]) }}"
                                                           class="btn btn-sm btn-primary">📋 Assign</a>
                                                        @endcan
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center text-muted py-4">
                                            <i class="bi bi-person-slash fs-2 d-block mb-2"></i>
                                            No active drivers at the moment.
                                        </div>
                                    </div>
                                @endforelse
                            </div>

                            {{-- ── ACTIVITY FEED ── --}}
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-bold">Activity Feed</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center text-muted py-3">
                                        <i class="bi bi-clock-history fs-2 d-block mb-2"></i>
                                        Activity feed coming soon.
                                    </div>
                                </div>
                            </div>

                        </div>{{-- /card-body --}}
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    @push('scripts')
    <script>
        function refreshDashboard() {
            location.reload();
        }
        // Auto-refresh every 30 seconds
        setTimeout(() => location.reload(), 30000);
    </script>
    @endpush
@endsection
