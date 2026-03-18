{{-- resources/views/backend/timesheets/partials/detail.blade.php --}}
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="p-3 bg-light rounded-3">
            <div class="text-muted small mb-1">Driver</div>
            {{-- DB::table() returns stdClass — use direct properties --}}
            <div class="fw-bold">{{ $timesheet->first_name }} {{ $timesheet->last_name }}</div>
            <div class="text-muted small">{{ $timesheet->phone }}</div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="p-3 bg-light rounded-3">
            <div class="text-muted small mb-1">Date & Hours</div>
            <div class="fw-bold">{{ \Carbon\Carbon::parse($timesheet->date)->format('l, d M Y') }}</div>
            <div class="text-primary fw-semibold">{{ number_format($timesheet->hours_worked, 2) }} hours worked</div>
            @if($timesheet->shift_start && $timesheet->shift_end)
                <div class="text-muted small">
                    {{ \Carbon\Carbon::parse($timesheet->shift_start)->format('h:i A') }}
                    – {{ \Carbon\Carbon::parse($timesheet->shift_end)->format('h:i A') }}
                </div>
            @endif
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
                    {{ \Carbon\Carbon::parse('2000-01-01 ' . $shift['start_time'])->format('h:i A') }}
                    – {{ \Carbon\Carbon::parse('2000-01-01 ' . $shift['end_time'])->format('h:i A') }}
                </span>
            </div>
            <span class="badge {{ $shift['completed_rides'] > 0 ? 'bg-success' : 'bg-secondary' }}">
                {{ $shift['completed_rides'] > 0 ? 'Active' : 'No rides' }}
            </span>
        </div>
        <div class="mt-2 d-flex gap-3 small text-muted">
            <span>🚗 {{ $shift['completed_rides'] }}/{{ $shift['total_rides'] }} rides completed</span>
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
<div class="mt-3 d-flex gap-2 align-items-center">
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
    <span class="text-muted small ms-auto">
        {{-- approved_at নেই — updated_at use করো --}}
        Reviewed · {{ \Carbon\Carbon::parse($timesheet->updated_at)->format('d M H:i') }}
    </span>
    @endif
</div>

<script>
$(document).on('click', '.btn-action-modal', function () {
    const id     = $(this).data('id');
    const action = $(this).data('action');
    $.ajax({
        url:    `/admin/timesheets/${id}/status`,
        method: 'PATCH',
        data:   { _token: '{{ csrf_token() }}', status: action },
        success: function (res) {
            if (res.success) {
                location.reload();
            } else {
                alert('Failed: ' + (res.message || 'Unknown error'));
            }
        },
        error: function (xhr) {
            alert('Error: ' + xhr.status + ' ' + xhr.responseText);
        }
    });
});
</script>
