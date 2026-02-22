@extends('backend.app')
@section('title', 'Ride Assign Create')

@section('css')
<style>
    /* ── Calendar ─────────────────────────────────────────────────── */
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    .calendar-nav {
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
    }
    .calendar-nav:hover { background: #0b5ed7; }
    .calendar-title { font-size: 1.1em; font-weight: 700; color: #343a40; }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 3px;
    }
    .cal-header-cell {
        background: #495057;
        color: #fff;
        text-align: center;
        padding: 8px 4px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 4px;
    }
    .cal-day {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 4px;
        text-align: center;
        cursor: pointer;
        font-size: 13px;
        min-height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .15s;
        user-select: none;
    }
    .cal-day:hover:not(.cal-disabled):not(.cal-other) {
        background: #cfe2ff;
        border-color: #0d6efd;
    }
    .cal-day.cal-other   { background: #f8f9fa; color: #ced4da; cursor: default; }
    .cal-day.cal-weekend { background: #fff5f5; color: #dc3545; }
    .cal-day.cal-disabled { opacity: .45; cursor: not-allowed; }
    .cal-day.cal-selected {
        background: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
        font-weight: 700;
    }
    .cal-day.cal-today {
        border-color: #0d6efd;
        font-weight: 700;
    }

    /* ── Date rows ────────────────────────────────────────────────── */
    .date-row {
        background: #fff;
        border: 1px solid #dee2e6;
        border-left: 4px solid #0d6efd;
        border-radius: 8px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: box-shadow .2s;
    }
    .date-row:hover { box-shadow: 0 2px 10px rgba(0,0,0,.08); }
    .date-row .date-label {
        font-weight: 700;
        font-size: 14px;
        color: #343a40;
        background: #e9ecef;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-bottom: 12px;
    }

    /* ── Capacity badge ───────────────────────────────────────────── */
    .cap-badge {
        font-size: 12px;
        padding: 5px 10px;
        border-radius: 20px;
        display: inline-block;
        margin-top: 6px;
        font-weight: 600;
        min-width: 180px;
        text-align: center;
    }
    .cap-badge.idle     { background: #e9ecef; color: #6c757d; }
    .cap-badge.loading  { background: #cff4fc; color: #055160; }
    .cap-badge.ok       { background: #d1e7dd; color: #0a3622; }
    .cap-badge.warn     { background: #fff3cd; color: #664d03; }
    .cap-badge.full     { background: #f8d7da; color: #58151c; }

    /* ── Section header ───────────────────────────────────────────── */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        margin-bottom: 12px;
    }

    /* ── Empty state ──────────────────────────────────────────────── */
    .empty-dates {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
        border: 2px dashed #dee2e6;
        border-radius: 8px;
    }
    .empty-dates .empty-icon { font-size: 40px; margin-bottom: 10px; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Ride Assign Create'])

<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-primary card-outline mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">New Ride Assignment</h3>
                        <a href="{{ route('admin.ride.assign.index') }}" class="btn btn-sm btn-outline-secondary">
                            ← Back
                        </a>
                    </div>

                    <div class="card-body">

                        {{-- Alerts --}}
                        @session('success')
                            <div class="alert alert-success">{{ $value }}</div>
                        @endsession
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
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
                                                    ${{ $subscription->kid_wage?->sell_price ?? $subscription->plan?->sell_price }} AUD
                                                </span>
                                            </li>
                                            <li class="list-group-item px-0 d-flex justify-content-between">
                                                <span class="text-muted fw-semibold">Wage Period</span>
                                                <span>
                                                    @if($subscription->kid_wage)
                                                        {{ \Carbon\Carbon::parse($subscription->kid_wage->start_date)->format('d M Y') }}
                                                        →
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

                        {{-- ══ FORM ══ --}}
                        <form action="{{ route('admin.ride.assign.store', $subscription->id) }}"
                              method="POST" id="assignmentForm">
                            @csrf

                            <input type="hidden" name="selected_dates" id="selectedDatesInput">
                            <input type="hidden" name="working_days"   id="totalWorkingDays" value="0">
                            <input type="hidden" name="fare"
                                   value="{{ $subscription->kid_wage?->sell_price ?? $subscription->plan?->sell_price }}">

                            {{-- ══ STEP 1: CALENDAR ══ --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">📅 Step 1 — Select Ride Dates</h6>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($subscription->kid_wage)
                                                <span class="badge bg-info text-dark">
                                                    {{ \Carbon\Carbon::parse($subscription->kid_wage->start_date)->format('d M') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($subscription->kid_wage->end_date)->format('d M Y') }}
                                                </span>
                                            @endif
                                            <span class="badge bg-secondary">Weekends disabled</span>
                                            <span class="badge bg-primary" id="selectedCountBadge">0 days selected</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    {{-- Calendar nav --}}
                                    <div class="calendar-header">
                                        <button type="button" class="calendar-nav" id="prevMonth">&#8249;</button>
                                        <span class="calendar-title" id="currentMonth"></span>
                                        <button type="button" class="calendar-nav" id="nextMonth">&#8250;</button>
                                    </div>

                                    {{-- Day headers + days --}}
                                    <div class="calendar-grid" id="calendarGrid"></div>

                                    {{-- Legend --}}
                                    <div class="d-flex gap-3 mt-3 flex-wrap">
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#0d6efd;border-radius:3px;vertical-align:middle"></span> Selected</span>
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#fff5f5;border:1px solid #dc3545;border-radius:3px;vertical-align:middle"></span> Weekend (disabled)</span>
                                        <span><span style="display:inline-block;width:14px;height:14px;background:#f8f9fa;border:1px solid #dee2e6;border-radius:3px;vertical-align:middle;opacity:.5"></span> Out of range</span>
                                    </div>
                                </div>
                            </div>

                            {{-- ══ STEP 2: DATE ROWS ══ --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 fw-bold">🚗 Step 2 — Assign Driver & Times</h6>
                                        <span class="text-muted small">Select dates on calendar above to see rows here</span>
                                    </div>
                                </div>
                                <div class="card-body" id="dateRowsContainer">
                                    {{-- Empty state --}}
                                    <div class="empty-dates" id="emptyState">
                                        <div class="empty-icon">📅</div>
                                        <div class="fw-semibold mb-1">No dates selected yet</div>
                                        <div class="small text-muted">Click on weekdays in the calendar above</div>
                                    </div>
                                </div>
                            </div>

                            {{-- ══ SUBMIT ══ --}}
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted small">
                                    <i class="bi bi-info-circle"></i>
                                    Each date creates a Morning + Afternoon ride automatically.
                                </div>
                                <button type="submit" class="btn btn-primary px-5" id="submitBtn" disabled>
                                    <i class="bi bi-check2-circle"></i> Create Ride Assignments
                                </button>
                            </div>

                        </form>

                    </div>{{-- /card-body --}}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {

    // ═══════════════════════════════════════════════════════════════
    // CONFIG
    // ═══════════════════════════════════════════════════════════════

    const MONTH_NAMES = ["January","February","March","April","May","June",
                         "July","August","September","October","November","December"];
    const DAY_HEADERS = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];

    // Kid wage range from PHP
    const rangeStart = new Date('{{ $subscription->kid_wage?->start_date ?? \Carbon\Carbon::parse($subscription->created_at)->format("Y-m-d") }}');
    const rangeEnd   = new Date('{{ $subscription->kid_wage?->end_date   ?? \Carbon\Carbon::parse($subscription->ends_at)->format("Y-m-d") }}');

    // Available drivers from PHP
    const driversData = [
        @if(isset($drivers))
            @foreach($drivers as $driver)
                { id: {{ $driver->id }}, name: "{{ addslashes($driver->driver_name) }} - {{ $driver->user?->phone }}" },
            @endforeach
        @endif
    ];

    // Capacity check endpoint
    const CHECK_URL  = '{{ route("admin.ride.assign.check-capacity") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // ═══════════════════════════════════════════════════════════════
    // STATE
    // ═══════════════════════════════════════════════════════════════

    let currentDate   = new Date();
    let selectedDates = []; // array of 'YYYY-MM-DD' strings

    // ═══════════════════════════════════════════════════════════════
    // UTILS
    // ═══════════════════════════════════════════════════════════════

    function toYMD(d) {
        return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    }

    function isWeekend(d)  { return d.getDay() === 0 || d.getDay() === 6; }

    function inRange(d) {
        const t = new Date(d); t.setHours(0,0,0,0);
        const s = new Date(rangeStart); s.setHours(0,0,0,0);
        const e = new Date(rangeEnd);   e.setHours(0,0,0,0);
        return t >= s && t <= e;
    }

    function formatLabel(ymd) {
        const [y, m, day] = ymd.split('-').map(Number);
        const d = new Date(y, m-1, day);
        return d.toLocaleDateString('en-AU', {
            weekday: 'short', day: '2-digit', month: 'short', year: 'numeric'
        });
    }

    function buildDriverOptions(selectedId) {
        let html = '<option value="">— Select Driver —</option>';
        driversData.forEach(d => {
            html += `<option value="${d.id}" ${d.id == selectedId ? 'selected' : ''}>${d.name}</option>`;
        });
        return html;
    }

    // ═══════════════════════════════════════════════════════════════
    // CALENDAR
    // ═══════════════════════════════════════════════════════════════

    function renderCalendar(date) {
        const year  = date.getFullYear();
        const month = date.getMonth();
        const today = toYMD(new Date());

        $('#currentMonth').text(`${MONTH_NAMES[month]} ${year}`);

        let html = '';

        // Day headers
        DAY_HEADERS.forEach(h => {
            html += `<div class="cal-header-cell">${h}</div>`;
        });

        const firstDow     = new Date(year, month, 1).getDay();
        const lastDate     = new Date(year, month+1, 0).getDate();
        const prevLastDate = new Date(year, month, 0).getDate();

        // Prev month trailing days
        for (let i = firstDow - 1; i >= 0; i--) {
            html += `<div class="cal-day cal-other cal-disabled">${prevLastDate - i}</div>`;
        }

        // Current month
        for (let d = 1; d <= lastDate; d++) {
            const dt      = new Date(year, month, d);
            const ymd     = toYMD(dt);
            const weekend = isWeekend(dt);
            const valid   = inRange(dt) && !weekend;
            const sel     = selectedDates.includes(ymd);
            const isToday = ymd === today;

            let cls = 'cal-day';
            if (sel)                           cls += ' cal-selected';
            else if (weekend)                  cls += ' cal-weekend';
            if (!valid)                        cls += ' cal-disabled';
            if (isToday && !sel)               cls += ' cal-today';

            html += `<div class="${cls}" data-date="${ymd}">${d}</div>`;
        }

        // Next month leading days
        const totalCells = firstDow + lastDate;
        const remainder  = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let d = 1; d <= remainder; d++) {
            html += `<div class="cal-day cal-other cal-disabled">${d}</div>`;
        }

        $('#calendarGrid').html(html);
    }

    // ═══════════════════════════════════════════════════════════════
    // DATE ROWS
    // ═══════════════════════════════════════════════════════════════

    function renderDateRows() {
        // Update hidden input + counter + button state
        $('#selectedDatesInput').val(JSON.stringify(selectedDates));
        $('#totalWorkingDays').val(selectedDates.length);
        $('#selectedCountBadge').text(
            selectedDates.length === 0
                ? '0 days selected'
                : `${selectedDates.length} ${selectedDates.length === 1 ? 'day' : 'days'} selected`
        );
        $('#submitBtn').prop('disabled', selectedDates.length === 0);

        if (selectedDates.length === 0) {
            $('#dateRowsContainer').html(`
                <div class="empty-dates" id="emptyState">
                    <div class="empty-icon">📅</div>
                    <div class="fw-semibold mb-1">No dates selected yet</div>
                    <div class="small text-muted">Click on weekdays in the calendar above</div>
                </div>
            `);
            return;
        }

        // Build rows for sorted dates
        let html = '';
        const sorted = [...selectedDates].sort();

        sorted.forEach((ymd, idx) => {
            const label = formatLabel(ymd);
            html += `
            <div class="date-row" data-row-date="${ymd}">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="date-label">📅 ${label}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-date"
                            data-date="${ymd}" title="Remove this date">
                        ✕ Remove
                    </button>
                </div>

                <div class="row g-3 align-items-start">

                    {{-- ── Driver ── --}}
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            <i class="bi bi-person-badge"></i> Assigned Driver
                        </label>
                        <select class="form-select form-select-sm driver-select"
                                name="driver[${ymd}]"
                                data-date="${ymd}"
                                required>
                            ${buildDriverOptions('')}
                        </select>
                        {{-- Capacity badge --}}
                        <div class="cap-badge idle mt-2" data-badge="${ymd}">
                            Select driver & pickup time
                        </div>
                    </div>

                    {{-- ── Date (readonly) ── --}}
                    <div class="col-md-1">
                        <label class="form-label fw-semibold small text-muted mb-1">Date</label>
                        <input type="date" class="form-control form-control-sm"
                               name="ride_date[${ymd}]" value="${ymd}" readonly>
                    </div>

                    {{-- ── Morning ── --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            🌅 Morning Trip
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Pickup Time</label>
                                <input type="time"
                                       class="form-control form-control-sm pickup-time"
                                       name="pickup_time[${ymd}]"
                                       value="07:00"
                                       data-date="${ymd}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Dropoff Time</label>
                                <input type="time"
                                       class="form-control form-control-sm"
                                       name="dropoff_time[${ymd}]"
                                       value="09:00"
                                       data-date="${ymd}">
                            </div>
                        </div>
                    </div>

                    {{-- ── Afternoon ── --}}
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small text-muted mb-1">
                            🌆 Afternoon Return
                        </label>
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label small mb-1">Pickup Time</label>
                                <input type="time"
                                       class="form-control form-control-sm"
                                       name="return_pickup_time[${ymd}]"
                                       value="15:00"
                                       data-date="${ymd}">
                            </div>
                            <div class="col-6">
                                <label class="form-label small mb-1">Return Home</label>
                                <input type="time"
                                       class="form-control form-control-sm"
                                       name="return_home_time[${ymd}]"
                                       value="17:00"
                                       data-date="${ymd}">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            `;
        });

        $('#dateRowsContainer').html(html);
    }

    // ═══════════════════════════════════════════════════════════════
    // TOGGLE DATE SELECTION
    // ═══════════════════════════════════════════════════════════════

    function toggleDate(ymd) {
        if (selectedDates.includes(ymd)) {
            // Deselect
            selectedDates = selectedDates.filter(d => d !== ymd);
            $(`.cal-day[data-date="${ymd}"]`).removeClass('cal-selected');
        } else {
            // Select
            selectedDates.push(ymd);
            $(`.cal-day[data-date="${ymd}"]`).addClass('cal-selected');
        }
        renderDateRows();
    }

    // ═══════════════════════════════════════════════════════════════
    // CAPACITY CHECK  (AJAX)
    // ═══════════════════════════════════════════════════════════════

    // Debounce timer per date
    const debounceTimers = {};

    function checkCapacity(ymd) {
        const row      = $(`[data-row-date="${ymd}"]`);
        const driverId = row.find('.driver-select').val();
        const pickup   = row.find(`.pickup-time[data-date="${ymd}"]`).val();
        const badge    = $(`[data-badge="${ymd}"]`);

        // Reset if not enough info
        if (!driverId || !pickup) {
            badge.attr('class','cap-badge idle').text('Select driver & pickup time');
            return;
        }

        // Loading state
        badge.attr('class','cap-badge loading').text('⏳ Checking capacity...');

        // Debounce 400ms
        clearTimeout(debounceTimers[ymd]);
        debounceTimers[ymd] = setTimeout(function () {
            $.ajax({
                url:    CHECK_URL,
                method: 'POST',
                data: {
                    _token:      CSRF_TOKEN,
                    driver_id:   driverId,
                    date:        ymd,
                    pickup_time: pickup,
                },
                success: function (res) {
                    if (res.available) {
                        badge.attr('class','cap-badge ok')
                             .text(`✅ ${res.capacity.message}`);
                    } else if (!res.has_buffer) {
                        badge.attr('class','cap-badge warn')
                             .text('⚠️ Need 10 min buffer after last drop-off');
                    } else {
                        badge.attr('class','cap-badge full')
                             .text(`🚫 ${res.capacity.message}`);
                    }
                },
                error: function () {
                    badge.attr('class','cap-badge warn')
                         .text('⚠️ Could not check capacity');
                }
            });
        }, 400);
    }

    // ═══════════════════════════════════════════════════════════════
    // EVENT LISTENERS
    // ═══════════════════════════════════════════════════════════════

    // Calendar day click
    $(document).on('click', '.cal-day:not(.cal-disabled):not(.cal-other)', function () {
        const ymd = $(this).data('date');
        if (ymd) toggleDate(ymd);
    });

    // Calendar navigation
    $('#prevMonth').on('click', function (e) {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    $('#nextMonth').on('click', function (e) {
        e.preventDefault();
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    // Remove date button
    $(document).on('click', '.btn-remove-date', function () {
        const ymd = $(this).data('date');
        selectedDates = selectedDates.filter(d => d !== ymd);
        $(`.cal-day[data-date="${ymd}"]`).removeClass('cal-selected');
        renderDateRows();
    });

    // Driver change → capacity check
    $(document).on('change', '.driver-select', function () {
        const ymd = $(this).data('date');
        checkCapacity(ymd);
    });

    // Pickup time change → capacity check
    $(document).on('change', '.pickup-time', function () {
        const ymd = $(this).data('date');
        checkCapacity(ymd);
    });

    // Form submit guard
    $('#assignmentForm').on('submit', function (e) {
        if (selectedDates.length === 0) {
            e.preventDefault();
            alert('Please select at least one date.');
            return false;
        }

        // Check if any driver is missing
        let missing = false;
        $('[data-row-date]').each(function () {
            const driver = $(this).find('.driver-select').val();
            if (!driver) {
                missing = true;
                const date = $(this).data('row-date');
                $(this).find('.driver-select').addClass('is-invalid');
            }
        });

        if (missing) {
            e.preventDefault();
            alert('Please assign a driver to all selected dates.');
            return false;
        }

        // Disable submit to prevent double-click
        $('#submitBtn').prop('disabled', true).text('Saving...');
        return true;
    });

    // ═══════════════════════════════════════════════════════════════
    // INIT
    // ═══════════════════════════════════════════════════════════════

    renderCalendar(currentDate);
    renderDateRows();

    @if(session('success'))
        toastr.success("{{ session('success') }}");
    @endif
    @if(session('error'))
        toastr.error("{{ session('error') }}");
    @endif

});
</script>
@endpush
@endsection
