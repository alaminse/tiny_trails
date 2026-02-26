{{-- resources/views/backend/driver-shifts/show.blade.php --}}
@extends('backend.app')
@section('title', 'Shift Details')

@section('css')
<style>
    .ride-card {
        border:2px solid #dee2e6; border-radius:10px; padding:12px 14px;
        cursor:pointer; transition:all .15s; margin-bottom:8px;
        background:#fff; user-select:none;
    }
    .ride-card:hover    { background:#f0f6ff; border-color:#9ec5fe; }
    .ride-card.selected { background:#dbeafe; border-color:#0d6efd; }
    .route-header {
        background:#1e293b; color:#e2e8f0; padding:7px 14px;
        border-radius:8px; font-size:.82rem; font-weight:600; margin:14px 0 8px;
    }
    .time-badge {
        font-size:.7rem; font-weight:700; padding:2px 7px;
        border-radius:20px; display:inline-block;
    }
    .driver-chip {
        display:inline-flex; align-items:center; gap:5px;
        background:#f1f3f5; border:1px solid #dee2e6; border-radius:20px;
        padding:4px 10px; font-size:.78rem; font-weight:600; margin:3px;
    }
    .assigned-item {
        font-size:.82rem; padding:6px 0; border-bottom:1px solid #f0f0f0;
        display:flex; justify-content:space-between; align-items:center;
    }
    .assigned-item:last-child { border:none; }
    .empty-state {
        text-align:center; padding:2.5rem 1rem;
        color:#adb5bd; border:2px dashed #dee2e6; border-radius:10px;
    }
    #leftPanel { position:sticky; top:80px; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Shift Details'])

<div class="app-content">
<div class="container-fluid">

    @session('success')
        <div class="alert alert-success alert-dismissible fade show">
            {{ $value }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endsession
    @if($errors->any())
        <div class="alert alert-danger">@foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach</div>
    @endif

    @php $def = \App\Models\DriverShift::SHIFTS[$shift->shift_number]; @endphp

    <div class="row g-4">

        {{-- ══════════ LEFT ══════════ --}}
        <div class="col-lg-4">
        <div id="leftPanel">

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header fw-bold text-white"
                     style="background:{{ $shift->shift_number==1?'#fbbf24':($shift->shift_number==2?'#f06292':'#5c6bc0') }};
                            color:{{ $shift->shift_number==1?'#78350f':'#fff' }} !important">
                    {{ $def['icon'] }} {{ $shift->shift_label }}
                    <span class="fw-normal small ms-1">{{ $shift->start_time }}–{{ $shift->end_time }}</span>
                </div>
                <div class="card-body">

                    {{-- Info list --}}
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item px-0 d-flex justify-content-between py-2">
                            <span class="text-muted">Date</span>
                            <strong>{{ \Carbon\Carbon::parse($shift->date)->format('D, d M Y') }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between py-2">
                            <span class="text-muted">Window</span>
                            <strong class="text-primary">{{ $shift->start_time }} – {{ $shift->end_time }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between py-2">
                            <span class="text-muted">Rides Assigned</span>
                            <strong>{{ $shift->shiftRides->count() }}</strong>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between py-2">
                            <span class="text-muted">Status</span>
                            <span class="badge bg-{{ $shift->status==='confirmed'?'success':($shift->status==='active'?'info':'secondary') }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </li>
                    </ul>

                    {{-- Drivers --}}
                    <div class="fw-semibold small text-muted mb-2">🚗 Assigned Drivers</div>
                    <div class="mb-2">
                        @forelse($shift->drivers as $driver)
                            <span class="driver-chip">
                                {{ $driver->user?->first_name }} {{ $driver->user?->last_name }}
                                <span class="text-muted" style="font-size:.7rem">
                                    · {{ $driver->vehicleType?->name ?? '—' }}
                                    ({{ $driver->vehicleType?->max_capacity ?? '?' }} seats)
                                </span>
                                @if(in_array($shift->status, ['draft','confirmed']))
                                <form action="{{ route('admin.driver.shifts.removeDriver', [$shift->id, $driver->id]) }}"
                                      method="POST" style="display:inline" onsubmit="return confirm('Remove driver?')">
                                    @csrf @method('DELETE')
                                    <button style="cursor:pointer;color:#dc3545;background:none;border:none;padding:0;font-size:.85rem">✕</button>
                                </form>
                                @endif
                            </span>
                        @empty
                            <div class="text-muted small fst-italic mb-2">No drivers assigned yet</div>
                        @endforelse
                    </div>

                    @if(in_array($shift->status, ['draft','confirmed']))
                    <form action="{{ route('admin.driver.shifts.addDriver', $shift->id) }}" method="POST"
                          class="d-flex gap-2 align-items-center mt-2 flex-wrap">
                        @csrf
                        <select name="driver_id" class="form-select form-select-sm" style="flex:1;min-width:150px" required>
                            <option value="">+ Add Driver</option>
                            @foreach($allDrivers as $d)
                                @if(!$shift->drivers->contains('id', $d->id))
                                <option value="{{ $d->id }}">
                                    {{ $d->user?->first_name }} {{ $d->user?->last_name }}
                                    ({{ $d->vehicleType?->name ?? 'No vehicle' }})
                                </option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-outline-primary">Add</button>
                    </form>
                    @endif

                    {{-- Actions --}}
                    <div class="d-grid gap-2 mt-3">
                        @if($shift->status === 'draft')
                            <form action="{{ route('admin.driver.shifts.confirm', $shift->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success w-100 fw-semibold">✓ Confirm Shift</button>
                            </form>
                            <form action="{{ route('admin.driver.shifts.destroy', $shift->id) }}" method="POST"
                                  onsubmit="return confirm('Delete shift? All rides will be unassigned.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger w-100">✕ Delete Shift</button>
                            </form>
                        @endif
                        <a href="{{ route('admin.driver.shifts.index', ['date' => $shift->date->toDateString()]) }}"
                           class="btn btn-outline-secondary">← All Shifts</a>
                    </div>
                </div>
            </div>

            {{-- Assigned rides list --}}
            @if($shift->shiftRides->isNotEmpty())
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light fw-semibold small">
                    📋 Assigned Rides ({{ $shift->shiftRides->count() }})
                </div>
                <div class="card-body p-2">
                    @foreach($shift->shiftRides->sortBy('seat_number') as $sr)
                    <div class="assigned-item px-2">
                        <div>
                            <span class="badge bg-secondary me-1" style="font-size:.68rem">#{{ $sr->seat_number }}</span>
                            <strong>{{ $sr->ride->rideAssign?->subscription?->kid?->first_name ?? '—' }}</strong>
                            <span class="text-muted" style="font-size:.73rem">
                                · {{ $sr->ride->pickupLocation?->city }}→{{ $sr->ride->dropoffLocation?->city }}
                                · {{ \Carbon\Carbon::parse($sr->ride->pickup)->format('H:i') }}
                            </span>
                            @if($sr->ride->driver)
                            <span class="badge bg-light text-dark border ms-1" style="font-size:.65rem">
                                🚗 {{ $sr->ride->driver->user?->first_name }}
                            </span>
                            @endif
                        </div>
                        @if(in_array($shift->status, ['draft','confirmed']))
                        <form action="{{ route('admin.driver.shifts.removeRide', [$shift->id, $sr->ride_id]) }}"
                              method="POST" onsubmit="return confirm('Remove?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger py-0 px-1" style="font-size:10px">✕</button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
        </div>

        {{-- ══════════ RIGHT — Available Rides ══════════ --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">➕ Assign Rides</span>
                        <span class="badge bg-info text-dark ms-2">⏰ {{ $shift->start_time }} – {{ $shift->end_time }}</span>
                    </div>
                    <span class="badge bg-{{ $availableRides->flatten()->count() > 0 ? 'success' : 'secondary' }} fs-6">
                        {{ $availableRides->flatten()->count() }} rides in window
                    </span>
                </div>

                <div class="card-body">

                    @if($availableRides->isEmpty())
                        <div class="empty-state">
                            <div style="font-size:2.5rem">🕐</div>
                            <div class="fw-semibold mt-2">No unassigned rides in this shift window</div>
                            <div class="small mt-1 text-muted">
                                Looking for pickup between <strong>{{ $shift->start_time }}</strong>
                                and <strong>{{ $shift->end_time }}</strong>
                                on <strong>{{ \Carbon\Carbon::parse($shift->date)->format('d M Y') }}</strong>
                            </div>
                        </div>

                    @else

                        {{-- Driver selector --}}
                        <div class="d-flex align-items-center gap-3 mb-3 p-3 bg-light rounded">
                            <label class="fw-semibold small text-nowrap mb-0">🚗 Assign to Driver:</label>
                            <select id="assignDriverSelect" class="form-select form-select-sm">
                                <option value="">— Select driver —</option>
                                @foreach($shift->drivers as $d)
                                    <option value="{{ $d->id }}">
                                        {{ $d->user?->first_name }} {{ $d->user?->last_name }}
                                        ({{ $d->vehicleType?->name ?? 'No vehicle' }},
                                        {{ $d->vehicleType?->max_capacity ?? '?' }} seats)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if($shift->drivers->isEmpty())
                            <div class="alert alert-warning small py-2 mb-3">
                                ⚠️ Add a driver to this shift (left panel) before assigning rides.
                            </div>
                        @endif

                        <div class="alert alert-primary py-2 small mb-3">
                            💡 Select driver above, then <strong>click a ride</strong> to assign it instantly,
                            or <strong>tick multiple rides</strong> and use Assign Selected.
                        </div>

                        {{-- Bulk bar --}}
                        <div id="bulkBar" class="d-none justify-content-between align-items-center p-2 mb-3 bg-light rounded border">
                            <span id="bulkCount" class="fw-semibold small">0 selected</span>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">Clear</button>
                                <button class="btn btn-sm btn-primary" id="btnBulkAssign" onclick="bulkAssign()">
                                    ✔ Assign Selected
                                </button>
                            </div>
                        </div>

                        {{-- Rides grouped by route --}}
                        @foreach($availableRides as $routeKey => $rides)
                        @php
                            $first  = $rides->first();
                            $pickup = $first->pickupLocation;
                            $drop   = $first->dropoffLocation;
                        @endphp

                        <div class="route-header">
                            📍 {{ $pickup?->city ?? $pickup?->address ?? '?' }}
                            → {{ $drop?->city ?? $drop?->address ?? '?' }}
                            <span class="badge bg-secondary ms-2" style="font-size:.7rem">{{ $rides->count() }}</span>
                        </div>

                        @foreach($rides->sortBy('pickup') as $ride)
                        @php
                            $kid       = $ride->rideAssign?->subscription?->kid;
                            $parent    = $ride->rideAssign?->subscription?->user;
                            $isMorning = in_array($ride->ride_type, ['pickup','morning']);
                        @endphp
                        <div class="ride-card" id="rideCard_{{ $ride->id }}"
                             onclick="handleRideClick({{ $ride->id }}, event)">
                            <div class="d-flex align-items-start gap-3">
                                <input type="checkbox" class="form-check-input ride-cb mt-1"
                                       id="cb_{{ $ride->id }}" value="{{ $ride->id }}"
                                       onclick="event.stopPropagation();toggleCheck({{ $ride->id }})">
                                <div class="text-center" style="min-width:46px">
                                    <div style="font-size:1.3rem">{{ $isMorning ? '🌅' : '🌆' }}</div>
                                    <span class="time-badge {{ $isMorning ? 'bg-primary text-white' : 'bg-warning text-dark' }}">
                                        {{ \Carbon\Carbon::parse($ride->pickup)->format('H:i') }}
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <strong>{{ $kid?->first_name }} {{ $kid?->last_name }}</strong>
                                            <span class="text-muted small ms-1">({{ $parent?->first_name }})</span>
                                        </div>
                                        <span class="badge bg-{{ $isMorning ? 'primary' : 'warning text-dark' }}">
                                            {{ $isMorning ? 'Morning' : 'Afternoon' }}
                                        </span>
                                    </div>
                                    <div class="text-muted small mt-1">
                                        ⏰ {{ \Carbon\Carbon::parse($ride->pickup)->format('H:i') }}
                                        → {{ \Carbon\Carbon::parse($ride->drop_off)->format('H:i') }}
                                        · Ride #{{ $ride->id }}
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

@endsection

@push('scripts')
<script>
const shiftId   = {{ $shift->id }};
const csrfToken = '{{ csrf_token() }}';
let selectedRides = [];

function getDriverId() {
    return document.getElementById('assignDriverSelect').value;
}

function handleRideClick(rideId, e) {
    if (e.target.type === 'checkbox') return;
    const driverId = getDriverId();
    if (!driverId) {
        showToast('⚠️ Select a driver first.', 'warning');
        document.getElementById('assignDriverSelect').focus();
        return;
    }
    assignSingle(rideId, driverId);
}

function toggleCheck(rideId) {
    const cb   = document.getElementById('cb_' + rideId);
    const card = document.getElementById('rideCard_' + rideId);
    if (cb.checked) { selectedRides.push(rideId); card.classList.add('selected'); }
    else { selectedRides = selectedRides.filter(id => id !== rideId); card.classList.remove('selected'); }
    updateBulkBar();
}

function updateBulkBar() {
    const bar = document.getElementById('bulkBar');
    document.getElementById('bulkCount').textContent = selectedRides.length + ' selected';
    bar.classList.toggle('d-none', selectedRides.length === 0);
    bar.classList.toggle('d-flex', selectedRides.length > 0);
}

function clearSelection() {
    selectedRides = [];
    document.querySelectorAll('.ride-cb').forEach(cb => {
        cb.checked = false;
        document.getElementById('rideCard_' + cb.value)?.classList.remove('selected');
    });
    updateBulkBar();
}

function assignSingle(rideId, driverId) {
    const card = document.getElementById('rideCard_' + rideId);
    card.style.opacity = '.4';
    card.style.pointerEvents = 'none';

    fetch(`/admin/driver-shifts/${shiftId}/assign-ride`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ ride_id: rideId, driver_id: driverId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { showToast('✅ Ride assigned!', 'success'); setTimeout(() => location.reload(), 700); }
        else { showToast('❌ ' + (data.message ?? 'Failed.'), 'danger'); card.style.opacity = ''; card.style.pointerEvents = ''; }
    })
    .catch(() => { showToast('❌ Network error.', 'danger'); card.style.opacity = ''; card.style.pointerEvents = ''; });
}

async function bulkAssign() {
    const driverId = getDriverId();
    if (!driverId) { showToast('⚠️ Select a driver first.', 'warning'); return; }
    const btn = document.getElementById('btnBulkAssign');
    btn.disabled = true; btn.textContent = '⏳ Assigning…';

    for (const rideId of [...selectedRides]) {
        await fetch(`/admin/driver-shifts/${shiftId}/assign-ride`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ ride_id: rideId, driver_id: driverId }),
        });
    }
    showToast('✅ All rides assigned!', 'success');
    setTimeout(() => location.reload(), 700);
}

function showToast(msg, type = 'success') {
    const t = document.createElement('div');
    t.className = `alert alert-${type} shadow position-fixed`;
    t.style.cssText = 'top:20px;right:20px;z-index:9999;min-width:280px;font-weight:600';
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
</script>
@endpush
