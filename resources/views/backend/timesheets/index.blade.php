{{-- resources/views/backend/timesheets/index.blade.php --}}
@extends('backend.app')
@section('title', 'Timesheets')

@section('css')@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Timesheets'])

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Timesheet Management</h3>
                        </div>

                        <div class="card-body">

                            @session('success')
                                <div class="alert alert-success">{{ $value }}</div>
                            @endsession

                            {{-- Stat Cards --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-warning text-dark border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Pending</div>
                                                    <div class="fs-2 fw-bold">{{ $pendingCount ?? 8 }}</div>
                                                    <div class="small">Awaiting approval</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-success text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Approved</div>
                                                    <div class="fs-2 fw-bold">{{ $approvedCount ?? 34 }}</div>
                                                    <div class="small">Ready for payroll</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-danger text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Rejected</div>
                                                    <div class="fs-2 fw-bold">{{ $rejectedCount ?? 2 }}</div>
                                                    <div class="small">Driver notified</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-primary text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Total Hours</div>
                                                    <div class="fs-2 fw-bold">{{ $totalHours ?? 142 }}</div>
                                                    <div class="small">This week</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Filters + Table --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-body">

                                    {{-- Filters --}}
                                    <form method="GET" class="row g-2 mb-4 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-semibold">Driver</label>
                                            <select class="form-select form-select-sm" name="driver_id">
                                                <option value="">All Drivers</option>
                                                @foreach($drivers ?? [] as $d)
                                                    <option value="{{ $d->id }}"
                                                        {{ request('driver_id') == $d->id ? 'selected' : '' }}>
                                                        {{ $d->user->first_name }} {{ $d->user->last_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">Status</label>
                                            <select class="form-select form-select-sm" name="status">
                                                <option value="">All</option>
                                                <option value="pending"  {{ request('status') == 'pending'  ? 'selected' : '' }}>Pending</option>
                                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">From</label>
                                            <input type="date" class="form-control form-control-sm" name="from" value="{{ request('from') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label small fw-semibold">To</label>
                                            <input type="date" class="form-control form-control-sm" name="to" value="{{ request('to') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button class="btn btn-primary btn-sm w-100">
                                                <i class="bi bi-funnel"></i> Filter
                                            </button>
                                        </div>
                                        <div class="col-md-1">
                                            <a href="{{ route('admin.timesheets.index') }}"
                                               class="btn btn-outline-secondary btn-sm w-100">Reset</a>
                                        </div>
                                    </form>

                                    {{-- Table --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Driver</th>
                                                    <th>Ride</th>
                                                    <th>Date</th>
                                                    <th>Shift Start</th>
                                                    <th>Shift End</th>
                                                    <th>Hours</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($timesheets ?? [] as $ts)
                                                    <tr>
                                                        <td><code>{{ $ts->id }}</code></td>
                                                        <td><strong>{{ $ts->driver->user->first_name }} {{ $ts->driver->user->last_name }}</strong></td>
                                                        <td><code>#{{ $ts->ride_id }}</code></td>
                                                        <td><code>{{ \Carbon\Carbon::parse($ts->date)->format('D d M') }}</code></td>
                                                        <td><code>{{ \Carbon\Carbon::parse($ts->shift_start)->format('g:i A') }}</code></td>
                                                        <td><code>{{ \Carbon\Carbon::parse($ts->shift_end)->format('g:i A') }}</code></td>
                                                        <td><strong>{{ $ts->hours_worked }}h</strong></td>
                                                        <td>
                                                            @if($ts->status === 'pending')
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            @elseif($ts->status === 'approved')
                                                                <span class="badge bg-success">Approved</span>
                                                            @else
                                                                <span class="badge bg-danger">Rejected</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($ts->status === 'pending')
                                                                <div class="d-flex gap-1">
                                                                    <form action="{{ route('admin.timesheets.approve', $ts->id) }}" method="POST">
                                                                        @csrf @method('PATCH')
                                                                        <button class="btn btn-sm btn-success">✓ Approve</button>
                                                                    </form>
                                                                    <button class="btn btn-sm btn-danger"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#modalReject"
                                                                            data-id="{{ $ts->id }}">
                                                                        ✕ Reject
                                                                    </button>
                                                                </div>
                                                            @else
                                                                <a href="{{ route('admin.timesheets.show', $ts->id) }}"
                                                                   class="btn btn-sm btn-outline-secondary">View</a>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    {{-- Demo rows --}}
                                                    <tr>
                                                        <td><code>1</code></td><td><strong>James O.</strong></td><td><code>#1042</code></td>
                                                        <td><code>Mon 24 Feb</code></td><td><code>7:00 AM</code></td><td><code>9:15 AM</code></td>
                                                        <td><strong>2.25h</strong></td>
                                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <button class="btn btn-sm btn-success">✓ Approve</button>
                                                                <button class="btn btn-sm btn-danger">✕ Reject</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>2</code></td><td><strong>Sarah K.</strong></td><td><code>#1043</code></td>
                                                        <td><code>Mon 24 Feb</code></td><td><code>7:30 AM</code></td><td><code>8:45 AM</code></td>
                                                        <td><strong>1.25h</strong></td>
                                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                                        <td>
                                                            <div class="d-flex gap-1">
                                                                <button class="btn btn-sm btn-success">✓ Approve</button>
                                                                <button class="btn btn-sm btn-danger">✕ Reject</button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><code>3</code></td><td><strong>Priya M.</strong></td><td><code>#1044</code></td>
                                                        <td><code>Mon 24 Feb</code></td><td><code>8:00 AM</code></td><td><code>9:30 AM</code></td>
                                                        <td><strong>1.50h</strong></td>
                                                        <td><span class="badge bg-success">Approved</span></td>
                                                        <td><button class="btn btn-sm btn-outline-secondary">View</button></td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Pagination --}}
                                    @if(isset($timesheets) && $timesheets->hasPages())
                                        <div class="mt-3">{{ $timesheets->withQueryString()->links() }}</div>
                                    @endif

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal: Reject --}}
    <div class="modal fade" id="modalReject" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="POST" id="rejectForm">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">✕ Reject Timesheet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Reason for Rejection</label>
                            <textarea class="form-control" name="notes" rows="3"
                                      placeholder="Explain reason — driver will be notified..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">✕ Confirm Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('modalReject').addEventListener('show.bs.modal', function(e) {
            const id = e.relatedTarget.dataset.id;
            document.getElementById('rejectForm').action = `/admin/timesheets/${id}/reject`;
        });
    </script>
    @endpush
@endsection
