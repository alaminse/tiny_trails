@extends('backend.app')
@section('title', 'Driver Attendance')

@section('css')
<style>
    /* ── Stats ── */
    .stat-card { border-radius:12px; padding:1.25rem; border:none; transition:transform .2s, box-shadow .2s; }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.1); }
    .stat-value { font-size:2rem; font-weight:800; line-height:1; }
    .stat-label { font-size:.8rem; opacity:.8; margin-top:.25rem; }

    /* ── View Tabs ── */
    .view-tabs .btn { border-radius:8px; font-size:.85rem; padding:6px 16px; }
    .view-tabs .btn.active { box-shadow:0 2px 8px rgba(0,0,0,.15); }

    /* ── Attendance Badges ── */
    .badge-present  { background:#d1e7dd; color:#0a3622; font-weight:700; padding:4px 10px; border-radius:20px; font-size:.75rem; }
    .badge-absent   { background:#f8d7da; color:#58151c; font-weight:700; padding:4px 10px; border-radius:20px; font-size:.75rem; }
    .badge-no-shift { background:#e9ecef; color:#6c757d; font-weight:700; padding:4px 10px; border-radius:20px; font-size:.75rem; }

    /* ── Driver Avatar ── */
    .driver-avatar {
        width:38px; height:38px; border-radius:50%;
        background:linear-gradient(135deg,#0d6efd,#0dcaf0);
        color:#fff; font-size:.78rem; font-weight:800;
        display:flex; align-items:center; justify-content:center;
        flex-shrink:0;
    }

    /* ── Date Card ── */
    .date-card { border-left:4px solid #0d6efd; border-radius:0 8px 8px 0; background:#fff; margin-bottom:1rem; padding:1rem 1.25rem; box-shadow:0 1px 4px rgba(0,0,0,.05); }
    .date-card.day-weekend { border-left-color:#dc3545; background:#fff5f5; }

    /* ── Shift Row ── */
    .shift-row { background:#f8f9fa; border-radius:8px; padding:.6rem 1rem; margin-top:.5rem; font-size:.85rem; }
    .shift-pill { display:inline-block; padding:2px 10px; border-radius:20px; font-size:.72rem; font-weight:700; margin-right:4px; }
    .shift-morning   { background:#fff3cd; color:#664d03; }
    .shift-midday    { background:#ffe5d0; color:#7c3a00; }
    .shift-afternoon { background:#fde2e4; color:#7b1c27; }
    .shift-evening   { background:#e5d5f5; color:#4a1a6b; }
    .shift-night     { background:#d0d5e8; color:#1a2461; }

    /* ── Grid View ── */
    .grid-table th, .grid-table td { font-size:.78rem; padding:6px 8px; white-space:nowrap; }
    .grid-cell-present  { background:#d1e7dd; color:#0a3622; border-radius:4px; text-align:center; padding:3px 6px; font-weight:700; font-size:.7rem; }
    .grid-cell-absent   { background:#f8d7da; color:#58151c; border-radius:4px; text-align:center; padding:3px 6px; font-size:.7rem; }
    .grid-cell-no-shift { background:#f8f9fa; color:#adb5bd; border-radius:4px; text-align:center; padding:3px 6px; font-size:.7rem; }

    /* ── Progress Bar ── */
    .ride-bar { height:5px; border-radius:3px; background:#e9ecef; margin-top:3px; overflow:hidden; }
    .ride-bar-fill { height:100%; border-radius:3px; background:linear-gradient(90deg,#0d6efd,#0dcaf0); }

    /* ── Filter Bar ── */
    .filter-bar { background:#f8f9fa; border:1px solid #dee2e6; border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.5rem; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Driver Attendance'])

<div class="app-content">
    <div class="container-fluid">

        @cannot('list-attendance')
        {{-- ── Access Denied ──────────────────────────────────────── --}}
        <div class="text-center py-5">
            <div style="font-size:4rem">🔒</div>
            <h4 class="mt-3 fw-bold">Access Denied</h4>
            <p class="text-muted">You do not have permission to view attendance records.</p>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm mt-2">
                <i class="bi bi-house"></i> Back to Dashboard
            </a>
        </div>
        @else
        {{-- ═══════════════════════════════════════════════════════════
             MAIN CONTENT — requires list-attendance
        ═══════════════════════════════════════════════════════════ --}}

        {{-- ══ HEADER ══ --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold">📅 Driver Attendance</h4>
                <small class="text-muted">{{ \Carbon\Carbon::parse($month . '-01')->format('F Y') }}</small>
            </div>
            {{-- Export: only roles with list-attendance (same gate as index) --}}
            @can('list-attendance')
            <a href="{{ route('admin.attendance.export', request()->query()) }}"
               class="btn btn-sm btn-outline-success">
                <i class="bi bi-download"></i> Export CSV
            </a>
            @endcan
        </div>

        {{-- ══ STATS ══ --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-xl-3">
                <div class="stat-card bg-primary text-white">
                    <div class="stat-value">{{ $stats['total_drivers'] }}</div>
                    <div class="stat-label">👥 Total Drivers</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card bg-success text-white">
                    <div class="stat-value">{{ $stats['present_today'] }}</div>
                    <div class="stat-label">✅ Present Today</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card bg-danger text-white">
                    <div class="stat-value">{{ $stats['absent_today'] }}</div>
                    <div class="stat-label">❌ Absent Today</div>
                </div>
            </div>
            <div class="col-6 col-xl-3">
                <div class="stat-card bg-info text-white">
                    <div class="stat-value">{{ $stats['total_rides_month'] }}</div>
                    <div class="stat-label">🚗 Rides This Month</div>
                </div>
            </div>
        </div>

        {{-- ══ FILTER BAR ══ --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.attendance.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Month</label>
                        <input type="month" name="month" class="form-control form-control-sm"
                               value="{{ $month }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Driver</label>
                        <select name="driver_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ $driverId == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->user?->first_name }} {{ $driver->user?->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold mb-1">View</label>
                        <div class="view-tabs d-flex gap-2">
                            <a href="{{ request()->fullUrlWithQuery(['view' => 'date']) }}"
                               class="btn btn-sm {{ $view === 'date' ? 'btn-primary active' : 'btn-outline-secondary' }}">
                                📅 Date Wise
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['view' => 'driver']) }}"
                               class="btn btn-sm {{ $view === 'driver' ? 'btn-primary active' : 'btn-outline-secondary' }}">
                                👤 Driver Wise
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"
                               class="btn btn-sm {{ $view === 'grid' ? 'btn-primary active' : 'btn-outline-secondary' }}">
                                📊 Grid
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════════
             DATE WISE VIEW
        ══════════════════════════════════════════════════════════ --}}
        @if($view === 'date')
            @forelse($byDate as $date => $records)
                @php
                    $dayOfWeek = \Carbon\Carbon::parse($date)->dayOfWeek;
                    $isWeekend = in_array($dayOfWeek, [0, 6]);
                    $isToday   = $date === \Carbon\Carbon::today()->toDateString();
                @endphp
                <div class="date-card {{ $isWeekend ? 'day-weekend' : '' }}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="fw-bold fs-6">
                                {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                            </span>
                            @if($isToday)
                                <span class="badge bg-primary ms-2">Today</span>
                            @endif
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success">
                                {{ $records->where('attendance_status', 'present')->count() }} Present
                            </span>
                            <span class="badge bg-danger">
                                {{ $records->where('attendance_status', 'absent')->count() }} Absent
                            </span>
                        </div>
                    </div>

                    <div class="row g-2">
                        @foreach($records as $rec)
                        <div class="col-md-6 col-xl-4">
                            <div class="border rounded-3 p-3 h-100 bg-white">
                                {{-- Driver info --}}
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="driver-avatar">{{ $rec['driver_avatar'] }}</div>
                                    <div>
                                        <div class="fw-semibold" style="font-size:.88rem">
                                            {{ $rec['driver_name'] }}
                                        </div>
                                        <div class="text-muted" style="font-size:.75rem">
                                            {{ $rec['driver_phone'] }}
                                        </div>
                                    </div>
                                    <div class="ms-auto">
                                        <span class="badge-{{ $rec['attendance_status'] === 'present' ? 'present' : 'absent' }}">
                                            {{ $rec['attendance_status'] === 'present' ? '✅ Present' : '❌ Absent' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Ride stats --}}
                                <div class="d-flex gap-3 mb-2 small text-muted">
                                    <span>🚗 {{ $rec['completed_rides'] }}/{{ $rec['total_rides'] }} rides</span>
                                    <span>⏱ {{ $rec['hours_worked'] }}h</span>
                                </div>

                                @if($rec['total_rides'] > 0)
                                    <div class="ride-bar">
                                        <div class="ride-bar-fill"
                                             style="width:{{ round($rec['completed_rides'] / $rec['total_rides'] * 100) }}%">
                                        </div>
                                    </div>
                                @endif

                                {{-- Shifts --}}
                                @foreach($rec['shifts'] as $shift)
                                    @php
                                        $shiftClass = match(strtolower($shift['shift_label'] ?? '')) {
                                            'morning'   => 'shift-morning',
                                            'midday'    => 'shift-midday',
                                            'afternoon' => 'shift-afternoon',
                                            'evening'   => 'shift-evening',
                                            'night'     => 'shift-night',
                                            default     => 'bg-secondary text-white',
                                        };
                                    @endphp
                                    <div class="shift-row d-flex justify-content-between align-items-center mt-1">
                                        <span class="shift-pill {{ $shiftClass }}">
                                            {{ $shift['shift_label'] }}
                                        </span>
                                        <span class="text-muted small">
                                            {{ \Carbon\Carbon::parse('2000-01-01 ' . $shift['start_time'])->format('h:i A') }}
                                            –
                                            {{ \Carbon\Carbon::parse('2000-01-01 ' . $shift['end_time'])->format('h:i A') }}
                                        </span>
                                        <span class="{{ $shift['completed_rides'] > 0 ? 'text-success' : 'text-muted' }} small fw-semibold">
                                            {{ $shift['completed_rides'] }}/{{ $shift['total_rides'] }}
                                        </span>
                                    </div>
                                @endforeach

                                {{-- "View Details" only for roles with view-attendance --}}
                                @can('view-attendance')
                                <div class="mt-2 text-end">
                                    <a href="{{ route('admin.attendance.driver', ['id' => $rec['driver_id'], 'month' => request('month')]) }}"
                                       class="btn btn-xs btn-outline-primary"
                                       style="font-size:.72rem; padding:2px 8px;">
                                        View Details →
                                    </a>
                                </div>
                                @endcan
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-5 text-muted">
                    <div style="font-size:3rem">📅</div>
                    <div class="fw-semibold mt-2">No attendance records found</div>
                </div>
            @endforelse

        {{-- ══════════════════════════════════════════════════════════
             DRIVER WISE VIEW
        ══════════════════════════════════════════════════════════ --}}
        @elseif($view === 'driver')
            <div class="row g-3">
                @forelse($byDriver as $driverId => $records)
                    @php $first = $records->first(); @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-header bg-white border-bottom d-flex align-items-center gap-2">
                                <div class="driver-avatar">{{ $first['driver_avatar'] }}</div>
                                <div>
                                    <div class="fw-bold">{{ $first['driver_name'] }}</div>
                                    <div class="text-muted small">{{ $first['driver_phone'] }}</div>
                                </div>
                                {{-- Details button — view-attendance only --}}
                                @can('view-attendance')
                                <a href="{{ route('admin.attendance.driver', ['id' => $driverId, 'month' => request('month')]) }}"
                                   class="btn btn-sm btn-outline-primary ms-auto">
                                    Details
                                </a>
                                @endcan
                            </div>
                            <div class="card-body p-3">
                                {{-- Summary --}}
                                <div class="row g-2 mb-3 text-center">
                                    <div class="col-4">
                                        <div class="p-2 bg-success bg-opacity-10 rounded-3">
                                            <div class="fw-bold text-success fs-5">
                                                {{ $records->where('attendance_status', 'present')->count() }}
                                            </div>
                                            <div class="text-muted" style="font-size:.7rem">Present</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-danger bg-opacity-10 rounded-3">
                                            <div class="fw-bold text-danger fs-5">
                                                {{ $records->where('attendance_status', 'absent')->count() }}
                                            </div>
                                            <div class="text-muted" style="font-size:.7rem">Absent</div>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="p-2 bg-primary bg-opacity-10 rounded-3">
                                            <div class="fw-bold text-primary fs-5">
                                                {{ $records->sum('completed_rides') }}
                                            </div>
                                            <div class="text-muted" style="font-size:.7rem">Rides</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mini calendar --}}
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($records->sortBy('date') as $rec)
                                        <div title="{{ \Carbon\Carbon::parse($rec['date'])->format('d M') }} — {{ $rec['attendance_status'] }}"
                                             style="width:28px;height:28px;border-radius:6px;font-size:.7rem;font-weight:700;
                                                    display:flex;align-items:center;justify-content:center;cursor:default;
                                                    background:{{ $rec['attendance_status'] === 'present' ? '#d1e7dd' : '#f8d7da' }};
                                                    color:{{ $rec['attendance_status'] === 'present' ? '#0a3622' : '#58151c' }}">
                                            {{ \Carbon\Carbon::parse($rec['date'])->format('j') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5 text-muted">No records found.</div>
                @endforelse
            </div>

        {{-- ══════════════════════════════════════════════════════════
             GRID VIEW
        ══════════════════════════════════════════════════════════ --}}
        @else
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table grid-table table-bordered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="min-width:160px">Driver</th>
                                    @foreach($gridDates as $date)
                                        @php
                                            $dow  = \Carbon\Carbon::parse($date)->dayOfWeek;
                                            $isWe = in_array($dow, [0, 6]);
                                        @endphp
                                        <th class="{{ $isWe ? 'text-danger' : '' }}"
                                            style="min-width:36px; text-align:center">
                                            <div>{{ \Carbon\Carbon::parse($date)->format('D') }}</div>
                                            <div>{{ \Carbon\Carbon::parse($date)->format('j') }}</div>
                                        </th>
                                    @endforeach
                                    <th>Present</th>
                                    <th>Absent</th>
                                    <th>Rides</th>
                                    {{-- Detail column only for view-attendance --}}
                                    @can('view-attendance')
                                    <th></th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gridData as $row)
                                    @php
                                        $presentCount = collect($row['days'])->where('status', 'present')->count();
                                        $absentCount  = collect($row['days'])->where('status', 'absent')->count();
                                        $totalRides   = collect($row['days'])->sum('rides');
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="driver-avatar"
                                                     style="width:30px;height:30px;font-size:.65rem">
                                                    {{ strtoupper(substr($row['driver_name'], 0, 2)) }}
                                                </div>
                                                <div class="fw-semibold" style="font-size:.8rem">
                                                    {{ $row['driver_name'] }}
                                                </div>
                                            </div>
                                        </td>
                                        @foreach($row['days'] as $day)
                                            <td style="padding:4px">
                                                @if($day['status'] === 'present')
                                                    <div class="grid-cell-present"
                                                         title="{{ $day['rides'] }} rides">
                                                        ✓{{ $day['rides'] > 0 ? ' ' . $day['rides'] : '' }}
                                                    </div>
                                                @elseif($day['status'] === 'absent')
                                                    <div class="grid-cell-absent">✗</div>
                                                @else
                                                    <div class="grid-cell-no-shift">—</div>
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-success fw-bold">{{ $presentCount }}</td>
                                        <td class="text-danger fw-bold">{{ $absentCount }}</td>
                                        <td class="text-primary fw-bold">{{ $totalRides }}</td>
                                        @can('view-attendance')
                                        <td>
                                            <a href="{{ route('admin.attendance.driver', ['id' => $row['driver_id'], 'month' => request('month')]) }}"
                                               class="btn btn-xs btn-outline-primary"
                                               style="font-size:.72rem; padding:2px 8px; white-space:nowrap">
                                                Details →
                                            </a>
                                        </td>
                                        @endcan
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @endcannot
        {{-- end @cannot('list-attendance') --}}

    </div>
</div>
@endsection
