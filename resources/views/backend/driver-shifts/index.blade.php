@extends('backend.app')
@section('title', 'Driver Shifts')

@section('css')
<style>
    .shift-card { border-radius:12px; overflow:hidden; }
    .shift-1 .card-header { background:linear-gradient(135deg,#fff8e1,#ffecb3); color:#78350f; }
    .shift-2 .card-header { background:linear-gradient(135deg,#fce4ec,#f8bbd9); color:#880e4f; }
    .shift-3 .card-header { background:linear-gradient(135deg,#e8eaf6,#c5cae9); color:#1a237e; }

    .driver-chip {
        display:inline-flex; align-items:center; gap:5px;
        background:#f1f3f5; border:1px solid #dee2e6; border-radius:20px;
        padding:3px 9px; font-size:.76rem; font-weight:600; margin:2px;
    }
    .ride-row { font-size:.81rem; padding:5px 0; border-bottom:1px solid #f0f0f0; }
    .ride-row:last-child { border:none; }
    .add-driver-form { display:none; gap:8px; align-items:center; flex-wrap:wrap; margin-top:8px; }
    .add-driver-form.open { display:flex; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Driver Shifts'])

<div class="app-content">
<div class="container-fluid">

    {{-- Top bar --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <form method="GET" class="d-flex align-items-center gap-2">
            <label class="fw-semibold mb-0 text-muted small">Date:</label>
            <input type="date" name="date" class="form-control form-control-sm"
                   value="{{ $date }}" onchange="this.form.submit()">
            <span class="badge bg-primary fs-6">
                {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
            </span>
        </form>
        <a href="{{ route('admin.driver.shifts.create', ['date' => $date]) }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            {{ $shifts->isNotEmpty() ? 'Recreate Shifts' : 'Create Shifts' }}
        </a>
    </div>

    @session('success')
        <div class="alert alert-success alert-dismissible fade show">
            {{ $value }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endsession

    @if($unshiftedCount > 0)
        <div class="alert alert-warning">
            ⚠️ <strong>{{ $unshiftedCount }} rides</strong> are unassigned on this date.
        </div>
    @endif

    @if($shifts->isEmpty())
        <div class="text-center py-5">
            <div style="font-size:60px">📅</div>
            <h5 class="text-muted mt-3">No shifts for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h5>
            <a href="{{ route('admin.driver.shifts.create', ['date' => $date]) }}"
               class="btn btn-primary mt-3">Create 3 Shifts</a>
        </div>
    @else
        <div class="row g-4">
            @foreach($shifts->sortBy('shift_number') as $shift)
            @php $def = \App\Models\DriverShift::SHIFTS[$shift->shift_number]; @endphp

            <div class="col-lg-4 col-md-6">
                <div class="card shift-card shift-{{ $shift->shift_number }} h-100 shadow-sm border-0">

                    <div class="card-header d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="fw-bold">
                            {{ $def['icon'] }} {{ $shift->shift_label }}
                            <span class="fw-normal small ms-1">{{ $shift->start_time }}–{{ $shift->end_time }}</span>
                        </span>
                        <div class="d-flex gap-1 align-items-center">
                            <span class="badge bg-{{ $shift->status === 'confirmed' ? 'success' : ($shift->status === 'active' ? 'info' : 'secondary') }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-3">

                        {{-- Drivers --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small text-muted">🚗 Drivers</span>
                                <button type="button" class="btn btn-outline-primary py-0 px-2"
                                        style="font-size:.72rem"
                                        onclick="toggleAddDriver({{ $shift->id }})">+ Add</button>
                            </div>
                            @forelse($shift->drivers as $driver)
                                <span class="driver-chip">
                                    {{ $driver->user?->first_name }} {{ $driver->user?->last_name }}
                                    <span class="text-muted" style="font-size:.68rem">
                                        · {{ $driver->vehicleType?->name ?? '—' }}
                                    </span>
                                    @if($shift->status === 'draft')
                                    <form action="{{ route('admin.driver.shifts.removeDriver', [$shift->id, $driver->id]) }}"
                                          method="POST" style="display:inline" onsubmit="return confirm('Remove driver?')">
                                        @csrf @method('DELETE')
                                        <button style="cursor:pointer;color:#dc3545;background:none;border:none;padding:0;font-size:.82rem">✕</button>
                                    </form>
                                    @endif
                                </span>
                            @empty
                                <span class="text-muted small fst-italic">No drivers yet</span>
                            @endforelse

                            {{-- Inline add-driver --}}
                            <form action="{{ route('admin.driver.shifts.addDriver', $shift->id) }}"
                                  method="POST" class="add-driver-form" id="addForm_{{ $shift->id }}">
                                @csrf
                                <select name="driver_id" class="form-select form-select-sm" style="flex:1;min-width:160px" required>
                                    <option value="">— Driver —</option>
                                    @foreach($drivers as $d)
                                        @if(!$shift->drivers->contains('id', $d->id))
                                        <option value="{{ $d->id }}">
                                            {{ $d->user?->first_name }} {{ $d->user?->last_name }}
                                            ({{ $d->vehicleType?->name ?? 'No vehicle' }})
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Add</button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                        onclick="toggleAddDriver({{ $shift->id }})">✕</button>
                            </form>
                        </div>

                        {{-- Stats --}}
                        <div class="d-flex gap-2 mb-3 flex-wrap">
                            <span class="badge bg-primary">{{ $shift->booked_seats }} rides</span>
                            <span class="badge bg-warning text-dark">{{ $shift->instant_seats }} instant</span>
                            <span class="badge bg-light text-dark border">{{ $shift->shiftRides->count() }} assigned</span>
                        </div>

                        {{-- Rides --}}
                        @forelse($shift->shiftRides->sortBy('seat_number') as $sr)
                            <div class="ride-row d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-secondary me-1" style="font-size:.68rem">#{{ $sr->seat_number }}</span>
                                    <strong>{{ $sr->ride->rideAssign?->subscription?->kid?->first_name ?? '—' }}</strong>
                                    <span class="text-muted" style="font-size:.73rem">
                                        · {{ $sr->ride->pickupLocation?->city }} → {{ $sr->ride->dropoffLocation?->city }}
                                        · {{ \Carbon\Carbon::parse($sr->ride->pickup)->format('H:i') }}
                                    </span>
                                </div>
                                @if(in_array($shift->status, ['draft','confirmed']))
                                <form action="{{ route('admin.driver.shifts.removeRide', [$shift->id, $sr->ride_id]) }}"
                                      method="POST" onsubmit="return confirm('Remove?')">
                                    @csrf @method('DELETE')
                                    <button class="btn py-0 px-1 btn-outline-danger" style="font-size:10px">✕</button>
                                </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-muted small text-center py-2 mb-0">No rides assigned</p>
                        @endforelse

                    </div>

                    <div class="card-footer d-flex gap-2 p-2 bg-transparent">
                        <a href="{{ route('admin.driver.shifts.show', $shift->id) }}"
                           class="btn btn-sm btn-outline-secondary flex-fill">Assign Rides</a>
                        @if($shift->status === 'draft')
                            <form action="{{ route('admin.driver.shifts.confirm', $shift->id) }}"
                                  method="POST" class="flex-fill">
                                @csrf
                                <button class="btn btn-sm btn-success w-100">✓ Confirm</button>
                            </form>
                        @endif
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    @endif

</div>
</div>
@endsection

@push('scripts')
<script>
function toggleAddDriver(id) {
    document.getElementById('addForm_' + id).classList.toggle('open');
}
</script>
@endpush
