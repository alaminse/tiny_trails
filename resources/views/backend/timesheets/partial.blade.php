{{-- resources/views/backend/timesheet/partials/detail.blade.php --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="p-3 bg-light rounded-3">
            <div class="text-muted small mb-1">Driver</div>
            <div class="fw-bold">{{ $timesheet->driver?->user?->first_name }} {{ $timesheet->driver?->user?->last_name }}</div>
            <div class="text-muted small">{{ $timesheet->driver?->user?->phone }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded-3">
            <div class="text-muted small mb-1">Date & Hours</div>
            <div class="fw-bold">{{ \Carbon\Carbon::parse($timesheet->date)->format('l, d M Y') }}</div>
            <div class="text-primary fw-semibold">{{ number_format($timesheet->hours_worked, 2) }} hours worked</div>
        </div>
    </div>
</div>

{{-- Shifts --}}
<h6 class="fw-bold mb-2">Shifts</h6>
@if($shifts && count($shifts) > 0)
    @foreach($shifts as $shift)
    <div class="modal-shift-row mb-2">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <span class="fw-semibold">{{ $shift['shift_label'] }}</span>
                <span class="text-muted small ms-2">
                    {{ \Carbon\Carbon::parse($shift['start_time'])->format('h:i A') }}
                    – {{ \Carbon\Carbon::parse($shift['end_time'])->format('h:i A') }}
                </span>
            </div>
            <span class="badge {{ $shift['status'] === 'completed' ? 'bg-success' : 'bg-secondary' }}">
                {{ ucfirst($shift['status']) }}
            </span>
        </div>
        <div class="mt-2 d-flex gap-3 small text-muted">
            <span>🚗 {{ $shift['completed_rides'] }}/{{ $shift['total_rides'] }} rides</span>
        </div>
    </div>
    @endforeach
@else
    <p class="text-muted">No shifts recorded.</p>
@endif

{{-- Notes --}}
@if($timesheet->notes)
<div class="mt-3">
    <h6 class="fw-bold mb-1">Notes</h6>
    <p class="text-muted small">{{ $timesheet->notes }}</p>
</div>
@endif

{{-- Approval --}}
<div class="mt-3 d-flex gap-2">
    @if($timesheet->status !== 'approved')
    <button class="btn btn-success btn-sm btn-action-modal"
            data-id="{{ $timesheet->id }}" data-action="approved">
        ✅ Approve
    </button>
    @endif
    @if($timesheet->status !== 'rejected')
    <button class="btn btn-danger btn-sm btn-action-modal"
            data-id="{{ $timesheet->id }}" data-action="rejected">
        ❌ Reject
    </button>
    @endif
    @if($timesheet->approved_by)
    <span class="text-muted small ms-auto align-self-center">
        Reviewed by Admin · {{ \Carbon\Carbon::parse($timesheet->approved_at)->format('d M H:i') }}
    </span>
    @endif
</div>

<script>
$(document).on('click', '.btn-action-modal', function () {
    const id     = $(this).data('id');
    const action = $(this).data('action');
    $.ajax({
        url:    `/admin/timesheet/${id}/status`,
        method: 'PATCH',
        data:   { _token: '{{ csrf_token() }}', status: action },
        success: function (res) {
            if (res.success) { location.reload(); }
        }
    });
});
</script>
