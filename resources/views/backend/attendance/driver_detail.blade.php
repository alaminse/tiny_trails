@extends('backend.app')
@section('title', 'Driver Attendance Detail')

@section('css')
<style>
    .stat-box { border-radius:12px; padding:1rem 1.25rem; text-align:center; }
    .day-card { border-radius:10px; overflow:hidden; margin-bottom:.75rem; border:1px solid #dee2e6; }
    .day-card .day-header { padding:.75rem 1rem; font-weight:700; font-size:.9rem; }
    .day-card .day-header.present { background:#d1e7dd; color:#0a3622; }
    .day-card .day-header.absent  { background:#f8d7da; color:#58151c; }
    .shift-item { padding:.6rem 1rem; border-top:1px solid #f0f0f0; background:#fff; font-size:.85rem; }
    .ride-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-right:4px; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Driver Detail'])

<div class="app-content">
    <div class="container-fluid">

        {{-- Back --}}
        <div class="mb-3">
            <a href="{{ route('admin.attendance.index', ['month' => $month]) }}"
               class="btn btn-sm btn-outline-secondary">← Back to Attendance</a>
        </div>

        {{-- ── Driver Info Card ── --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:60px;height:60px;border-radius:50%;
                                background:linear-gradient(135deg,#0d6efd,#0dcaf0);
                                display:flex;align-items:center;justify-content:center;
                                color:#fff;font-size:1.2rem;font-weight:800;">
                        {{ strtoupper(substr($driver->user?->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($driver->user?->last_name ?? '', 0, 1)) }}
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $driver->user?->first_name }} {{ $driver->user?->last_name }}</h5>
                        <div class="text-muted">{{ $driver->user?->phone }}</div>
                        <div class="text-muted small">{{ $driver->user?->email }}</div>
                    </div>
                    <div class="ms-auto">
                        <form method="GET">
                            <input type="hidden" name="driver_id" value="{{ $driver->id }}">
                            <input type="month" name="month" class="form-control form-control-sm"
                                   value="{{ $month }}" onchange="this.form.submit()">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Monthly Summary ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-box bg-success bg-opacity-10">
                    <div class="fw-bold text-success" style="font-size:2rem">{{ $summary['present_days'] }}</div>
                    <div class="text-muted small">✅ Present Days</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box bg-danger bg-opacity-10">
                    <div class="fw-bold text-danger" style="font-size:2rem">{{ $summary['absent_days'] }}</div>
                    <div class="text-muted small">❌ Absent Days</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box bg-primary bg-opacity-10">
                    <div class="fw-bold text-primary" style="font-size:2rem">{{ $summary['completed_rides'] }}</div>
                    <div class="text-muted small">🚗 Completed Rides</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-box bg-info bg-opacity-10">
                    <div class="fw-bold text-info" style="font-size:2rem">{{ $summary['total_rides'] }}</div>
                    <div class="text-muted small">📋 Total Assigned</div>
                </div>
            </div>
        </div>

        {{-- ── Daily Records ── --}}
        <h6 class="fw-bold mb-3">Daily Attendance — {{ \Carbon\Carbon::parse($month.'-01')->format('F Y') }}</h6>

        @forelse($dailyData as $day)
            <div class="day-card">
                <div class="day-header {{ $day['attendance_status'] }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>{{ $day['day_name'] }}</span>
                        <div class="d-flex gap-2 align-items-center">
                            <span style="font-size:.8rem; font-weight:400">
                                🚗 {{ $day['completed_rides'] }}/{{ $day['total_rides'] }} rides
                            </span>
                            <span class="badge {{ $day['attendance_status'] === 'present' ? 'bg-success' : 'bg-danger' }}">
                                {{ $day['attendance_status'] === 'present' ? 'Present' : 'Absent' }}
                            </span>
                        </div>
                    </div>
                </div>
                @foreach($day['shifts'] as $shift)
                    <div class="shift-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $shift['shift_label'] }}</strong>
                            <span class="text-muted ms-2">
                                {{ \Carbon\Carbon::parse('2000-01-01 '.$shift['start_time'])->format('h:i A') }}
                                – {{ \Carbon\Carbon::parse('2000-01-01 '.$shift['end_time'])->format('h:i A') }}
                            </span>
                        </div>
                        <div class="d-flex gap-3 small">
                            <span class="text-success">✅ {{ $shift['completed_rides'] }} done</span>
                            <span class="text-muted">📋 {{ $shift['total_rides'] }} total</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <div style="font-size:2.5rem">📅</div>
                <div class="fw-semibold mt-2">No shifts assigned this month</div>
            </div>
        @endforelse

    </div>
</div>
@endsection
