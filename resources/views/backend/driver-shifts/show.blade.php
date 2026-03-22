{{-- resources/views/backend/driver-shifts/show.blade.php --}}
@extends('backend.app')
@section('title', 'Shift Details')

@section('css')
    <style>
        /* ── Seat Grid ── */
        .seat-grid {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 8px 0 20px;
        }

        .seat-box {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            border: 2px solid #dee2e6;
            transition: transform .15s, box-shadow .15s;
            cursor: default;
            position: relative;
        }

        .seat-box.clickable {
            cursor: pointer;
        }

        .seat-box.clickable:hover {
            transform: scale(1.18);
            box-shadow: 0 3px 10px rgba(0, 0, 0, .2);
        }

        .seat-box.booked {
            background: #0d6efd;
            color: #fff;
            border-color: #0a58ca;
        }

        .seat-box.booked.clickable:hover {
            background: #dc3545;
            border-color: #b02a37;
        }

        .seat-box.completed {
            background: #adb5bd;
            color: #fff;
            border-color: #6c757d;
        }

        .seat-box.empty {
            background: #f0f4ff;
            color: #6c757d;
            border-color: #c5d0e6;
        }

        .seat-box.empty.clickable:hover {
            background: #dbeafe;
            border-color: #0d6efd;
            color: #0d6efd;
        }

        .seat-box.active-seat {
            outline: 3px solid #0d6efd;
            outline-offset: 2px;
        }

        .seat-box.pulse {
            animation: pulse .4s ease;
        }

        @keyframes pulse {
            0% {
                transform: scale(1)
            }

            50% {
                transform: scale(1.3)
            }

            100% {
                transform: scale(1)
            }
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0)
            }

            25% {
                transform: translateX(-6px)
            }

            75% {
                transform: translateX(6px)
            }
        }

        /* ── Driver section divider ── */
        .driver-seat-section {
            margin-bottom: 14px;
        }

        .driver-seat-label {
            font-size: .72rem;
            font-weight: 700;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        /* ── Ride Cards ── */
        .ride-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 11px 13px;
            cursor: pointer;
            transition: all .15s;
            margin-bottom: 7px;
            user-select: none;
            background: #fff;
        }

        .ride-card:hover {
            background: #f0f6ff;
            border-color: #9ec5fe;
        }

        .ride-card.selected {
            background: #dbeafe;
            border-color: #0d6efd;
        }

        .ride-card .kid-name {
            font-weight: 700;
            font-size: .9rem;
        }

        .ride-card .ride-meta {
            font-size: .74rem;
            color: #6c757d;
            margin-top: 2px;
        }

        /* ── Route Header ── */
        .route-header {
            background: #1a1d24;
            color: #e2e8f0;
            padding: 6px 13px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin: 12px 0 7px;
        }

        .time-badge {
            font-size: .7rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            display: inline-block;
        }

        /* ── Left panel sticky ── */
        #assignPanel {
            position: sticky;
            top: 76px;
        }

        /* ── Assigned list ── */
        .assigned-item {
            font-size: .8rem;
            padding: 5px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .assigned-item:last-child {
            border-bottom: none;
        }

        /* ── Driver chip ── */
        .driver-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #f1f3f5;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 3px 9px;
            font-size: .74rem;
            font-weight: 600;
            margin: 2px;
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Shift Details'])

    @can('view-driver-shifts')
        <div class="app-content">
            <div class="container-fluid">

                @session('success')
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ $value }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endsession
                @if ($errors->any())
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $e)
                            <div>• {{ $e }}</div>
                        @endforeach
                    </div>
                @endif

                @php $def = \App\Models\DriverShift::SHIFTS[$shift->shift_number]; @endphp

                <div class="row g-4">

                    {{-- ══════════════════════════════════════════
                LEFT — Shift info, drivers, seat grid
            ══════════════════════════════════════════ --}}
                    <div class="col-lg-4">
                        <div id="assignPanel">

                            <div class="card shadow-sm border-0 mb-3">
                                {{-- Header --}}
                                <div class="card-header fw-bold"
                                    style="background:{{ $shift->shift_number == 1 ? '#fbbf24' : ($shift->shift_number == 2 ? '#f06292' : '#5c6bc0') }};
                                color:{{ $shift->shift_number == 1 ? '#78350f' : '#fff' }}">
                                    {{ $def['icon'] }} {{ $shift->shift_label }}
                                    <span class="fw-normal small ms-1">{{ $shift->start_time }}–{{ $shift->end_time }}</span>
                                    <span
                                        class="badge ms-2 bg-{{ $shift->status === 'confirmed' ? 'success' : ($shift->status === 'active' ? 'info' : 'light text-dark') }}">
                                        {{ ucfirst($shift->status) }}
                                    </span>
                                </div>

                                <div class="card-body pb-2">

                                    {{-- Date / window --}}
                                    <div class="d-flex justify-content-between small mb-3 text-muted">
                                        <span>📅 {{ \Carbon\Carbon::parse($shift->date)->format('D d M Y') }}</span>
                                        <span>🕐 {{ $shift->start_time }} – {{ $shift->end_time }}</span>
                                    </div>

                                    {{-- Drivers on shift --}}
                                    <div class="fw-semibold small text-muted mb-1">🚗 Drivers</div>
                                    <div class="mb-2">
                                        @forelse($shift->drivers as $d)
                                            <span class="driver-chip">
                                                {{ $d->user?->first_name }}
                                                <span class="text-muted" style="font-size:.68rem">
                                                    {{ $d->vehicleType?->name ?? '' }}
                                                    ({{ $driverSeatData[$d->id]['free'] }}/{{ $driverSeatData[$d->id]['max'] }}
                                                    free)
                                                </span>
                                                @if (in_array($shift->status, ['draft', 'confirmed']))
                                                    @can('delete-driver-shifts')
                                                        <form
                                                            action="{{ route('admin.driver.shifts.removeDriver', [$shift->id, $d->id]) }}"
                                                            method="POST" style="display:inline"
                                                            onsubmit="return confirm('Remove driver?')">
                                                            @csrf @method('DELETE')
                                                            <button
                                                                style="background:none;border:none;color:#dc3545;cursor:pointer;padding:0;font-size:.8rem">✕</button>
                                                        </form>
                                                    @endcan
                                                @endif
                                            </span>
                                        @empty
                                            <span class="text-muted small fst-italic">No drivers yet</span>
                                        @endforelse
                                    </div>

                                    {{-- Add driver --}}
                                    @if (in_array($shift->status, ['draft', 'confirmed']))
                                        @can('edit-driver-shifts')
                                            <form action="{{ route('admin.driver.shifts.addDriver', $shift->id) }}" method="POST"
                                                class="d-flex gap-2 mb-3">
                                                @csrf
                                                <select name="driver_id" class="form-select form-select-sm" required>
                                                    <option value="">+ Add Driver</option>
                                                    @foreach ($allDrivers as $d)
                                                        @if (!$shift->drivers->contains('id', $d->id))
                                                            <option value="{{ $d->id }}">
                                                                {{ $d->user?->first_name }} {{ $d->user?->last_name }}
                                                                ({{ $d->vehicleType?->name ?? 'No vehicle' }})
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <button type="submit"
                                                    class="btn btn-sm btn-outline-primary text-nowrap">Add</button>
                                            </form>
                                        @endcan
                                    @endif

                                    {{-- ── DRIVER SELECTOR for assignment ── --}}
                                    @can('edit-driver-shifts')
                                        @if (in_array($shift->status, ['draft', 'confirmed']) && $shift->drivers->isNotEmpty())
                                            <div class="bg-light rounded p-2 mb-3">
                                                <label class="fw-semibold small d-block mb-1">
                                                    🪑 Assign rides to driver:
                                                </label>
                                                <select id="assignDriverSelect" class="form-select form-select-sm"
                                                    onchange="onDriverChange()">
                                                    <option value="">— Select driver —</option>
                                                    @foreach ($shift->drivers as $d)
                                                        @php $sd = $driverSeatData[$d->id]; @endphp
                                                        <option value="{{ $d->id }}" data-max="{{ $sd['max'] }}"
                                                            data-used="{{ $sd['used'] }}" data-free="{{ $sd['free'] }}"
                                                            data-name="{{ $d->user?->first_name }}"
                                                            {{ $sd['free'] === 0 ? 'disabled' : '' }}>
                                                            {{ $d->user?->first_name }} {{ $d->user?->last_name }}
                                                            — {{ $sd['free'] }}/{{ $sd['max'] }} seats free
                                                            {{ $sd['free'] === 0 ? '(FULL)' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        {{-- ── SEAT GRID PER DRIVER ── --}}
                                        @if (in_array($shift->status, ['draft', 'confirmed']))
                                            <div class="fw-semibold small text-muted mb-1">
                                                🪑 Seat Grid
                                                <span class="fw-normal text-primary" style="font-size:.7rem">
                                                    · click empty to assign · click blue to remove
                                                </span>
                                            </div>

                                            @forelse($shift->drivers as $driver)
                                                @php $sd = $driverSeatData[$driver->id]; @endphp
                                                <div class="driver-seat-section">
                                                    <div class="driver-seat-label">
                                                        🚗 {{ $driver->user?->first_name }}
                                                        <span class="badge bg-{{ $sd['free'] > 0 ? 'success' : 'danger' }} ms-1"
                                                            style="font-size:.65rem">
                                                            {{ $sd['free'] }}/{{ $sd['max'] }} free
                                                        </span>
                                                    </div>
                                                    <div class="seat-grid">
                                                        @for ($s = 1; $s <= $sd['max']; $s++)
                                                            @php
                                                                // Use seatMap for O(1) lookup by exact seat number
                                                                $sr = $sd['seatMap'][$s] ?? null;
                                                                $isDone =
                                                                    $sr &&
                                                                    in_array($sr->ride?->status, [
                                                                        'completed',
                                                                        'cancelled',
                                                                        'canceled',
                                                                    ]);
                                                                $isBooked = $sr && !$isDone;
                                                                $kidName =
                                                                    $sr?->ride?->rideAssign?->subscription?->kid
                                                                        ?->first_name ?? '';
                                                            @endphp

                                                            @if ($isBooked)
                                                                @php
                                                                    $pickupFmt = \Carbon\Carbon::parse(
                                                                        $sr->ride?->pickup,
                                                                    )->format('H:i');
                                                                    $dropoffFmt = \Carbon\Carbon::parse(
                                                                        $sr->ride?->drop_off,
                                                                    )->format('H:i');
                                                                @endphp
                                                                <div class="seat-box booked clickable"
                                                                    id="seat_{{ $driver->id }}_{{ $s }}"
                                                                    title="{{ $kidName }} · {{ $pickupFmt }}–{{ $dropoffFmt }}&#10;Click to remove"
                                                                    onclick="confirmRemoveSeat({{ $shift->id }}, {{ $sr->ride_id }}, '{{ addslashes($kidName) }}', {{ $s }})">
                                                                    {{ $s }}
                                                                    <span
                                                                        style="position:absolute;bottom:-14px;left:50%;transform:translateX(-50%);
                                                            font-size:8px;color:#6c757d;white-space:nowrap">
                                                                        {{ $pickupFmt }}
                                                                    </span>
                                                                </div>
                                                            @elseif($isDone)
                                                                <div class="seat-box completed" title="Done: {{ $kidName }}">
                                                                    {{ $s }}
                                                                </div>
                                                            @else
                                                                <div class="seat-box empty clickable"
                                                                    id="seat_{{ $driver->id }}_{{ $s }}"
                                                                    title="Assign ride to seat {{ $s }} for {{ $driver->user?->first_name }}"
                                                                    onclick="selectSeat({{ $driver->id }}, {{ $s }}, '{{ addslashes($driver->user?->first_name ?? '') }}')">
                                                                    {{ $s }}
                                                                </div>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-muted small fst-italic mb-2">Add drivers above to see seat grid.
                                                </div>
                                            @endforelse

                                            {{-- Active seat info banner --}}
                                            <div id="seatAssignBox"
                                                class="alert alert-primary py-2 px-3 small d-none
                                                            d-flex justify-content-between align-items-center">
                                                <span>
                                                    Assigning to <strong>Seat <span id="activeSeatLabel">—</span></strong>
                                                    for <strong><span id="activeDriverLabel">—</span></strong>
                                                </span>
                                                <button type="button" class="btn-close btn-close-sm"
                                                    onclick="cancelSeatSelect()"></button>
                                            </div>
                                        @endif
                                    @endcan
                                    {{-- Actions --}}
                                    <div class="d-grid gap-2 mt-3">
                                        @if ($shift->status === 'draft')
                                            @can('edit-driver-shifts')
                                                <form action="{{ route('admin.driver.shifts.confirm', $shift->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button class="btn btn-success w-100 fw-semibold">✓ Confirm Shift</button>
                                                </form>
                                            @endcan
                                            @can('delete-driver-shifts')
                                                <form action="{{ route('admin.driver.shifts.destroy', $shift->id) }}"
                                                    method="POST"
                                                    onsubmit="return confirm('Delete shift? All rides will be unlinked.')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-outline-danger w-100">✕ Delete Shift</button>
                                                </form>
                                            @endcan
                                        @endif
                                        <a href="{{ route('admin.driver.shifts.index', ['date' => $shift->date->toDateString()]) }}"
                                            class="btn btn-outline-secondary">← All Shifts</a>
                                    </div>

                                </div>
                            </div>

                            {{-- Assigned rides list --}}
                            @if ($shift->shiftRides->isNotEmpty())
                                <div class="card shadow-sm border-0">
                                    <div class="card-header bg-light fw-semibold small">
                                        📋 Assigned Rides ({{ $shift->shiftRides->count() }})
                                    </div>
                                    <div class="card-body p-2">
                                        @foreach ($shift->shiftRides->sortBy('seat_number') as $sr)
                                            <div class="assigned-item px-1">
                                                <div>
                                                    <span
                                                        class="badge bg-{{ $sr->type === 'completed' ? 'secondary' : 'primary' }} me-1"
                                                        style="font-size:.65rem">
                                                        S{{ $sr->seat_number }}
                                                    </span>
                                                    <strong>{{ $sr->ride->rideAssign?->subscription?->kid?->first_name ?? '—' }}</strong>
                                                    <span class="text-muted" style="font-size:.7rem">
                                                        · {{ $sr->ride->pickupLocation?->city }}
                                                        → {{ $sr->ride->dropoffLocation?->city }}
                                                        · {{ \Carbon\Carbon::parse($sr->ride->pickup)->format('H:i') }}
                                                    </span>
                                                    @if ($sr->ride->driver)
                                                        <span class="badge bg-light text-dark border ms-1"
                                                            style="font-size:.63rem">
                                                            @php
                                                                $rideDriver = $sr->ride->driver;
                                                                $driverName =
                                                                    $rideDriver?->first_name ?? // if driver IS a User
                                                                    ($rideDriver?->user?->first_name ?? // if driver has user relation
                                                                        '—');
                                                            @endphp
                                                            🚗 {{ $driverName }}
                                                        </span>
                                                    @endif
                                                </div>
                                                @if (in_array($shift->status, ['draft', 'confirmed']) && $sr->type !== 'completed')
                                                    @can('delete-driver-shifts')
                                                    <form
                                                        action="{{ route('admin.driver.shifts.removeRide', [$shift->id, $sr->ride_id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Remove ride? Seat will be freed.')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-outline-danger py-0 px-1"
                                                            style="font-size:10px">✕</button>
                                                    </form>
                                                    @endcan
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- ══════════════════════════════════════════
                RIGHT — Available unassigned rides
            ══════════════════════════════════════════ --}}
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold">🕐 Unassigned Rides in Window</span>
                                    <span class="badge bg-info text-dark ms-2">
                                        {{ $shift->start_time }} – {{ $shift->end_time }}
                                    </span>
                                </div>
                                <span
                                    class="badge bg-{{ $availableRides->flatten()->count() > 0 ? 'success' : 'secondary' }} fs-6">
                                    {{ $availableRides->flatten()->count() }} rides
                                </span>
                            </div>

                            <div class="card-body">

                                {{-- Instruction --}}
                                <div class="alert alert-primary py-2 small mb-3 d-flex align-items-center gap-2"
                                    id="instructionBanner">
                                    <span style="font-size:1.2rem">👈</span>
                                    <span>
                                        <strong>1.</strong> Select driver (left) &nbsp;
                                        <strong>2.</strong> Click empty seat (left grid) &nbsp;
                                        <strong>3.</strong> Click a ride here to assign it
                                    </span>
                                </div>
                                <div class="alert alert-warning py-2 small mb-3 d-none" id="noSeatWarning">
                                    ⚠️ Click an empty <strong>seat</strong> on the left first, then pick a ride.
                                </div>

                                @if ($shift->drivers->isEmpty())
                                    <div class="alert alert-warning">
                                        ⚠️ Add a driver to this shift first (left panel).
                                    </div>
                                @elseif($availableRides->isEmpty())
                                    <div class="text-center py-4 text-muted"
                                        style="border:2px dashed #dee2e6;border-radius:10px">
                                        <div style="font-size:2.5rem">🕐</div>
                                        <div class="fw-semibold mt-2">No unassigned rides in this time window</div>
                                        <div class="small mt-1">
                                            Pickup between <strong>{{ $shift->start_time }}</strong>
                                            and <strong>{{ $shift->end_time }}</strong>
                                            on <strong>{{ \Carbon\Carbon::parse($shift->date)->format('d M Y') }}</strong>
                                        </div>
                                    </div>
                                @else
                                    @foreach ($availableRides as $routeKey => $rides)
                                        @php
                                            $first = $rides->first();
                                            $pickup = $first->pickupLocation;
                                            $drop = $first->dropoffLocation;
                                        @endphp

                                        <div class="route-header">
                                            📍 {{ $pickup?->city ?? ($pickup?->address ?? '?') }}
                                            → {{ $drop?->city ?? ($drop?->address ?? '?') }}
                                            <span class="badge bg-secondary ms-2" style="font-size:.68rem">
                                                {{ $rides->count() }}
                                            </span>
                                        </div>

                                        @foreach ($rides->sortBy('pickup') as $ride)
                                            @php
                                                $kid = $ride->rideAssign?->subscription?->kid;
                                                $parent = $ride->rideAssign?->subscription?->user;
                                                $isMorning = in_array($ride->ride_type, ['pickup', 'morning']);
                                            @endphp
                                            @php
                                                $pickupLoc = $ride->pickupLocation;
                                                $dropoffLoc = $ride->dropoffLocation;
                                                $pickupAddr =
                                                    $pickupLoc?->city ??
                                                    ($pickupLoc?->street1 ?? ($pickupLoc?->address ?? '—'));
                                                $dropoffAddr =
                                                    $dropoffLoc?->city ??
                                                    ($dropoffLoc?->street1 ?? ($dropoffLoc?->address ?? '—'));
                                            @endphp
                                            <div class="ride-card" id="rideCard_{{ $ride->id }}"
                                                onclick="assignRideToSeat({{ $ride->id }})">
                                                <div class="d-flex align-items-start gap-3">

                                                    {{-- Time column --}}
                                                    <div class="text-center" style="min-width:46px">
                                                        <div style="font-size:1.3rem">{{ $isMorning ? '🌅' : '🌆' }}</div>
                                                        <span
                                                            class="time-badge {{ $isMorning ? 'bg-primary text-white' : 'bg-warning text-dark' }}">
                                                            {{ \Carbon\Carbon::parse($ride->pickup)->format('H:i') }}
                                                        </span>
                                                    </div>

                                                    {{-- Info column --}}
                                                    <div class="flex-grow-1">

                                                        {{-- Row 1: kid name + type badge --}}
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div class="kid-name">
                                                                {{ $kid?->first_name }} {{ $kid?->last_name }}
                                                            </div>
                                                            <span
                                                                class="badge bg-{{ $isMorning ? 'primary' : 'warning text-dark' }} ms-2">
                                                                {{ $isMorning ? 'Morning' : 'Afternoon' }}
                                                            </span>
                                                        </div>

                                                        {{-- Row 2: parent --}}
                                                        <div class="ride-meta">
                                                            👨‍👩‍👦 {{ $parent?->first_name }} {{ $parent?->last_name }}
                                                            · 📞 {{ $parent?->phone }}
                                                        </div>

                                                        {{-- Row 3: pickup → dropoff location --}}
                                                        <div class="ride-meta mt-1"
                                                            style="background:#f8f9fa;border-radius:6px;padding:4px 7px;
                                                    border-left:3px solid #0d6efd">
                                                            📍 <strong>{{ $pickupAddr }}</strong>
                                                            <span class="mx-1 text-muted">→</span>
                                                            🏁 <strong>{{ $dropoffAddr }}</strong>
                                                        </div>

                                                        {{-- Row 4: pickup time → dropoff time + ride id --}}
                                                        <div class="ride-meta mt-1">
                                                            ⏰
                                                            <strong>{{ \Carbon\Carbon::parse($ride->pickup)->format('H:i') }}</strong>
                                                            →
                                                            <strong>{{ \Carbon\Carbon::parse($ride->drop_off)->format('H:i') }}</strong>
                                                            <span class="text-muted ms-2">#{{ $ride->id }}</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <form id="removeForm" method="POST" style="display:none">@csrf @method('DELETE')</form>
    @endcan
@endsection

@push('scripts')
    <script>
        // ── State ──────────────────────────────────────────
        let _activeSeat = null; // seat number (integer)
        let _activeDriver = null; // driver id
        let _activeDriverName = '';
        const shiftId = {{ $shift->id }};
        const assignRideUrl = '{{ route('admin.driver.shifts.assignRide', $shift->id) }}';
        const removeRideBase = '{{ route('admin.driver.shifts.removeRide', [$shift->id, 0]) }}'.replace('/0', '/');
        const csrfToken = '{{ csrf_token() }}';

        // ── When driver dropdown changes ───────────────────
        function onDriverChange() {
            cancelSeatSelect();
        }

        // ── Click an empty seat ────────────────────────────
        function selectSeat(driverId, seatNum, driverName) {
            // Auto-sync dropdown to match the seat's driver
            const sel = document.getElementById('assignDriverSelect');
            if (sel) {
                sel.value = driverId;
            }

            // Deselect previous
            clearActiveSeat();

            _activeSeat = seatNum;
            _activeDriver = driverId;
            _activeDriverName = driverName;

            // Highlight
            const el = document.getElementById('seat_' + driverId + '_' + seatNum);
            if (el) {
                el.classList.add('active-seat', 'pulse');
                setTimeout(() => el.classList.remove('pulse'), 400);
            }

            document.getElementById('activeSeatLabel').textContent = seatNum;
            document.getElementById('activeDriverLabel').textContent = driverName;
            document.getElementById('seatAssignBox').classList.remove('d-none');
            document.getElementById('instructionBanner').classList.add('d-none');
            document.getElementById('noSeatWarning').classList.add('d-none');

            if (window.innerWidth < 992) {
                document.querySelector('.col-lg-8')?.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        }

        // ── Cancel seat selection ──────────────────────────
        function cancelSeatSelect() {
            clearActiveSeat();
            document.getElementById('seatAssignBox')?.classList.add('d-none');
            document.getElementById('instructionBanner')?.classList.remove('d-none');
            document.querySelectorAll('.ride-card.selected').forEach(c => c.classList.remove('selected'));
        }

        function clearActiveSeat() {
            if (_activeSeat !== null && _activeDriver !== null) {
                const el = document.getElementById('seat_' + _activeDriver + '_' + _activeSeat);
                if (el) el.classList.remove('active-seat');
            }
            _activeSeat = null;
            _activeDriver = null;
            _activeDriverName = '';
        }

        // ── Click a ride → assign to active seat ──────────
        function assignRideToSeat(rideId) {
            if (_activeSeat === null || _activeDriver === null) {
                document.getElementById('noSeatWarning')?.classList.remove('d-none');
                document.getElementById('instructionBanner')?.classList.add('d-none');
                // Shake the seat grid
                document.querySelectorAll('.seat-grid').forEach(g => {
                    g.style.animation = 'none';
                    g.offsetHeight;
                    g.style.animation = 'shake .35s ease';
                });
                return;
            }

            const card = document.getElementById('rideCard_' + rideId);
            if (card) card.classList.add('selected');

            fetch(assignRideUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        ride_id: rideId,
                        seat_number: _activeSeat,
                        driver_id: _activeDriver,
                    }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast('✅ Seat ' + _activeSeat + ' → ' + _activeDriverName + ' · ' + (data.seats_available) +
                            ' seats left', 'success');
                        setTimeout(() => location.reload(), 800);
                    } else {
                        showToast('❌ ' + (data.message ?? 'Assignment failed.'), 'danger');
                        if (card) card.classList.remove('selected');
                    }
                })
                .catch(() => {
                    showToast('❌ Network error.', 'danger');
                    if (card) card.classList.remove('selected');
                });
        }

        // ── Click a booked seat → remove ride ─────────────
        function confirmRemoveSeat(shiftId, rideId, kidName, seatNum) {
            if (!confirm('Remove ' + kidName + ' from Seat ' + seatNum + '?\nSeat will be freed.')) return;
            const form = document.getElementById('removeForm');
            form.action = removeRideBase + rideId;
            form.submit();
        }

        // ── Toast ──────────────────────────────────────────
        function showToast(msg, type = 'success') {
            const t = document.createElement('div');
            t.className = 'alert alert-' + type + ' position-fixed shadow';
            t.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:280px;font-weight:600;font-size:.88rem';
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => t.remove(), 3500);
        }
    </script>
@endpush
