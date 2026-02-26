@extends('backend.app')
@section('title', 'Create Ride Assignment')

@section('css')
<style>
    /* ── Calendar ── */
    .calendar-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .calendar-nav {
        background:#0d6efd; color:#fff; border:none; padding:6px 14px;
        border-radius:6px; cursor:pointer; font-size:1.2rem; line-height:1; transition:background .2s;
    }
    .calendar-nav:hover { background:#0b5ed7; }
    .calendar-title { font-size:1.1rem; font-weight:700; color:#343a40; }

    .calendar-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:3px; }
    .cal-header-cell {
        background:#495057; color:#fff; text-align:center;
        padding:8px 4px; font-size:.75rem; font-weight:700; border-radius:4px;
    }
    .cal-day {
        background:#fff; border:1px solid #dee2e6; border-radius:6px;
        padding:8px 4px; text-align:center; cursor:pointer; font-size:.8rem;
        min-height:38px; display:flex; align-items:center; justify-content:center;
        transition:all .15s ease-in-out; user-select:none;
    }
    .cal-day:hover:not(.cal-disabled):not(.cal-other) { background:#cfe2ff; border-color:#0d6efd; transform:scale(1.05); }
    .cal-day.cal-other   { background:#f8f9fa; color:#ced4da; cursor:default; }
    .cal-day.cal-weekend { background:#fff5f5; color:#dc3545; }
    .cal-day.cal-disabled { opacity:.45; cursor:not-allowed; }
    .cal-day.cal-selected { background:#0d6efd !important; color:#fff !important; border-color:#0d6efd !important; font-weight:700; box-shadow:0 2px 4px rgba(13,110,253,.4); }
    .cal-day.cal-today   { border-color:#0d6efd; font-weight:700; }

    /* ── Date Rows ── */
    .date-row {
        background:#fff; border:1px solid #dee2e6; border-left:4px solid #0d6efd;
        border-radius:8px; padding:1rem 1.25rem; margin-bottom:1rem; transition:box-shadow .2s;
    }
    .date-row:hover { box-shadow:0 4px 12px rgba(0,0,0,.08); }
    .date-row .date-label {
        font-weight:700; font-size:.9rem; color:#343a40;
        background:#e9ecef; padding:4px 12px; border-radius:20px; display:inline-block;
    }
    .btn-remove-date { transition:all .2s; }
    .btn-remove-date:hover { transform:scale(1.1); }

    /* ── Misc ── */
    .empty-dates { text-align:center; padding:3rem 1.5rem; color:#6c757d; border:2px dashed #dee2e6; border-radius:8px; }
    .empty-dates .empty-icon { font-size:2.5rem; margin-bottom:.5rem; }
    .auto-select-bar { background:#d1e7dd; border:1px solid #a3cfbb; border-radius:8px; padding:.6rem 1rem; margin-bottom:1rem; font-size:.85rem; color:#0a3622; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Create Ride Assignment'])

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">New Ride Assignment</h3>
                        <a href="{{ route('admin.ride.assign.index') }}" class="btn btn-sm btn-outline-secondary">← Back to List</a>
                    </div>

                    <div class="card-body">

                        {{-- Flash Messages --}}
                        @session('success')
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ $value }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endsession
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                <strong>Whoops!</strong> There were some problems with your input.
                                <ul class="mb-0 mt-2">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        {{-- ══ SUBSCRIPTION DETAILS ══ --}}
                        <div class="card shadow-sm border-0 rounded-3 mb-4">
                            <div class="card-header bg-primary text-white rounded-top-3">
                                <h6 class="mb-0 fw-bold">📋 Subscription Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Customer</span>
                                                <span>{{ $subscription->user?->first_name }} {{ $subscription->user?->last_name }}</span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Phone</span>
                                                <span>{{ $subscription->user?->phone }}</span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Kid</span>
                                                <span>{{ $subscription->kid?->first_name }} {{ $subscription->kid?->last_name }}</span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Plan</span>
                                                <span>{{ $subscription->plan?->name }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Pickup</span>
                                                <span class="text-end" style="max-width:60%">{{ $subscription->pickupLocation?->address }}</span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Dropoff</span>
                                                <span class="text-end" style="max-width:60%">{{ $subscription->dropoffLocation?->address }}</span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Fare</span>
                                                <span class="fw-bold text-success">
                                                    ${{ number_format($subscription->kid_wage?->sell_price ?? $subscription->plan?->sell_price, 2) }} AUD
                                                </span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Wage Period</span>
                                                <span>
                                                    @if($subscription->kid_wage)
                                                        {{ \Carbon\Carbon::parse($subscription->kid_wage->start_date)->format('d M Y') }} →
                                                        {{ \Carbon\Carbon::parse($subscription->kid_wage->end_date)->format('d M Y') }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ ASSIGNMENT FORM ══ --}}
                        <form action="{{ route('admin.ride.assign.store', $subscription->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="selected_dates" id="selectedDatesInput">
                            <input type="hidden" name="working_days"   id="totalWorkingDays" value="0">
                            <input type="hidden" name="fare"           value="{{ $subscription->kid_wage?->sell_price ?? $subscription->plan?->sell_price }}">

                            {{-- ══ STEP 1: DATES ══ --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">📅 Step 1 — Ride Dates</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($subscription->kid_wage)
                                                <span class="badge bg-info text-dark">
                                                    {{ \Carbon\Carbon::parse($subscription->kid_wage->start_date)->format('d M') }} –
                                                    {{ \Carbon\Carbon::parse($subscription->kid_wage->end_date)->format('d M Y') }}
                                                </span>
                                            @endif
                                            <span class="badge bg-secondary">Weekends disabled</span>
                                            <span class="badge bg-primary" id="selectedCountBadge">0 days selected</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Auto-select notice --}}
                                    <div class="auto-select-bar">
                                        ✅ All weekdays in the wage period have been <strong>auto-selected</strong>.
                                        You can click any highlighted day to <strong>deselect</strong> it, or click an unselected day to add it back.
                                    </div>

                                    {{-- Calendar Nav --}}
                                    <div class="calendar-header">
                                        <button type="button" class="calendar-nav" id="prevMonth">&#8249;</button>
                                        <span class="calendar-title" id="currentMonth"></span>
                                        <button type="button" class="calendar-nav" id="nextMonth">&#8250;</button>
                                    </div>
                                    <div class="calendar-grid" id="calendarGrid"></div>

                                    {{-- Legend --}}
                                    <div class="d-flex gap-3 mt-3 flex-wrap small">
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#0d6efd;border-radius:3px;vertical-align:middle"></span> Selected</span>
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#fff5f5;border:1px solid #dc3545;border-radius:3px;vertical-align:middle"></span> Weekend</span>
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#f8f9fa;border:1px solid #dee2e6;border-radius:3px;vertical-align:middle;opacity:.5"></span> Out of range</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ══ STEP 2: TIMES ══ --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">🕐 Step 2 — Set Pickup & Dropoff Times</h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll">✔ Select All</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger"  id="btnClearAll">✕ Clear All</button>
                                    </div>
                                </div>
                                <div class="card-body" id="dateRowsContainer">
                                    <div class="empty-dates" id="emptyState">
                                        <div class="empty-icon">📅</div>
                                        <div class="fw-semibold mb-1">No dates selected yet</div>
                                        <div class="small text-muted">All weekdays are auto-selected — use the calendar to adjust.</div>
                                    </div>
                                </div>
                            </div>

                            {{-- ══ ACTIONS ══ --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle"></i>
                                    Each selected date creates a Morning Trip + Afternoon Return.
                                </div>
                                <button type="submit" class="btn btn-primary px-5">
                                    <i class="bi bi-check2-circle"></i> Create Assignments
                                </button>
                            </div>
                        </form>

                    </div>{{-- /card-body --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- jQuery 3.7.1 (CDN) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<script>
$(function () {

    /* ──────────────────────────────────────────
       CONFIG
    ────────────────────────────────────────── */
    const MONTH_NAMES = ["January","February","March","April","May","June",
                         "July","August","September","October","November","December"];
    const DAY_HEADERS  = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    const rangeStart = new Date('{{ $subscription->kid_wage?->start_date ?? \Carbon\Carbon::parse($subscription->created_at)->format("Y-m-d") }}');
    const rangeEnd   = new Date('{{ $subscription->kid_wage?->end_date   ?? \Carbon\Carbon::parse($subscription->ends_at)->format("Y-m-d") }}');

    let currentDate   = new Date(rangeStart); // calendar opens at range-start month
    let selectedDates = [];                   // ['YYYY-MM-DD', ...]

    /* ──────────────────────────────────────────
       UTILITIES
    ────────────────────────────────────────── */
    const toYMD = d => {
        const y = d.getFullYear();
        const m = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        return `${y}-${m}-${day}`;
    };

    const isWeekend = d => d.getDay() === 0 || d.getDay() === 6;

    const inRange = d => {
        const c = new Date(d); c.setHours(0,0,0,0);
        const s = new Date(rangeStart); s.setHours(0,0,0,0);
        const e = new Date(rangeEnd);   e.setHours(0,0,0,0);
        return c >= s && c <= e;
    };

    const formatLabel = ymd => {
        const [y, m, d] = ymd.split('-').map(Number);
        return new Date(y, m - 1, d).toLocaleDateString('en-AU', {
            weekday:'short', day:'2-digit', month:'short', year:'numeric'
        });
    };

    /* ──────────────────────────────────────────
       AUTO-SELECT ALL WEEKDAYS IN RANGE
    ────────────────────────────────────────── */
    function autoSelectAllWeekdays() {
        selectedDates = [];
        const cursor = new Date(rangeStart);
        cursor.setHours(0,0,0,0);
        const end = new Date(rangeEnd);
        end.setHours(0,0,0,0);

        while (cursor <= end) {
            if (!isWeekend(cursor)) {
                selectedDates.push(toYMD(new Date(cursor)));
            }
            cursor.setDate(cursor.getDate() + 1);
        }
    }

    /* ──────────────────────────────────────────
       CALENDAR RENDER
    ────────────────────────────────────────── */
    function renderCalendar(date) {
        const year  = date.getFullYear();
        const month = date.getMonth();
        const today = toYMD(new Date());

        $('#currentMonth').text(`${MONTH_NAMES[month]} ${year}`);

        let html = DAY_HEADERS.map(d => `<div class="cal-header-cell">${d}</div>`).join('');

        const firstDay     = new Date(year, month, 1).getDay();
        const daysInMonth  = new Date(year, month + 1, 0).getDate();
        const daysInPrev   = new Date(year, month, 0).getDate();

        // Trailing days from prev month
        for (let i = firstDay - 1; i >= 0; i--) {
            html += `<div class="cal-day cal-other cal-disabled">${daysInPrev - i}</div>`;
        }

        // Current month days
        for (let day = 1; day <= daysInMonth; day++) {
            const dateObj  = new Date(year, month, day);
            const ymd      = toYMD(dateObj);
            const weekend  = isWeekend(dateObj);
            const valid    = inRange(dateObj) && !weekend;
            const selected = selectedDates.includes(ymd);
            const isToday  = ymd === today;

            let cls = 'cal-day';
            if (selected)            cls += ' cal-selected';
            else if (weekend)        cls += ' cal-weekend';
            if (!valid)              cls += ' cal-disabled';
            if (isToday && !selected) cls += ' cal-today';

            html += `<div class="${cls}" data-date="${ymd}">${day}</div>`;
        }

        // Leading days from next month
        const totalCells = firstDay + daysInMonth;
        const trailingDays = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let day = 1; day <= trailingDays; day++) {
            html += `<div class="cal-day cal-other cal-disabled">${day}</div>`;
        }

        $('#calendarGrid').html(html);
    }

    /* ──────────────────────────────────────────
       DATE ROWS RENDER
    ────────────────────────────────────────── */
    function renderDateRows() {
        const count = selectedDates.length;

        $('#selectedDatesInput').val(JSON.stringify(selectedDates));
        $('#totalWorkingDays').val(count);
        $('#selectedCountBadge').text(count === 0 ? '0 days selected' : `${count} ${count === 1 ? 'day' : 'days'} selected`);

        if (count === 0) {
            $('#dateRowsContainer').html(`
                <div class="empty-dates" id="emptyState">
                    <div class="empty-icon">📅</div>
                    <div class="fw-semibold mb-1">No dates selected yet</div>
                    <div class="small text-muted">All weekdays are auto-selected — use the calendar to adjust.</div>
                </div>`);
            return;
        }

        const sortedDates = [...selectedDates].sort();
        let html = '';

        sortedDates.forEach(ymd => {
            const label = formatLabel(ymd);
            html += `
            <div class="date-row" data-row-date="${ymd}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="date-label">📅 ${label}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-date" data-date="${ymd}">✕ Remove</button>
                </div>
                <div class="row g-3 align-items-start">
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small text-muted mb-1">Date</label>
                        <input type="date" class="form-control form-control-sm" name="ride_date[${ymd}]" value="${ymd}" readonly>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-muted mb-1">🌅 Morning Trip</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Pickup</label>
                                <input type="time" class="form-control form-control-sm" name="pickup_time[${ymd}]" value="07:00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Dropoff</label>
                                <input type="time" class="form-control form-control-sm" name="dropoff_time[${ymd}]" value="09:00">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold small text-muted mb-1">🌆 Afternoon Return</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Pickup</label>
                                <input type="time" class="form-control form-control-sm" name="return_pickup_time[${ymd}]" value="15:00">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Return Home</label>
                                <input type="time" class="form-control form-control-sm" name="return_home_time[${ymd}]" value="17:00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        $('#dateRowsContainer').html(html);
    }

    /* ──────────────────────────────────────────
       TOGGLE A SINGLE DATE
    ────────────────────────────────────────── */
    function toggleDate(ymd) {
        const idx = selectedDates.indexOf(ymd);
        if (idx !== -1) {
            selectedDates.splice(idx, 1);
            $(`.cal-day[data-date="${ymd}"]`).removeClass('cal-selected');
        } else {
            selectedDates.push(ymd);
            $(`.cal-day[data-date="${ymd}"]`).addClass('cal-selected');
        }
        renderDateRows();
    }

    /* ──────────────────────────────────────────
       EVENT DELEGATION (jQuery)
    ────────────────────────────────────────── */
    // Calendar navigation
    $(document).on('click', '#prevMonth', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    $(document).on('click', '#nextMonth', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    // Click a calendar day
    $(document).on('click', '.cal-day:not(.cal-disabled):not(.cal-other)', function () {
        toggleDate($(this).data('date'));
    });

    // Remove a date row
    $(document).on('click', '.btn-remove-date', function () {
        const ymd = $(this).data('date');
        selectedDates = selectedDates.filter(d => d !== ymd);
        $(`.cal-day[data-date="${ymd}"]`).removeClass('cal-selected');
        renderDateRows();
    });

    // Select All weekdays in range
    $('#btnSelectAll').on('click', () => {
        autoSelectAllWeekdays();
        renderCalendar(currentDate);
        renderDateRows();
    });

    // Clear All
    $('#btnClearAll').on('click', () => {
        selectedDates = [];
        renderCalendar(currentDate);
        renderDateRows();
    });

    /* ──────────────────────────────────────────
       INIT — auto-select then render
    ────────────────────────────────────────── */
    autoSelectAllWeekdays();
    renderCalendar(currentDate);
    renderDateRows();

});
</script>
@endpush
