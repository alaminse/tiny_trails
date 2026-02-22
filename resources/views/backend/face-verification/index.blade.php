@extends('backend.app')
@section('title', 'Face Verification')

@section('css')
<style>
    .face-card { border-left: 4px solid #dee2e6; transition: box-shadow 0.2s; }
    .face-card:hover { box-shadow: 0 4px 14px rgba(0,0,0,.08) !important; }
    .face-card.verified { border-left-color: #198754; }
    .face-card.expiring { border-left-color: #ffc107; }
    .face-card.expired  { border-left-color: #dc3545; }
    .face-card.none     { border-left-color: #adb5bd; opacity: .75; }

    .session-bar { height: 8px; border-radius: 4px; background: #e9ecef; overflow: hidden; }
    .session-fill { height: 100%; border-radius: 4px; transition: width .4s; }

    .driver-avatar {
        width: 48px; height: 48px; border-radius: 50%;
        background: #f0f0f0; border: 2px solid #dee2e6;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; flex-shrink: 0; overflow: hidden;
    }
    .driver-avatar.verified { border-color: #198754; }
    .driver-avatar.expiring { border-color: #ffc107; }
    .driver-avatar.expired  { border-color: #dc3545; }
</style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Face Verification'])

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">Driver Face Verification Status</h3>
                        </div>

                        <div class="card-body">

                            {{-- Stat Cards --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-success text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Verified</div>
                                                    <div class="fs-2 fw-bold">{{ $verifiedCount ?? 8 }}</div>
                                                    <div class="small">Active sessions</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-danger text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Expired / None</div>
                                                    <div class="fs-2 fw-bold">{{ $expiredCount ?? 4 }}</div>
                                                    <div class="small">Cannot take shifts</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-primary text-white border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Window</div>
                                                    <div class="fs-2 fw-bold">5h</div>
                                                    <div class="small">Per shift session</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="card bg-warning text-dark border-0 shadow-sm">
                                                <div class="card-body py-3">
                                                    <div class="text-uppercase small fw-semibold mb-1">Expiring Soon</div>
                                                    <div class="fs-2 fw-bold">{{ $expiringCount ?? 2 }}</div>
                                                    <div class="small">Within 30 min</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Alert --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-body">
                                    <div class="alert alert-info d-flex align-items-start gap-2 mb-0">
                                        <i class="bi bi-info-circle-fill fs-5 mt-1"></i>
                                        <div>
                                            Face verification is performed by drivers in their app at shift start.
                                            The <strong>5-hour window</strong> covers a full morning shift.
                                            Drivers whose session expires mid-shift are <strong>not interrupted</strong> —
                                            expiry only blocks new shift acceptance.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Driver Cards --}}
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-light border-bottom">
                                    <h6 class="mb-0 fw-bold">Verification Status per Driver</h6>
                                </div>
                                <div class="card-body">

                                    @forelse($drivers ?? [] as $driver)
                                        @php
                                            $isVerified = $driver->face_verification_status === 'verified'
                                                && $driver->face_verified_until
                                                && \Carbon\Carbon::parse($driver->face_verified_until)->isFuture();

                                            $minsLeft = $driver->face_verified_until
                                                ? max(0, now()->diffInMinutes(
                                                    \Carbon\Carbon::parse($driver->face_verified_until), false
                                                  ))
                                                : 0;

                                            $total = 300; // 5h in minutes
                                            $used  = $isVerified ? max(0, $total - $minsLeft) : $total;
                                            $pct   = min(100, ($used / $total) * 100);

                                            $cardClass = match(true) {
                                                !$driver->face_verified_at          => 'none',
                                                $isVerified && $minsLeft > 30       => 'verified',
                                                $isVerified && $minsLeft <= 30      => 'expiring',
                                                default                             => 'expired',
                                            };

                                            $barColor = match($cardClass) {
                                                'verified'       => '#198754',
                                                'expiring'       => '#ffc107',
                                                'expired','none' => '#dc3545',
                                            };
                                        @endphp

                                        <div class="card face-card {{ $cardClass }} shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">

                                                    {{-- Avatar --}}
                                                    <div class="driver-avatar {{ $cardClass }}">
                                                        @if($driver->user->photo)
                                                            <img src="{{ asset('storage/'.$driver->user->photo) }}"
                                                                 class="w-100 h-100 object-fit-cover" alt="">
                                                        @else
                                                            👤
                                                        @endif
                                                    </div>

                                                    {{-- Info --}}
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">{{ $driver->user->first_name }} {{ $driver->user->last_name }}</div>
                                                        <div class="small text-muted">
                                                            @if($cardClass === 'verified')
                                                                ✅ Verified · Valid until
                                                                {{ \Carbon\Carbon::parse($driver->face_verified_until)->format('g:i A') }}
                                                                ({{ $minsLeft }}m remaining)
                                                            @elseif($cardClass === 'expiring')
                                                                ⚠️ Expiring soon · Valid until
                                                                {{ \Carbon\Carbon::parse($driver->face_verified_until)->format('g:i A') }}
                                                                ({{ $minsLeft }}m remaining)
                                                            @elseif($cardClass === 'expired')
                                                                ❌ Expired · Session ended at
                                                                {{ \Carbon\Carbon::parse($driver->face_verified_until)->format('g:i A') }}
                                                            @else
                                                                ❌ Never verified today · Cannot accept shifts
                                                            @endif
                                                        </div>
                                                    </div>

                                                    {{-- Progress bar --}}
                                                    <div style="min-width:160px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1">
                                                            <span>Session</span>
                                                            <span>{{ round($pct) }}% used</span>
                                                        </div>
                                                        <div class="session-bar">
                                                            <div class="session-fill"
                                                                 style="background:{{ $barColor }}; width:{{ $pct }}%">
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Status badge --}}
                                                    @if($cardClass === 'verified')
                                                        <span class="badge bg-success">Active</span>
                                                    @elseif($cardClass === 'expiring')
                                                        <span class="badge bg-warning text-dark">Expiring</span>
                                                    @elseif($cardClass === 'expired')
                                                        <span class="badge bg-danger">Expired</span>
                                                    @else
                                                        <span class="badge bg-secondary">None</span>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>

                                    @empty
                                        {{-- Demo cards --}}
                                        <div class="card face-card verified shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div class="driver-avatar verified">👨</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">James O.</div>
                                                        <div class="small text-muted">✅ Verified · Valid until 11:58 AM (3h 16m remaining)</div>
                                                    </div>
                                                    <div style="min-width:160px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Session</span><span>35% used</span></div>
                                                        <div class="session-bar"><div class="session-fill" style="background:#198754;width:35%"></div></div>
                                                    </div>
                                                    <span class="badge bg-success">Active</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card face-card expiring shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div class="driver-avatar expiring">👩</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Priya M.</div>
                                                        <div class="small text-muted">⚠️ Expiring soon · Valid until 09:05 AM (28m remaining)</div>
                                                    </div>
                                                    <div style="min-width:160px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Session</span><span>91% used</span></div>
                                                        <div class="session-bar"><div class="session-fill" style="background:#ffc107;width:91%"></div></div>
                                                    </div>
                                                    <span class="badge bg-warning text-dark">Expiring</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card face-card expired shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div class="driver-avatar expired">👨</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Mike R.</div>
                                                        <div class="small text-muted">❌ Expired · Session ended at 08:00 AM</div>
                                                    </div>
                                                    <div style="min-width:160px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Session</span><span>100% used</span></div>
                                                        <div class="session-bar"><div class="session-fill" style="background:#dc3545;width:100%"></div></div>
                                                    </div>
                                                    <span class="badge bg-danger">Expired</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card face-card none shadow-sm mb-3">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                                    <div class="driver-avatar">👨</div>
                                                    <div class="flex-grow-1">
                                                        <div class="fw-bold">Tom W.</div>
                                                        <div class="small text-muted">❌ Never verified today · Cannot accept shifts</div>
                                                    </div>
                                                    <div style="min-width:160px;">
                                                        <div class="d-flex justify-content-between small text-muted mb-1"><span>Session</span><span>None</span></div>
                                                        <div class="session-bar"><div class="session-fill" style="background:#adb5bd;width:0%"></div></div>
                                                    </div>
                                                    <span class="badge bg-secondary">None</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforelse

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Auto-refresh every 60s to update timers
        setTimeout(() => location.reload(), 60000);
    </script>
    @endpush
@endsection
