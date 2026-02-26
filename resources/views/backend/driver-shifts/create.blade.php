{{-- resources/views/backend/driver-shifts/create.blade.php --}}
@extends('backend.app')
@section('title', 'Create Shifts')

@section('content')
@include('backend.includes.header', ['mainTitle' => 'Create Shifts for Date'])

<div class="app-content">
<div class="container-fluid">
<div class="row justify-content-center">
<div class="col-xl-8">

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
        </div>
    @endif

    @if($existingShifts->isNotEmpty())
        <div class="alert alert-warning d-flex gap-3 align-items-start">
            <span style="font-size:1.5rem">⚠️</span>
            <div>
                <div class="fw-bold mb-1">Shifts already exist for {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</div>
                <div class="small">
                    Submitting will <strong class="text-danger">delete all existing shifts and ride assignments</strong> for this date, then recreate fresh.
                </div>
                <div class="mt-2 d-flex gap-2 flex-wrap">
                    @foreach($existingShifts as $s)
                        <span class="badge bg-secondary">
                            {{ \App\Models\DriverShift::SHIFTS[$s->shift_number]['icon'] ?? '' }}
                            {{ $s->shift_label }} ({{ $s->booked_seats }} rides)
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.driver.shifts.store') }}" method="POST">
        @csrf

        {{-- Step 1: Date only --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-primary text-white fw-bold">① Select Date</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Date <span class="text-danger">*</span></label>
                        <input type="date" name="date" id="dateInput" class="form-control"
                               value="{{ old('date', $date) }}"
                               min="{{ today()->toDateString() }}" required>
                        <div class="form-text">
                            Shifts are <strong>global per date</strong> — drivers are assigned per shift separately.
                        </div>
                    </div>
                    <div class="col-md-7 d-flex align-items-end">
                        <div class="p-3 bg-light rounded w-100">
                            <div class="small text-muted mb-1">Creating 3 shifts for:</div>
                            <div class="fw-bold fs-5" id="selectedDateLabel">
                                {{ \Carbon\Carbon::parse($date)->format('D, d M Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Shift config --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center">
                <span>② Configure Shift Windows
                    <span class="text-muted fw-normal small ms-2">— 3 × 8-hour shifts</span>
                </span>
                <span class="badge bg-success">06:00 → 14:00 → 22:00 → 06:00</span>
            </div>
            <div class="card-body">

                @php
                $shiftDefs = [
                    1 => ['icon'=>'🌅','label'=>'Morning','start'=>'06:00','end'=>'14:00',
                          'bg'=>'#fff8e1','border'=>'#f59e0b','bar'=>'#fbbf24','text'=>'#78350f'],
                    2 => ['icon'=>'🌇','label'=>'Evening','start'=>'14:00','end'=>'22:00',
                          'bg'=>'#fce4ec','border'=>'#e91e63','bar'=>'#f06292','text'=>'#880e4f'],
                    3 => ['icon'=>'🌙','label'=>'Night',  'start'=>'22:00','end'=>'06:00',
                          'bg'=>'#e8eaf6','border'=>'#5c6bc0','bar'=>'#5c6bc0','text'=>'#1a237e'],
                ];
                @endphp

                {{-- Timeline --}}
                <div class="mb-4">
                    <div class="d-flex rounded overflow-hidden" style="height:30px;font-size:.72rem;font-weight:700;">
                        <div class="d-flex align-items-center justify-content-center"
                             style="width:33.33%;background:#fbbf24;color:#78350f;">🌅 06:00–14:00</div>
                        <div class="d-flex align-items-center justify-content-center text-white"
                             style="width:33.34%;background:#f06292;">🌇 14:00–22:00</div>
                        <div class="d-flex align-items-center justify-content-center text-white"
                             style="width:33.33%;background:#5c6bc0;">🌙 22:00–06:00</div>
                    </div>
                    <div class="d-flex justify-content-between text-muted px-1" style="font-size:.7rem;margin-top:3px;">
                        <span>06:00</span><span>14:00</span><span>22:00</span>
                        <span>06:00 <span class="text-danger">(+1 day)</span></span>
                    </div>
                </div>

                @foreach($shiftDefs as $num => $def)
                    <input type="hidden" name="shifts[{{ $num }}][shift_number]" value="{{ $num }}">
                    <input type="hidden" name="shifts[{{ $num }}][enabled]" value="1">

                    <div class="rounded mb-3 p-3"
                         style="border-left:4px solid {{ $def['border'] }};background:{{ $def['bg'] }}">
                        <div class="fw-bold mb-3" style="font-size:.95rem;color:{{ $def['text'] }}">
                            {{ $def['icon'] }} Shift {{ $num }} — {{ $def['label'] }}
                            <span class="badge ms-2"
                                  style="background:{{ $def['bar'] }};color:#fff;font-size:.72rem;">
                                {{ $def['start'] }} – {{ $def['end'] }}
                            </span>
                            @if($num===3)
                                <span class="badge bg-dark ms-1" style="font-size:.68rem;">overnight</span>
                            @endif
                        </div>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Start Time</label>
                                <input type="time" class="form-control"
                                       name="shifts[{{ $num }}][start_time]"
                                       value="{{ old("shifts.{$num}.start_time", $def['start']) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">End Time</label>
                                <input type="time" class="form-control"
                                       name="shifts[{{ $num }}][end_time]"
                                       value="{{ old("shifts.{$num}.end_time", $def['end']) }}" required>
                                @if($num===3)
                                    <div class="form-text text-danger fw-semibold">⚠ Next day 06:00</div>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">⚡ Instant Reserve</label>
                                <input type="number" class="form-control"
                                       name="shifts[{{ $num }}][instant_seats]"
                                       value="{{ old("shifts.{$num}.instant_seats", 0) }}"
                                       min="0" max="10">
                                <div class="form-text">Seats for instant pickups</div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-semibold">Notes</label>
                                <input type="text" class="form-control"
                                       name="shifts[{{ $num }}][notes]"
                                       value="{{ old("shifts.{$num}.notes") }}" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- Unshifted rides info --}}
        @if($unshiftedRides->isNotEmpty())
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold" style="background:#fff3cd">
                    📋 Unassigned Rides on {{ $date }}
                    <span class="badge bg-warning text-dark ms-1">{{ $unshiftedRides->flatten()->count() }}</span>
                    <span class="text-muted fw-normal small ms-2">— Assign from the shift detail page after creating</span>
                </div>
            </div>
        @endif

        {{-- Submit --}}
        <div class="d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.driver.shifts.index', ['date' => $date]) }}"
               class="btn btn-outline-secondary">← Back</a>
            <div class="text-center">
                <button type="submit" class="btn btn-primary px-5 fw-semibold"
                    @if($existingShifts->isNotEmpty())
                        onclick="return confirm('This will DELETE existing shifts and all ride assignments. Continue?')"
                    @endif>
                    {{ $existingShifts->isNotEmpty() ? '⚠ Delete & Recreate Shifts' : 'Create 3 Shifts →' }}
                </button>
                <div class="text-muted small mt-1">Morning (06–14) · Evening (14–22) · Night (22–06)</div>
            </div>
        </div>

    </form>
</div>
</div>
</div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('dateInput').addEventListener('change', function () {
    const d = new Date(this.value + 'T00:00:00');
    document.getElementById('selectedDateLabel').textContent =
        d.toLocaleDateString('en-AU', {weekday:'short', day:'2-digit', month:'short', year:'numeric'});
});
</script>
@endpush
