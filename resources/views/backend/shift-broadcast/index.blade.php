{{-- resources/views/backend/shift-broadcast/index.blade.php --}}
@extends('backend.app')
@section('title', 'Shift Broadcasts')

@section('css')
    <style>
        .broadcast-card {
            border-left: 4px solid #ffc107;
            transition: box-shadow 0.2s;
        }

        .broadcast-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, .1) !important;
        }

        .broadcast-card.filled {
            border-left-color: #198754;
        }

        .broadcast-card.expired {
            border-left-color: #dc3545;
            opacity: .75;
        }

        .timer {
            font-family: monospace;
            font-weight: 700;
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Shift Broadcasts'])
    @can('list-shift-broadcast')
        <div class="app-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card card-primary card-outline mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h3 class="card-title">Shift Broadcasts</h3>
                                @can('create-shift-broadcast')
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalBroadcast">
                                        <i class="bi bi-megaphone"></i> New Broadcast
                                    </button>
                                @endcan
                            </div>

                            <div class="card-body">

                                @session('success')
                                    <div class="alert alert-success">{{ $value }}</div>
                                @endsession
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $e)
                                                <li>{{ $e }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Tabs --}}
                                <div class="card shadow-sm border-0 rounded-3 mb-4">
                                    <div class="card-body">

                                        <ul class="nav nav-tabs mb-4" id="broadcastTabs">
                                            <li class="nav-item">
                                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOpen">
                                                    Open
                                                    <span class="badge bg-warning text-dark ms-1">{{ $openCount ?? 5 }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabFilled">
                                                    Filled
                                                    <span class="badge bg-success ms-1">{{ $filledCount ?? 12 }}</span>
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabExpired">
                                                    Expired
                                                    <span class="badge bg-danger ms-1">{{ $expiredCount ?? 3 }}</span>
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content">

                                            {{-- OPEN --}}
                                            <div class="tab-pane fade show active" id="tabOpen">
                                                @forelse($openBroadcasts ?? [] as $b)
                                                    <div class="card broadcast-card shadow-sm mb-3">
                                                        <div
                                                            class="card-body d-flex justify-content-between align-items-center flex-wrap gap-3">
                                                            <div class="flex-grow-1">
                                                                <div class="fw-bold mb-1">
                                                                    📍 {{ $b->ride->pickupLocation->address }}
                                                                    → {{ $b->ride->dropoffLocation->address }}
                                                                </div>
                                                                <div class="d-flex flex-wrap gap-3 text-muted small">
                                                                    <span><i class="bi bi-calendar3"></i>
                                                                        {{ \Carbon\Carbon::parse($b->ride->date)->format('D d M') }}</span>
                                                                    <span><i class="bi bi-clock"></i>
                                                                        {{ \Carbon\Carbon::parse($b->ride->pickup)->format('g:i A') }}</span>
                                                                    <span><i class="bi bi-geo-alt"></i>
                                                                        {{ $b->broadcast_area }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="text-center">
                                                                <div class="timer text-warning"
                                                                    data-expires="{{ $b->expires_at }}">
                                                                    ⏱ calculating…
                                                                </div>
                                                                <div class="small text-muted">remaining</div>
                                                            </div>
                                                            <div class="d-flex flex-column align-items-end gap-2">
                                                                <span class="badge bg-warning text-dark">🟡 Open</span>
                                                                @can('cancel-shift-broadcast')
                                                                    <form
                                                                        action="{{ route('admin.shift.broadcast.cancel', $b->id) }}"
                                                                        method="POST">
                                                                        @csrf @method('PATCH')
                                                                        <button class="btn btn-sm btn-outline-danger"
                                                                            onclick="return confirm('Cancel this broadcast?')">
                                                                            Cancel
                                                                        </button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
                                                <div class="text-center py-5">
                                                    <div class="mb-3" style="font-size:40px;">😕</div>
                                                    <h6 class="fw-bold">No Shift Broadcast Found</h6>
                                                    <p class="text-muted mb-0">No active Shift Broadcast available.</p>
                                                </div>
                                                @endforelse
                                            </div>

                                            {{-- FILLED --}}
                                            <div class="tab-pane fade" id="tabFilled">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Route</th>
                                                            <th>Date</th>
                                                            <th>Driver</th>
                                                            <th>Accepted At</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($filledBroadcasts ?? [] as $b)
                                                            <tr>
                                                                <td>{{ $b->ride->pickupLocation->address }} →
                                                                    {{ $b->ride->dropoffLocation->address }}</td>
                                                                <td><code>{{ \Carbon\Carbon::parse($b->ride->date)->format('D d M') }}</code>
                                                                </td>
                                                                <td>{{ $b->acceptance->driver->user->first_name ?? '—' }}</td>
                                                                <td><code>{{ \Carbon\Carbon::parse($b->acceptance->accepted_at)->format('g:i A') }}</code>
                                                                </td>
                                                                <td><span class="badge bg-success">Filled</span></td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td>Ghan NT → Sunshine Primary</td>
                                                                <td><code>Mon 24 Feb</code></td>
                                                                <td>James O.</td>
                                                                <td><code>06:45 AM</code></td>
                                                                <td><span class="badge bg-success">Filled</span></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Clayton → St. Joseph's</td>
                                                                <td><code>Mon 24 Feb</code></td>
                                                                <td>Sarah K.</td>
                                                                <td><code>06:52 AM</code></td>
                                                                <td><span class="badge bg-success">Filled</span></td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            {{-- EXPIRED --}}
                                            <div class="tab-pane fade" id="tabExpired">
                                                <div class="alert alert-danger">
                                                    ⚠️ {{ $expiredCount ?? 3 }} broadcasts expired without driver acceptance.
                                                </div>
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Route</th>
                                                            <th>Date</th>
                                                            <th>Expired At</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($expiredBroadcasts ?? [] as $b)
                                                            <tr>
                                                                <td>{{ $b->ride->pickupLocation->address }}</td>
                                                                <td><code>{{ \Carbon\Carbon::parse($b->ride->date)->format('D d M') }}</code>
                                                                </td>
                                                                <td><code>{{ \Carbon\Carbon::parse($b->expires_at)->format('g:i A') }}</code>
                                                                </td>
                                                                <td>
                                                                    @can('create-shift-broadcast')
                                                                        <form action="{{ route('admin.shift.broadcast.store') }}"
                                                                            method="POST" class="d-inline">
                                                                            @csrf
                                                                            <input type="hidden" name="ride_id"
                                                                                value="{{ $b->ride_id }}">
                                                                            <button
                                                                                class="btn btn-sm btn-primary">Re-broadcast</button>
                                                                        </form>
                                                                    @endcan
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td>Ringwood VIC → St. Luke's</td>
                                                                <td><code>Sun 23 Feb</code></td>
                                                                <td><code>07:30 AM</code></td>
                                                                <td><button class="btn btn-sm btn-primary">Re-broadcast</button>
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>{{-- /tab-content --}}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Modal: New Broadcast --}}
    @can('create-shift-broadcast')
        <div class="modal fade" id="modalBroadcast" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.shift.broadcast.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="bi bi-megaphone"></i> New Shift Broadcast</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning small mb-3">
                                ⚠️ First driver to accept gets the job. Cannot be undone without cancelling.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Select Ride <span class="text-danger">*</span></label>
                                <select class="form-select" name="ride_id" required>
                                    <option value="">— Select Ride —</option>
                                    @foreach ($unassignedRides ?? [] as $ride)
                                        <option value="{{ $ride->id }}">
                                            {{ $ride->date }} · {{ $ride->pickupLocation->address }} →
                                            {{ $ride->dropoffLocation->address }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Broadcast Area / Zone</label>
                                <input type="text" class="form-control" name="broadcast_area"
                                    placeholder="e.g. Oakleigh, VIC">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Expires In</label>
                                <select class="form-select" name="expiry_minutes">
                                    <option value="60">60 minutes</option>
                                    <option value="90">90 minutes</option>
                                    <option value="120">2 hours</option>
                                    <option value="360">6 hours</option>
                                    <option value="1440">24 hours</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-megaphone"></i> Send Broadcast
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            document.querySelectorAll('.timer[data-expires]').forEach(el => {
                function tick() {
                    const diff = new Date(el.dataset.expires) - new Date();
                    if (diff <= 0) {
                        el.textContent = '⏱ Expired';
                        return;
                    }
                    const mins = Math.floor(diff / 60000);
                    const hrs = Math.floor(mins / 60);
                    el.textContent = hrs > 0 ? `⏱ ${hrs}h ${mins % 60}m left` : `⏱ ${mins} min left`;
                    if (mins < 20) {
                        el.classList.remove('text-warning');
                        el.classList.add('text-danger');
                    }
                }
                tick();
                setInterval(tick, 60000);
            });
        </script>
    @endpush
@endsection
