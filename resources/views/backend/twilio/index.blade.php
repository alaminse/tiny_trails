@extends('backend.app')
@section('title', 'Twilio Credentials')

@section('css')
<style>
    .cred-card { border-radius: 12px; border: none; transition: transform .2s, box-shadow .2s; }
    .cred-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.1); }
    .cred-card.is-active { border-left: 4px solid #198754 !important; }
    .mode-badge-demo       { background: #cfe2ff; color: #084298; font-weight: 700; padding: 3px 10px; border-radius: 20px; font-size: .72rem; }
    .mode-badge-production { background: #f8d7da; color: #58151c; font-weight: 700; padding: 3px 10px; border-radius: 20px; font-size: .72rem; }
    .token-text { font-family: monospace; font-size: .8rem; letter-spacing: 1px; }
</style>
@endsection

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Twilio Credentials'])

<div class="app-content">
<div class="container-fluid">

    {{-- ── Alerts ── --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ── Active Banner ── --}}
    @php $active = $credentials->firstWhere('is_active', true); @endphp
    @if($active)
        <div class="alert alert-{{ $active->mode === 'production' ? 'danger' : 'info' }} d-flex align-items-center gap-3 mb-4">
            <span style="font-size:1.5rem">
                {{ $active->mode === 'production' ? '🚀' : '🧪' }}
            </span>
            <div>
                <strong>Active Credential:</strong> {{ $active->label }}
                &nbsp;|&nbsp;
                <span class="mode-badge-{{ $active->mode }}">{{ strtoupper($active->mode) }}</span>
                &nbsp;|&nbsp;
                <span class="token-text">{{ $active->from_number }}</span>
            </div>
        </div>
    @else
        <div class="alert alert-warning mb-4">
            ⚠️ <strong>No active credential.</strong> SMS will not work until you activate one.
        </div>
    @endif

    <div class="row g-4">

        {{-- ══ LEFT: Credential Cards ══ --}}
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">📋 Saved Credentials</h5>
                <span class="badge bg-secondary">{{ $credentials->count() }} total</span>
            </div>

            @forelse($credentials as $cred)
            <div class="card cred-card shadow-sm mb-3 {{ $cred->is_active ? 'is-active' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between gap-3">

                        {{-- Info --}}
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="fw-bold fs-6">{{ $cred->label }}</span>
                                <span class="mode-badge-{{ $cred->mode }}">{{ strtoupper($cred->mode) }}</span>
                                @if($cred->is_active)
                                    <span class="badge bg-success">✅ ACTIVE</span>
                                @endif
                            </div>

                            <div class="row g-1 small text-muted">
                                <div class="col-12">
                                    <strong>SID:</strong>
                                    <span class="token-text">{{ $cred->account_sid }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>Token:</strong>
                                    <span class="token-text">{{ $cred->masked_token }}</span>
                                </div>
                                <div class="col-12">
                                    <strong>From:</strong> {{ $cred->from_number }}
                                    @if($cred->messaging_service_sid)
                                        &nbsp;|&nbsp;
                                        <strong>MSID:</strong> {{ $cred->messaging_service_sid }}
                                    @endif
                                </div>
                                <div class="col-12 text-muted" style="font-size:.72rem">
                                    Added: {{ $cred->created_at->format('d M Y, h:i A') }}
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        @can('manage-twilio-credentials')
                        <div class="d-flex flex-column gap-2" style="min-width:100px">

                            {{-- Activate --}}
                            @unless($cred->is_active)
                            <form method="POST"
                                  action="{{ route('admin.twilio.activate', $cred) }}"
                                  onsubmit="return confirm('Switch active credential to \'{{ $cred->label }}\'?')">
                                @csrf
                                <button class="btn btn-sm btn-outline-success w-100">
                                    ⚡ Activate
                                </button>
                            </form>
                            @endunless

                            {{-- Edit --}}
                            <button class="btn btn-sm btn-outline-secondary w-100"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $cred->id }}">
                                ✏️ Edit
                            </button>

                            {{-- Delete --}}
                            @unless($cred->is_active)
                            <form method="POST"
                                  action="{{ route('admin.twilio.destroy', $cred) }}"
                                  onsubmit="return confirm('Delete \'{{ $cred->label }}\'?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger w-100">🗑 Delete</button>
                            </form>
                            @endunless

                        </div>
                        @endcan

                    </div>
                </div>
            </div>

            {{-- Edit Modals --}}
            @can('manage-twilio-credentials')
            <div class="modal fade" id="editModal{{ $cred->id }}" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.twilio.update', $cred) }}">
                            @csrf @method('PUT')
                            <div class="modal-header">
                                <h5 class="modal-title">✏️ Edit — {{ $cred->label }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                @include('backend.twilio._form', ['c' => $cred])
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm"
                                        data-bs-dismiss="modal">Cancel</button>
                                <button class="btn btn-primary btn-sm">💾 Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endcan

            @empty
            <div class="text-center py-5 text-muted">
                <div style="font-size:3rem">📵</div>
                <div class="fw-semibold mt-2">No credentials yet.</div>
                <div class="small">Add your first credential using the form →</div>
            </div>
            @endforelse
        </div>

        {{-- ══ RIGHT: Add + Test ══ --}}
        <div class="col-lg-4">

            {{-- Add New --}}
            @can('manage-twilio-credentials')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom fw-bold">
                    ➕ Add New Credential
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.twilio.store') }}">
                        @csrf
                        @include('backend.twilio._form', ['c' => null])
                        <button class="btn btn-primary btn-sm w-100 mt-3">
                            💾 Save Credential
                        </button>
                    </form>
                </div>
            </div>
            @endcan

            {{-- Validate --}}
            @can('manage-twilio-credentials')
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom fw-bold">
                    🔍 Validate Active Credential
                </div>
                <div class="card-body">
                    @if($active)
                        <p class="text-muted small mb-3">
                            Checks if <strong>{{ $active->label }}</strong>
                            can connect to Twilio (no SMS sent).
                        </p>
                        <form method="POST" action="{{ route('admin.twilio.validate') }}">
                            @csrf
                            <button class="btn btn-outline-info btn-sm w-100">
                                🔍 Validate Now
                            </button>
                        </form>
                    @else
                        <p class="text-muted small">Activate a credential first.</p>
                    @endif
                </div>
            </div>
            @endcan

            {{-- Test SMS --}}
            @can('manage-twilio-credentials')
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white border-bottom fw-bold">
                    🧪 Send Test SMS
                </div>
                <div class="card-body">
                    @if($active)
                        <p class="text-muted small mb-3">
                            Sends a real SMS using <strong>{{ $active->label }}</strong>.
                        </p>
                        <form method="POST" action="{{ route('admin.twilio.test') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Phone Number</label>
                                <input type="text" name="to"
                                       class="form-control form-control-sm"
                                       placeholder="+1234567890" required>
                                <div class="form-text">Include country code e.g. +1</div>
                            </div>
                            <button class="btn btn-warning btn-sm w-100">
                                📤 Send Test SMS
                            </button>
                        </form>
                    @else
                        <p class="text-muted small">Activate a credential first.</p>
                    @endif
                </div>
            </div>
            @endcan

        </div>
    </div>

</div>
</div>
@endsection
