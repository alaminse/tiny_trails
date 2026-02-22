{{-- resources/views/backend/vehicle-types/index.blade.php --}}
@extends('backend.app')
@section('title', 'Vehicle Types')

@section('css')
<style>
    .vtype-card {
        border: 2px solid #dee2e6; border-radius: 12px;
        text-align: center; padding: 24px 16px; cursor: pointer;
        transition: all 0.2s; background: #fff;
    }
    .vtype-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 4px 16px rgba(13,110,253,.12);
        transform: translateY(-3px);
    }
    .vtype-card .vicon { font-size: 40px; margin-bottom: 10px; }
    .vtype-card.add-card {
        border-style: dashed; color: #6c757d; min-height: 160px;
        display: flex; flex-direction: column;
        align-items: center; justify-content: center;
    }
    .vtype-card.add-card:hover { border-color: #198754; color: #198754; }
</style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Vehicle Types'])

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Vehicle Type Management</h3>
                            <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal" data-bs-target="#modalVehicle">
                                <i class="bi bi-plus-circle"></i> Add Vehicle Type
                            </button>
                        </div>

                        <div class="card-body">

                            @session('success')
                                <div class="alert alert-success">{{ $value }}</div>
                            @endsession

                            {{-- Vehicle Type Cards --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="mb-0 fw-bold">Vehicle Types</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        @forelse($vehicleTypes ?? [] as $vt)
                                            <div class="col-6 col-md-3">
                                                <div class="vtype-card"
                                                     data-bs-toggle="modal" data-bs-target="#modalVehicle"
                                                     data-id="{{ $vt->id }}"
                                                     data-name="{{ $vt->name }}"
                                                     data-capacity="{{ $vt->max_capacity }}"
                                                     data-desc="{{ $vt->description }}"
                                                     data-status="{{ $vt->status }}">
                                                    <div class="vicon">
                                                        @if($vt->max_capacity <= 4) 🚗
                                                        @elseif($vt->max_capacity <= 7) 🚐
                                                        @else 🚌
                                                        @endif
                                                    </div>
                                                    <div class="fw-bold mb-1">{{ $vt->name }}</div>
                                                    <div class="text-primary fw-semibold small mb-2">
                                                        Max {{ $vt->max_capacity }} kids
                                                    </div>
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <span class="badge {{ $vt->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ ucfirst($vt->status) }}
                                                        </span>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                                            {{ $vt->drivers_count ?? 0 }} drivers
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-6 col-md-3">
                                                <div class="vtype-card">
                                                    <div class="vicon">🚗</div>
                                                    <div class="fw-bold mb-1">4-Seater Sedan</div>
                                                    <div class="text-primary fw-semibold small mb-2">Max 3 kids</div>
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <span class="badge bg-success">Active</span>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">4 drivers</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="vtype-card">
                                                    <div class="vicon">🚐</div>
                                                    <div class="fw-bold mb-1">7-Seater Van</div>
                                                    <div class="text-primary fw-semibold small mb-2">Max 6 kids</div>
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <span class="badge bg-success">Active</span>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">6 drivers</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-3">
                                                <div class="vtype-card">
                                                    <div class="vicon">🚌</div>
                                                    <div class="fw-bold mb-1">12-Seater Minibus</div>
                                                    <div class="text-primary fw-semibold small mb-2">Max 11 kids</div>
                                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                                        <span class="badge bg-success">Active</span>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">2 drivers</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforelse

                                        {{-- Add new card --}}
                                        <div class="col-6 col-md-3">
                                            <div class="vtype-card add-card"
                                                 data-bs-toggle="modal" data-bs-target="#modalVehicle">
                                                <div style="font-size:30px;">＋</div>
                                                <div class="fw-semibold mt-2 small">Add New Type</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Driver → Vehicle Table --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="mb-0 fw-bold">Driver → Vehicle Assignments</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Driver</th>
                                                    <th>Vehicle Type</th>
                                                    <th>Plate</th>
                                                    <th>Max Capacity</th>
                                                    <th>Availability</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($drivers ?? [] as $driver)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $driver->user->first_name }} {{ $driver->user->last_name }}</strong>
                                                        </td>
                                                        <td>
                                                            @if($driver->vehicleType)
                                                                @if($driver->vehicleType->max_capacity <= 4) 🚗
                                                                @elseif($driver->vehicleType->max_capacity <= 7) 🚐
                                                                @else 🚌
                                                                @endif
                                                                {{ $driver->vehicleType->name }}
                                                            @else
                                                                <span class="text-muted">Not Assigned</span>
                                                            @endif
                                                        </td>
                                                        <td><code>{{ $driver->car_plate_number ?? '—' }}</code></td>
                                                        <td>
                                                            @if($driver->vehicleType)
                                                                <span class="badge bg-info text-dark">
                                                                    {{ $driver->vehicleType->max_capacity }} kids
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">—</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @php
                                                                [$cls, $label] = match($driver->availability_status) {
                                                                    'on_trip'          => ['bg-primary',           '🔵 On Trip'],
                                                                    'ready_next_batch' => ['bg-info text-dark',    '⚡ Next Batch'],
                                                                    'delayed'          => ['bg-danger',            '🔴 Delayed'],
                                                                    'available'        => ['bg-success',           '✅ Ready'],
                                                                    default            => ['bg-secondary',         '⚫ Offline'],
                                                                };
                                                            @endphp
                                                            <span class="badge {{ $cls }}">{{ $label }}</span>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-secondary"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#modalAssign"
                                                                    data-driver-id="{{ $driver->id }}"
                                                                    data-driver-name="{{ $driver->user->first_name }} {{ $driver->user->last_name }}"
                                                                    data-vtype-id="{{ $driver->vehicle_type_id }}">
                                                                {{ $driver->vehicle_type_id ? 'Change Vehicle' : 'Assign Vehicle' }}
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr><td><strong>James O.</strong></td><td>🚐 7-Seater Van</td><td><code>VIC-4821</code></td><td><span class="badge bg-info text-dark">6 kids</span></td><td><span class="badge bg-primary">🔵 On Trip</span></td><td><button class="btn btn-sm btn-outline-secondary">Change Vehicle</button></td></tr>
                                                    <tr><td><strong>Sarah K.</strong></td><td>🚗 4-Seater Sedan</td><td><code>VIC-3310</code></td><td><span class="badge bg-info text-dark">3 kids</span></td><td><span class="badge bg-success">✅ Ready</span></td><td><button class="btn btn-sm btn-outline-secondary">Change Vehicle</button></td></tr>
                                                    <tr><td><strong>Tom W.</strong></td><td><span class="text-muted">Not Assigned</span></td><td><code>VIC-5512</code></td><td><span class="badge bg-secondary">—</span></td><td><span class="badge bg-secondary">⚫ Offline</span></td><td><button class="btn btn-sm btn-primary">Assign Vehicle</button></td></tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Add/Edit Vehicle Type --}}
    <div class="modal fade" id="modalVehicle" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.vehicle.types.store') }}" method="POST" id="vehicleForm">
                    @csrf
                    <input type="hidden" name="_method" id="vMethod" value="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">🚐 Vehicle Type</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" id="vName"
                                       placeholder="e.g. 7-Seater Van" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Max Kids <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="max_capacity" id="vCapacity"
                                       placeholder="e.g. 6" min="1" max="50" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="text" class="form-control" name="description" id="vDesc"
                                       placeholder="e.g. Standard minivan, approved for school runs">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select class="form-select" name="status" id="vStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">💾 Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal: Assign Vehicle to Driver --}}
    <div class="modal fade" id="modalAssign" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.vehicle.types.assign') }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="driver_id" id="aDriverId">
                    <div class="modal-header">
                        <h5 class="modal-title">Assign Vehicle to <span id="aDriverName" class="text-primary"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Vehicle Type</label>
                            <select class="form-select" name="vehicle_type_id" id="aVtypeId" required>
                                <option value="">— Select —</option>
                                @foreach($vehicleTypes ?? [] as $vt)
                                    <option value="{{ $vt->id }}">
                                        {{ $vt->name }} (Max {{ $vt->max_capacity }} kids)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">💾 Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Edit vehicle type
        document.getElementById('modalVehicle').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (!btn?.dataset.id) return;
            document.getElementById('vMethod').value   = 'PUT';
            document.getElementById('vName').value     = btn.dataset.name;
            document.getElementById('vCapacity').value = btn.dataset.capacity;
            document.getElementById('vDesc').value     = btn.dataset.desc;
            document.getElementById('vStatus').value   = btn.dataset.status;
        });
        document.getElementById('modalVehicle').addEventListener('hidden.bs.modal', function() {
            document.getElementById('vehicleForm').reset();
            document.getElementById('vMethod').value = 'POST';
        });

        // Assign vehicle to driver
        document.getElementById('modalAssign').addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            document.getElementById('aDriverId').value          = btn.dataset.driverId;
            document.getElementById('aDriverName').textContent  = btn.dataset.driverName;
            document.getElementById('aVtypeId').value           = btn.dataset.vtypeId;
        });
    </script>
    @endpush
@endsection
