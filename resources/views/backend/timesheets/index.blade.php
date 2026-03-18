@extends('backend.app')
@section('title', 'Driver Timesheets')

@section('css')
<style>
    /* ── Stats Cards ── */
    .stat-card {
        border-radius: 12px;
        padding: 1.25rem;
        border: none;
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
    .stat-card .stat-icon { font-size: 2rem; margin-bottom: .5rem; }
    .stat-card .stat-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-card .stat-label { font-size: .8rem; opacity: .8; margin-top: .25rem; }

    /* ── Table ── */
    .ts-table th { background: #495057; color: #fff; font-size: .78rem; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
    .ts-table td { vertical-align: middle; font-size: .875rem; }
    .ts-table tbody tr:hover { background: #f8f9fa; }

    /* ── Status Badges ── */
    .badge-present  { background: #d1e7dd; color: #0a3622; }
    .badge-absent   { background: #f8d7da; color: #58151c; }
    .badge-pending  { background: #fff3cd; color: #664d03; }
    .badge-approved { background: #d1e7dd; color: #0a3622; }
    .badge-rejected { background: #f8d7da; color: #58151c; }

    /* ── Shift Pills ── */
    .shift-pill {
        display: inline-block; padding: 2px 8px;
        border-radius: 20px; font-size: .72rem; font-weight: 600;
        margin: 1px;
    }
    .shift-morning   { background: #fff3cd; color: #664d03; }
    .shift-midday    { background: #ffe5d0; color: #7c3a00; }
    .shift-afternoon { background: #fde2e4; color: #7b1c27; }
    .shift-evening   { background: #e5d5f5; color: #4a1a6b; }
    .shift-night     { background: #d0d5e8; color: #1a2461; }

    /* ── Filter Bar ── */
    .filter-bar { background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; }

    /* ── Approval Buttons ── */
    .btn-approve { background: #0f9d58; color: #fff; border: none; padding: 4px 10px; border-radius: 6px; font-size: .75rem; }
    .btn-approve:hover { background: #0b7a44; color: #fff; }
    .btn-reject  { background: #d93025; color: #fff; border: none; padding: 4px 10px; border-radius: 6px; font-size: .75rem; }
    .btn-reject:hover  { background: #b02019; color: #fff; }

    /* ── Progress bar ── */
    .attendance-bar { height: 6px; border-radius: 3px; background: #dee2e6; overflow: hidden; margin-top: 4px; }
    .attendance-bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, #0d6efd, #0dcaf0); transition: width .6s; }

    /* ── Detail Modal ── */
    .modal-shift-row { border-left: 4px solid #0d6efd; padding: .75rem 1rem; margin-bottom: .5rem; background: #f8f9fa; border-radius: 0 8px 8px 0; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Driver Timesheets'])

<div class="app-content">
    <div class="container-fluid">

        {{-- ══ PAGE HEADER ══ --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0 fw-bold">🕐 Driver Timesheets</h4>
                <small class="text-muted">Attendance & work hours management</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.timesheets.export', request()->query()) }}"
                   class="btn btn-sm btn-outline-success">
                    <i class="bi bi-download"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- ══ SUMMARY STATS ══ --}}
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card bg-primary text-white">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value">{{ $stats['total_drivers'] }}</div>
                    <div class="stat-label">Active Drivers</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card bg-success text-white">
                    <div class="stat-icon">✅</div>
                    <div class="stat-value">{{ $stats['total_present'] }}</div>
                    <div class="stat-label">Present Today</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card bg-warning text-dark">
                    <div class="stat-icon">⏳</div>
                    <div class="stat-value">{{ $stats['pending_approval'] }}</div>
                    <div class="stat-label">Pending Approval</div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="stat-card bg-info text-white">
                    <div class="stat-icon">⏱</div>
                    <div class="stat-value">{{ number_format($stats['total_hours'], 1) }}h</div>
                    <div class="stat-label">Total Hours This Month</div>
                </div>
            </div>
        </div>

        {{-- ══ FILTER BAR ══ --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('admin.timesheets.index') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Month</label>
                        <input type="month" name="month" class="form-control form-control-sm"
                               value="{{ request('month', now()->format('Y-m')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Driver</label>
                        <select name="driver_id" class="form-select form-select-sm">
                            <option value="">All Drivers</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->user?->first_name }} {{ $driver->user?->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            <a href="{{ route('admin.timesheets.index') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-x"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- ══ MAIN TABLE ══ --}}
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">
                    📋 Timesheet Records
                    <span class="badge bg-secondary ms-2">{{ $timesheets->total() }}</span>
                </h6>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-success" id="btnApproveAll">
                        ✅ Approve All Pending
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table ts-table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Driver</th>
                                <th>Date</th>
                                <th>Shifts</th>
                                <th>Hours</th>
                                <th>Rides</th>
                                <th>Attendance</th>
                                <th>TS Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($timesheets as $ts)
                            <tr data-ts-id="{{ $ts->id }}">
                                {{-- Driver --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                             style="width:34px;height:34px;font-size:.75rem;font-weight:700;flex-shrink:0">
                                            {{ strtoupper(substr($ts->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($ts->last_name ?? '', 0, 1)) }}

                                        </div>
                                        <div>
                                            <div class="fw-semibold" style="font-size:.85rem">
                                            {{ strtoupper(substr($ts->first_name ?? 'D', 0, 1)) }}{{ strtoupper(substr($ts->last_name ?? '', 0, 1)) }}

                                            </div>
                                            <div class="text-muted" style="font-size:.75rem">
                                                {{ $ts->phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td>
                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($ts->date)->format('d M Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($ts->date)->format('l') }}</div>
                                </td>

                                {{-- Shifts --}}
                                <td>
                                    @if($ts->shifts && count($ts->shifts) > 0)
                                        @foreach($ts->shifts as $shift)
                                            @php
                                                $shiftClass = match(strtolower($shift['label'] ?? '')) {
                                                    'morning'   => 'shift-morning',
                                                    'midday'    => 'shift-midday',
                                                    'afternoon' => 'shift-afternoon',
                                                    'evening'   => 'shift-evening',
                                                    'night'     => 'shift-night',
                                                    default     => 'bg-secondary text-white',
                                                };
                                            @endphp
                                            <span class="shift-pill {{ $shiftClass }}">
                                                {{ $shift['label'] ?? 'Shift' }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>

                                {{-- Hours --}}
                                <td>
                                    <div class="fw-bold text-primary">
                                        {{ number_format($ts->hours_worked, 1) }}h
                                    </div>
                                    @if($ts->shift_start && $ts->shift_end)
                                        <div class="text-muted small">
                                            {{ \Carbon\Carbon::parse($ts->shift_start)->format('H:i') }}
                                            – {{ \Carbon\Carbon::parse($ts->shift_end)->format('H:i') }}
                                        </div>
                                    @endif
                                </td>

                                {{-- Rides --}}
                                <td>
                                    <div class="fw-semibold">
                                        <span class="text-success">{{ $ts->completed_rides ?? 0 }}</span>
                                        <span class="text-muted">/{{ $ts->total_rides ?? 0 }}</span>
                                    </div>
                                    @if(($ts->total_rides ?? 0) > 0)
                                        @php $pct = round(($ts->completed_rides ?? 0) / $ts->total_rides * 100); @endphp
                                        <div class="attendance-bar">
                                            <div class="attendance-bar-fill" style="width:{{ $pct }}%"></div>
                                        </div>
                                        <div class="text-muted" style="font-size:.7rem">{{ $pct }}% done</div>
                                    @endif
                                </td>

                                {{-- Attendance --}}
                                <td>
                                    @php
                                        $attStatus = $ts->attendance_status ?? 'pending';
                                        $attClass  = match($attStatus) {
                                            'present'   => 'badge-present',
                                            'absent'    => 'badge-absent',
                                            'cancelled' => 'bg-secondary text-white',
                                            default     => 'badge-pending',
                                        };
                                        $attIcon = match($attStatus) {
                                            'present'   => '✅',
                                            'absent'    => '❌',
                                            'cancelled' => '🚫',
                                            default     => '⏳',
                                        };
                                    @endphp
                                    <span class="badge {{ $attClass }} px-2 py-1">
                                        {{ $attIcon }} {{ ucfirst($attStatus) }}
                                    </span>
                                </td>

                                {{-- Timesheet Status --}}
                                <td>
                                    @php
                                        $tsStatus = $ts->status ?? 'pending';
                                        $tsClass  = match($tsStatus) {
                                            'approved' => 'badge-approved',
                                            'rejected' => 'badge-rejected',
                                            default    => 'badge-pending',
                                        };
                                    @endphp
                                    <span class="badge {{ $tsClass }} px-2 py-1">{{ ucfirst($tsStatus) }}</span>
                                </td>

                                {{-- Actions --}}
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <button class="btn-approve btn-action"
                                                data-id="{{ $ts->id }}" data-action="approved"
                                                {{ $tsStatus === 'approved' ? 'disabled' : '' }}>
                                            ✅
                                        </button>
                                        <button class="btn-reject btn-action"
                                                data-id="{{ $ts->id }}" data-action="rejected"
                                                {{ $tsStatus === 'rejected' ? 'disabled' : '' }}>
                                            ❌
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary btn-view"
                                                data-id="{{ $ts->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#tsDetailModal">
                                            👁
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <div style="font-size:2rem">📋</div>
                                    <div class="fw-semibold mt-2">No timesheet records found</div>
                                    <div class="small">Adjust your filters and try again</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($timesheets->hasPages())
            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Showing {{ $timesheets->firstItem() }}–{{ $timesheets->lastItem() }}
                    of {{ $timesheets->total() }} records
                </small>
                {{ $timesheets->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

{{-- ══ DETAIL MODAL ══ --}}
<div class="modal fade" id="tsDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">🕐 Timesheet Detail</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tsDetailBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script>
$(function () {

    // ── Approve / Reject single ───────────────────────────
    $(document).on('click', '.btn-action', function () {
        const id     = $(this).data('id');
        const action = $(this).data('action');
        const row    = $(this).closest('tr');

        $.ajax({
            url:    `/admin/timesheet/${id}/status`,
            method: 'PATCH',
            data:   { _token: '{{ csrf_token() }}', status: action },
            success: function (res) {
                if (res.success) {
                    // Update badge in row
                    const badgeClass = action === 'approved' ? 'badge-approved' : 'badge-rejected';
                    row.find('.badge-pending, .badge-approved, .badge-rejected')
                       .last()
                       .attr('class', `badge ${badgeClass} px-2 py-1`)
                       .text(action.charAt(0).toUpperCase() + action.slice(1));

                    // Disable the used button
                    row.find(`[data-action="${action}"]`).prop('disabled', true);
                    row.find(`[data-action="${action === 'approved' ? 'rejected' : 'approved'}"]`).prop('disabled', false);
                }
            },
            error: function () {
                alert('Failed to update status. Please try again.');
            }
        });
    });

    // ── Approve All Pending ───────────────────────────────
    $('#btnApproveAll').on('click', function () {
        if (!confirm('Approve all pending timesheets for this month?')) return;
        const month = $('[name="month"]').val() || '{{ now()->format("Y-m") }}';
        $.ajax({
            url:    '/admin/timesheet/approve-all',
            method: 'POST',
            data:   { _token: '{{ csrf_token() }}', month: month },
            success: function (res) {
                if (res.success) {
                    alert(`✅ ${res.count} timesheets approved.`);
                    location.reload();
                }
            }
        });
    });

    // ── View Detail Modal ─────────────────────────────────
    $(document).on('click', '.btn-view', function () {
        const id = $(this).data('id');
        $('#tsDetailBody').html('<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>');
        $.get(`/admin/timesheet/${id}/detail`, function (html) {
            $('#tsDetailBody').html(html);
        });
    });

    // ── Auto-submit filter on month change ────────────────
    $('[name="month"]').on('change', function () {
        $('#filterForm').submit();
    });

});
</script>
@endpush
