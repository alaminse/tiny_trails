{{-- resources/views/backend/driver-wages/index.blade.php --}}
@extends('backend.app')
@section('title', 'Driver Wages')

@section('css')@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Driver Wages'])

    @can('list-driver-wages')
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header d-flex align-items-center">
                                <h3 class="card-title mb-0">Driver Wage Management</h3>
                                @can('create-driver-wages')
                                    <div class="ms-auto">
                                        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalWage">
                                            <i class="bi bi-plus-circle"></i> Set Wage
                                        </button>
                                    </div>
                                @endcan
                            </div>

                            <div class="card-body">

                                @session('success')
                                    <div class="alert alert-success">{{ $value }}</div>
                                @endsession

                                {{-- Info Alert --}}
                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body">
                                        <div class="alert alert-info d-flex align-items-center gap-2 mb-0">
                                            <i class="bi bi-lock-fill fs-5"></i>
                                            <span>
                                                Driver wages are managed exclusively by <strong>Super Admins</strong>.
                                                Drivers do <strong>not</strong> see their wage rates.
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Wage Table --}}
                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Driver</th>
                                                        <th>Rate Type</th>
                                                        <th>Rate Amount</th>
                                                        <th>Effective From</th>
                                                        <th>Effective To</th>
                                                        <th>Status</th>
                                                        <th>Created By</th>
                                                        @can('edit-driver-wages')
                                                            <th>Action</th>
                                                        @endcan
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($wages ?? [] as $wage)
                                                        <tr>
                                                            <td>
                                                                <strong>{{ $wage->driver->user->first_name }}
                                                                    {{ $wage->driver->user->last_name }}</strong>
                                                            </td>
                                                            <td>
                                                                @if ($wage->rate_type === 'daily')
                                                                    <span class="badge bg-primary">Daily</span>
                                                                @else
                                                                    <span class="badge bg-info text-dark">Hourly</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="fs-5 fw-bold text-success">${{ number_format($wage->rate_amount, 2) }}</span>
                                                                <small class="text-muted">/
                                                                    {{ $wage->rate_type === 'daily' ? 'day' : 'hr' }}</small>
                                                            </td>
                                                            <td><code>{{ \Carbon\Carbon::parse($wage->effective_from)->format('d M Y') }}</code>
                                                            </td>
                                                            <td>
                                                                @if ($wage->effective_to)
                                                                    <code>{{ \Carbon\Carbon::parse($wage->effective_to)->format('d M Y') }}</code>
                                                                @else
                                                                    <span class="text-muted">Ongoing</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge {{ $wage->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                                    {{ ucfirst($wage->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="small text-muted">
                                                                {{ $wage->createdBy->first_name ?? '—' }}</td>
                                                            <td>
                                                                @can('edit-driver-wages')
                                                                    <button class="btn btn-sm btn-outline-secondary"
                                                                        data-bs-toggle="modal" data-bs-target="#modalWage"
                                                                        data-id="{{ $wage->id }}"
                                                                        data-driver="{{ $wage->driver_id }}"
                                                                        data-rate-type="{{ $wage->rate_type }}"
                                                                        data-rate-amount="{{ $wage->rate_amount }}"
                                                                        data-from="{{ $wage->effective_from }}"
                                                                        data-to="{{ $wage->effective_to }}"
                                                                        data-status="{{ $wage->status }}"
                                                                        data-notes="{{ $wage->notes }}">
                                                                        ✏️ Edit
                                                                    </button>
                                                                @endcan
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <div class="text-center py-5">
                                                            <div class="mb-3" style="font-size:40px;">😕</div>
                                                            <h6 class="fw-bold">No Driver Wages Found</h6>
                                                            <p class="text-muted mb-0">No active Driver Wages available.</p>
                                                        </div>
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
    @endcan

    {{-- Modal: Set Wage --}}
    @canany(['create-driver-wages', 'edit-driver-wages'])
        <div class="modal fade" id="modalWage" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.driver.wages.store') }}" method="POST" id="wageForm">
                        @csrf
                        <input type="hidden" name="_method" id="wageMethod" value="POST">
                        <input type="hidden" name="wage_id" id="wageId">
                        <div class="modal-header">
                            <h5 class="modal-title">💰 Set Driver Wage</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Driver <span class="text-danger">*</span></label>
                                    <select class="form-select" name="driver_id" id="wDriver" required>
                                        <option value="">— Select Driver —</option>
                                        @foreach ($drivers ?? [] as $d)
                                            <option value="{{ $d->id }}">
                                                {{ $d->user->first_name }} {{ $d->user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Rate Type <span class="text-danger">*</span></label>
                                    <select class="form-select" name="rate_type" id="wRateType" required
                                        onchange="updateRateLabel()">
                                        <option value="daily">Daily</option>
                                        <option value="hourly">Hourly</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" id="rateLabel">
                                        Rate Amount (AUD / day) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" class="form-control" name="rate_amount" id="wAmount"
                                        placeholder="e.g. 180.00" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Status</label>
                                    <select class="form-select" name="status" id="wStatus">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Effective From <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="effective_from" id="wFrom"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Effective To <small class="text-muted fw-normal">(blank = ongoing)</small>
                                    </label>
                                    <input type="date" class="form-control" name="effective_to" id="wTo">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Notes <small class="text-muted fw-normal">(internal only)</small>
                                    </label>
                                    <textarea class="form-control" name="notes" id="wNotes" rows="2" placeholder="Not visible to driver..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">💾 Save Wage</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcanany

    @push('scripts')
        <script>
            function updateRateLabel() {
                const type = document.getElementById('wRateType').value;
                document.getElementById('rateLabel').innerHTML =
                    `Rate Amount (AUD / ${type === 'daily' ? 'day' : 'hr'}) <span class="text-danger">*</span>`;
            }

            document.getElementById('modalWage').addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                if (!btn || !btn.dataset.id) return;
                document.getElementById('wageMethod').value = 'PUT';
                document.getElementById('wageId').value = btn.dataset.id;
                document.getElementById('wDriver').value = btn.dataset.driver;
                document.getElementById('wRateType').value = btn.dataset.rateType;
                document.getElementById('wAmount').value = btn.dataset.rateAmount;
                document.getElementById('wFrom').value = btn.dataset.from;
                document.getElementById('wTo').value = btn.dataset.to;
                document.getElementById('wStatus').value = btn.dataset.status;
                document.getElementById('wNotes').value = btn.dataset.notes;
                updateRateLabel();
            });

            document.getElementById('modalWage').addEventListener('hidden.bs.modal', function() {
                document.getElementById('wageForm').reset();
                document.getElementById('wageMethod').value = 'POST';
                document.getElementById('wageId').value = '';
            });
        </script>
    @endpush
@endsection
