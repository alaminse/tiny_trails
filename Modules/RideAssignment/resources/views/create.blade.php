@extends('backend.app')
@section('title', 'Ride Assign Create')

@section('css')
    <style>
        .accordion-button:focus {
            box-shadow: none;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .calendar-nav {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }

        .calendar-title {
            font-size: 1.2em;
            font-weight: bold;
            color: #495057;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #dee2e6;
            border-radius: 4px;
            overflow: hidden;
        }

        .calendar-day-header {
            background: #6c757d;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 0.9em;
        }

        .calendar-day {
            background: white;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .calendar-day:hover:not(.disabled):not(.other-month) {
            background: #e3f2fd;
        }

        .calendar-day.disabled {
            background: #f8f9fa;
            color: #6c757d;
            cursor: not-allowed;
            opacity: 0.5;
        }

        .calendar-day.other-month {
            color: #adb5bd;
            background: #f8f9fa;
        }

        .calendar-day.weekend {
            background: #ffe6e6;
            color: #dc3545;
        }

        .calendar-day.weekend.disabled {
            background: #f1f1f1;
            color: #999;
        }

        .selected-dates-display {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 15px;
            margin-top: 15px;
        }

        .selected-date-tag {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 4px 8px;
            margin: 2px;
            border-radius: 12px;
            font-size: 0.8em;
        }

        .commission-input-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .commission-input-row label {
            min-width: 120px;
            font-weight: 600;
        }

        .commission-input-row input {
            flex: 1;
            max-width: 150px;
        }
        
        .calendar-day.selected {
    background: #007bff !important;  /* ✅ Selected always blue */
    color: white !important;
}

.calendar-day.weekend:not(.selected) {
    background: #ffe6e6;  /* ✅ Only when NOT selected */
    color: #dc3545;
}
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Ride Assign Create'])

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-primary card-outline mb-4">
                        <div class="card-header">
                            <h3 class="card-title">New Ride Assignment - {{ $subscription->name }}</h3>
                        </div>

                        <!-- Subscription Information -->
                        <div class="card-body">
                            <div class="card shadow-sm border-0 rounded-3 mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Subscription Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @session('success')
                                            <div class="alert alert-success" role="alert">
                                                {{ $value }}
                                            </div>
                                        @endsession

                                        <!-- Way 1: Display All Error Messages -->
                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row g-4">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Customer:</span>
                                                    <span>{{ $subscription->user?->first_name }}
                                                        {{ $subscription->user?->last_name }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Phone:</span>
                                                    <span>{{ $subscription->user?->phone }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Kid:</span>
                                                    <span>{{ $subscription->kid?->first_name }}
                                                        {{ $subscription->kid?->last_name }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Plan:</span>
                                                    <span>{{ $subscription->plan?->name }}
                                                        ({{ ucfirst($subscription->plan?->interval) }}ly)</span>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Right Column -->
                                        <div class="col-md-6">
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Pickup:</span>
                                                    <span>{{ $subscription->pickupLocation->address }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Dropoff:</span>
                                                    <span>{{ $subscription->dropoffLocation->address }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Plan Price:</span>
                                                    <span class="fw-bold">${{ $subscription->plan?->sell_price }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Status:</span>
                                                    <span
                                                        class="btn btn-sm btn-gradient-success">{{ ucfirst($subscription->status) }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <form action="{{ route('admin.ride.assign.store', $subscription->id) }}" method="POST" id="assignmentForm" enctype="multipart/form-data">
                                @csrf

                                <input type="hidden" name="selected_dates" id="selectedDatesInput">
                                <input type="hidden" name="working_days" id="totalWorkingDays" value="0">

                                <!-- Commission Calculation Row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card card-primary card-outline mb-4">
                                            <div class="card-body">
                                                <h5 class="mb-0"><strong>Commission Calculation</strong></h5>
                                                <hr>
                                                <div class="row mb-3">
                                                    <!-- Base Fare -->
                                                    <div class="col-md-6">
                                                        <label for="base_fare" class="form-label fw-semibold">Base Fare
                                                            (AUD):</label>
                                                    </div>

                                                    <!-- Per Km Fare -->
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control" id="base_fare"
                                                            name="base_fare" value="{{ $subscription->plan->sell_price }}"
                                                            step="0.01" min="0">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <!-- Base Fare -->
                                                    <div class="col-md-6">
                                                        <label for="base_fare" class="form-label fw-semibold">Driver
                                                            Commission (%):</label>
                                                    </div>

                                                    <!-- Per Km Fare -->
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control"
                                                            id="driver_commission_percent" name="driver_commission_percent"
                                                            value="70" step="0.1" min="0" max="100">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <!-- Base Fare -->
                                                    <div class="col-md-6">
                                                        <label for="base_fare" class="form-label fw-semibold">Platform Fee (%):</label>
                                                    </div>

                                                    <!-- Per Km Fare -->
                                                    <div class="col-md-6">
                                                        <input type="number" class="form-control" id="platform_fee"
                                                            name="platform_fee" value="30" step="0.1" min="0"
                                                            max="100">
                                                    </div>
                                                </div>


                                                <hr>

                                                <!-- Single Day -->
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="fw-semibold">Single Day Calculation:</span>
                                                        <small class="text-muted">(Base calculation per ride)</small>
                                                    </div>
                                                </div>

                                                <ul class="list-group list-group-flush mb-3">
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>• Driver Earnings (Single Day):</span>
                                                        <span id="driver_earnings_per_day"
                                                            class="fw-bold text-success">AUD 0.00</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>• Platform Commission (Single Day):</span>
                                                        <span id="platform_commission_per_day"
                                                            class="fw-bold text-danger">AUD 0.00</span>
                                                    </li>
                                                </ul>

                                                <hr>

                                                <!-- Total Calculation -->
                                                <div class="mb-2">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="fw-semibold">Total for All Working Days:</span>
                                                        <small class="text-muted">(Automatic calculation for subscription
                                                            period)</small>
                                                    </div>
                                                </div>

                                                <ul class="list-group list-group-flush mb-3">
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>• Total Driver Earnings:</span>
                                                        <span id="total_driver_earnings" class="fw-bold text-success">AUD
                                                            0.00</span>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between">
                                                        <span>• Total Platform Commission:</span>
                                                        <span id="total_platform_commission"
                                                            class="fw-bold text-danger">AUD 0.00</span>
                                                    </li>
                                                </ul>

                                                <!-- Total Revenue -->
                                                <div
                                                    class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                                    <span class="fw-bold">Total Revenue:</span>
                                                    <span id="total_revenue" class="fw-bold text-primary">AUD 0.00</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-primary card-outline mb-4">
                                            <div class="card-body">
                                                <h5 class="mb-0"><strong>Select Ride Dates</strong></h5>
                                                <hr>
                                                <p class="text-muted small">Select specific dates for ride assignments.
                                                    Weekends (Sat/Sun) are disabled.</p>
                                                <!-- Working Days -->
                                                <div
                                                    class="d-flex justify-content-between align-items-center btn btn-sm btn-gradient-success p-2 rounded mb-5">
                                                    <span class="fw-semibold">Working Days in Subscription:</span>
                                                    <span id="selected_days_count" class="text-white">0 selected / 0 total
                                                        working days</span>
                                                </div>

                                                <div class="calendar-header">
                                                    <button type="button" class="calendar-nav"
                                                        id="prevMonth">&lt;</button>
                                                    <span class="calendar-title"
                                                        id="currentMonth">{{ date('F Y') }}</span>
                                                    <button type="button" class="calendar-nav"
                                                        id="nextMonth">&gt;</button>
                                                </div>

                                                <div class="calendar-grid" id="calendarGrid">
                                                    <!-- Calendar will be generated by JavaScript -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <!-- Selected Dates Accordion -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="mb-0"><strong>Selected Dates Configuration</strong></h6>
                                        </div>

                                        <div class="accordion" id="selectedDatesAccordion">
                                            <!-- Accordion items will be generated by JavaScript -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-gradient-info">Create Ride Assignments</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
    // =============================================================================
    // GLOBAL VARIABLES & CONFIGURATION
    // =============================================================================
    let currentDate = new Date();
    let selectedDates = [];
    let subscriptionStartDate = new Date('{{ $subscription->created_at }}');
    let subscriptionEndDate = new Date('{{ $subscription->ends_at }}');
    console.log(subscriptionStartDate);

    // Month names for calendar display
    const monthNames = [
        "January", "February", "March", "April", "May", "June",
        "July", "August", "September", "October", "November", "December"
    ];

    // Day headers for calendar
    const dayHeaders = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    // =============================================================================
    // UTILITY FUNCTIONS
    // =============================================================================

    function formatDateString(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function isWeekend(date) {
        const dayOfWeek = date.getDay();
        return dayOfWeek === 0 || dayOfWeek === 6;
    }

    function safeParseFloat(value, fallback = 0) {
        const parsed = parseFloat(value);
        return isNaN(parsed) ? fallback : parsed;
    }

    // ✅ Check if date is within 30 days from purchase date (and not before purchase)
    function isWithin30Days(date) {
        const checkDate = new Date(date);
        checkDate.setHours(0, 0, 0, 0);
        
        const startDate = new Date(subscriptionStartDate);
        startDate.setHours(0, 0, 0, 0);
        
        const endDate = new Date(subscriptionStartDate);
        endDate.setDate(endDate.getDate() + 30);
        endDate.setHours(0, 0, 0, 0);
        
        // ✅ Date must be >= purchase date AND <= purchase date + 30 days
        return checkDate >= startDate && checkDate <= endDate;
    }

    // ✅ Check if date is before purchase date
    function isBeforePurchaseDate(date) {
        const checkDate = new Date(date);
        checkDate.setHours(0, 0, 0, 0);
        
        const startDate = new Date(subscriptionStartDate);
        startDate.setHours(0, 0, 0, 0);
        
        return checkDate < startDate;
    }

    // =============================================================================
    // COMMISSION CALCULATION FUNCTIONS
    // =============================================================================

    function calculateCommissions() {
        try {
            console.log('=== Starting Commission Calculation ===');
            
            // Get input values
            const baseFare = safeParseFloat($('#base_fare').val());
            const driverCommissionPercent = safeParseFloat($('#driver_commission_percent').val());
            const platformFeePercent = safeParseFloat($('#platform_fee').val());

            console.log('Input Values:', {
                baseFare,
                driverCommissionPercent,
                platformFeePercent
            });

            // ✅ Use selected dates count directly
            const totalDays = selectedDates.length;
            $('#totalWorkingDays').val(totalDays);

            console.log('Total Selected Days:', totalDays);

            // Validate
            if (baseFare <= 0) {
                console.warn('Invalid base fare');
                // Show zero values
                updateCommissionDisplay({
                    baseFare: 0,
                    driverCommissionPercent: 0,
                    platformFeePercent: 0,
                    totalDays: 0,
                    driverEarningsPerDay: 0,
                    platformCommissionPerDay: 0,
                    totalDriverEarnings: 0,
                    totalPlatformCommission: 0
                });
                return;
            }

            // Calculate per day (divide by selected days)
            let driverEarningsPerDay = 0;
            let platformCommissionPerDay = 0;
            let totalDriverEarnings = 0;
            let totalPlatformCommission = 0;

            if (totalDays > 0) {
                driverEarningsPerDay = (baseFare * (driverCommissionPercent / 100)) / totalDays;
                platformCommissionPerDay = (baseFare * (platformFeePercent / 100)) / totalDays;
                totalDriverEarnings = driverEarningsPerDay * totalDays;
                totalPlatformCommission = platformCommissionPerDay * totalDays;
            } else {
                // If no days selected, show total amounts
                totalDriverEarnings = baseFare * (driverCommissionPercent / 100);
                totalPlatformCommission = baseFare * (platformFeePercent / 100);
                driverEarningsPerDay = 0;
                platformCommissionPerDay = 0;
            }

            console.log('Calculated Values:', {
                perDay: {
                    driver: driverEarningsPerDay,
                    platform: platformCommissionPerDay
                },
                total: {
                    driver: totalDriverEarnings,
                    platform: totalPlatformCommission
                }
            });

            // Update UI
            updateCommissionDisplay({
                baseFare,
                driverCommissionPercent,
                platformFeePercent,
                totalDays,
                driverEarningsPerDay,
                platformCommissionPerDay,
                totalDriverEarnings,
                totalPlatformCommission
            });

            console.log('=== Commission Calculation Complete ===');

        } catch (error) {
            console.error('Error in calculateCommissions:', error);
        }
    }

    function updateCommissionDisplay(data) {
        try {
            // Update selected days counter
            if (data.totalDays > 0) {
                $('#selected_days_count').html(
                    `${data.totalDays} ${data.totalDays === 1 ? 'day' : 'days'} selected`
                );
            } else {
                $('#selected_days_count').html('No dates selected');
            }

            // Update single day displays
            $('#driver_earnings_per_day').html(
                `AUD ${data.driverEarningsPerDay.toFixed(2)}`
            );

            $('#platform_commission_per_day').html(
                `AUD ${data.platformCommissionPerDay.toFixed(2)}`
            );

            // Update total displays
            $('#total_driver_earnings').html(
                `AUD ${data.totalDriverEarnings.toFixed(2)}`
            );

            $('#total_platform_commission').html(
                `AUD ${data.totalPlatformCommission.toFixed(2)}`
            );

            $('#total_revenue').html(
                `AUD ${data.baseFare.toFixed(2)}`
            );

            console.log('UI Updated Successfully');

        } catch (error) {
            console.error('Error updating commission display:', error);
        }
    }

    // =============================================================================
    // CALENDAR GENERATION FUNCTIONS
    // =============================================================================

    function generateCalendar(date) {
        try {
            console.log('=== Generating Calendar ===');
            
            const year = date.getFullYear();
            const month = date.getMonth();
            
            console.log('Calendar for:', monthNames[month], year);

            // Update header
            $('#currentMonth').text(monthNames[month] + ' ' + year);

            // Generate calendar HTML
            let calendarHTML = '';
            
            // Add day headers
            dayHeaders.forEach(day => {
                calendarHTML += `<div class="calendar-day-header">${day}</div>`;
            });

            // Get first day of month
            const firstDay = new Date(year, month, 1);
            const firstDayOfWeek = firstDay.getDay();
            
            // Get last day of month
            const lastDay = new Date(year, month + 1, 0);
            const lastDate = lastDay.getDate();

            // Get last day of previous month
            const prevMonthLastDay = new Date(year, month, 0).getDate();

            // Add previous month's trailing days
            for (let i = firstDayOfWeek - 1; i >= 0; i--) {
                const dayNum = prevMonthLastDay - i;
                const prevMonth = month === 0 ? 11 : month - 1;
                const prevYear = month === 0 ? year - 1 : year;
                const prevDate = new Date(prevYear, prevMonth, dayNum);
                const dateStr = formatDateString(prevDate);
                
                calendarHTML += `<div class="calendar-day other-month disabled" data-date="${dateStr}">
                    ${dayNum}
                </div>`;
            }

            // ✅ Add current month's days (Only enable from purchase date to +30 days)
            for (let day = 1; day <= lastDate; day++) {
                const currentDate = new Date(year, month, day);
                const dateStr = formatDateString(currentDate);
                const isSelected = selectedDates.includes(dateStr);
                
                let classes = 'calendar-day';
                
                // ✅ Check if date is valid (purchase date to +30 days)
                const isValidDate = isWithin30Days(currentDate);
                const isBeforePurchase = isBeforePurchaseDate(currentDate);
                
                // ✅ Weekend check - no color change when selected
                if (isWeekend(currentDate) && !isSelected) {
                    classes += ' weekend';
                }
                
                // ✅ Disable dates before purchase date OR after 30 days
                if (!isValidDate || isBeforePurchase) {
                    classes += ' disabled';
                }
                
                // ✅ Selected date highlight (removes weekend color)
                if (isSelected) {
                    classes += ' selected';
                }

                calendarHTML += `<div class="${classes}" data-date="${dateStr}">
                    ${day}
                </div>`;
            }

            // Add next month's leading days
            const totalCells = firstDayOfWeek + lastDate;
            const remainingCells = 42 - totalCells;
            
            for (let day = 1; day <= remainingCells; day++) {
                const nextMonth = month === 11 ? 0 : month + 1;
                const nextYear = month === 11 ? year + 1 : year;
                const nextDate = new Date(nextYear, nextMonth, day);
                const dateStr = formatDateString(nextDate);
                
                calendarHTML += `<div class="calendar-day other-month disabled" data-date="${dateStr}">
                    ${day}
                </div>`;
            }

            // Update calendar
            $('#calendarGrid').html(calendarHTML);
            
            console.log('Calendar HTML generated, length:', calendarHTML.length);
            console.log('=== Calendar Generation Complete ===');

        } catch (error) {
            console.error('Error generating calendar:', error);
        }
    }

    // =============================================================================
    // DATE SELECTION FUNCTIONS
    // =============================================================================

    function updateSelectedDatesDisplay() {
        try {
            if (selectedDates.length === 0) {
                $('#selectedDatesAccordion').html('<p class="text-muted">No dates selected yet.</p>');
            } else {
                displaySelectedDatesAccordion();
            }

            // Update hidden input
            $('#selectedDatesInput').val(JSON.stringify(selectedDates));

            // Recalculate commissions
            calculateCommissions();

            console.log(`Selected dates: ${selectedDates.length}`);

        } catch (error) {
            console.error('Error updating selected dates display:', error);
        }
    }

    function toggleDateSelection(dateStr) {
        try {
            if (selectedDates.includes(dateStr)) {
                // Remove date
                selectedDates = selectedDates.filter(d => d !== dateStr);
                $(`.calendar-day[data-date="${dateStr}"]`).removeClass('selected');
                console.log(`Date deselected: ${dateStr}`);
            } else {
                // Add date
                selectedDates.push(dateStr);
                $(`.calendar-day[data-date="${dateStr}"]`).addClass('selected');
                console.log(`Date selected: ${dateStr}`);
            }

            updateSelectedDatesDisplay();

        } catch (error) {
            console.error('Error toggling date selection:', error);
        }
    }

    function displaySelectedDatesAccordion() {
        try {
            let accordionHTML = '';

            selectedDates.sort().forEach((date, index) => {
                const [year, month, day] = date.split('-').map(Number);
                const dateObj = new Date(year, month - 1, day);

                let formattedDate = dateObj.toLocaleDateString("en-AU", {
                    weekday: "short",
                    year: "numeric",
                    month: "short",
                    day: "2-digit"
                });

                const accordionId = `accordion-${date.replace(/-/g, '')}`;
                const collapseId = `collapse-${date.replace(/-/g, '')}`;

                accordionHTML += `
                    <div class="accordion-item mb-2">
                        <div class="accordion-header" id="${accordionId}">
                            <button class="accordion-button collapsed d-flex justify-content-between align-items-center w-100" style="padding: 7px;"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}"
                                    aria-expanded="false" aria-controls="${collapseId}">
                                ${formattedDate}
                            </button>
                        </div>
                        <div id="${collapseId}" class="accordion-collapse collapse"
                            aria-labelledby="${accordionId}" data-bs-parent="#selectedDatesAccordion">
                            <div class="accordion-body">
                                ${generateDateAssignmentForm(date, index)}
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#selectedDatesAccordion').html(accordionHTML);

        } catch (error) {
            console.error('Error displaying accordion:', error);
        }
    }

    function generateDateAssignmentForm(date, index) {
        let driversOptions = '<option value="">Select Driver</option>';

        @if(isset($drivers))
            @foreach($drivers as $driver)
                driversOptions += `<option value="{{ $driver->id }}">{{ $driver->driver_name }} - {{ $driver->user?->phone }}</option>`;
            @endforeach
        @endif

        return `
            <div class="date-assignment-row">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Assigned Driver:</label>
                        <select class="form-select driver-select" name="driver[${date}]" data-date="${date}">
                            ${driversOptions}
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Date:</label>
                        <input type="date" class="form-control" name="ride_date[${date}]" value="${date}" data-date="${date}" readonly>
                    </div>

                    <div class="col-md-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Pickup Time:</label>
                                <input type="time" class="form-control" name="pickup_time[${date}]" value="07:00" data-date="${date}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Dropoff Time:</label>
                                <input type="time" class="form-control" name="dropoff_time[${date}]" value="09:00" data-date="${date}">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Return Pickup:</label>
                                <input type="time" class="form-control" name="return_pickup_time[${date}]" value="15:00" data-date="${date}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Return Home:</label>
                                <input type="time" class="form-control" name="return_home_time[${date}]" value="17:00" data-date="${date}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // =============================================================================
    // EVENT HANDLERS
    // =============================================================================

    function initializeEventListeners() {
        try {
            // Commission input changes
            $('#base_fare, #driver_commission_percent, #platform_fee').on('input change', function() {
                console.log('Input changed:', $(this).attr('id'), '=', $(this).val());
                calculateCommissions();
            });

            // Calendar navigation
            $('#prevMonth').on('click', function(e) {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() - 1);
                generateCalendar(currentDate);
            });

            $('#nextMonth').on('click', function(e) {
                e.preventDefault();
                currentDate.setMonth(currentDate.getMonth() + 1);
                generateCalendar(currentDate);
            });

            // ✅ Date selection (Only dates within 30 days from purchase)
            $(document).on('click', '.calendar-day:not(.other-month):not(.disabled)', function(e) {
                e.preventDefault();
                const dateStr = $(this).data('date');
                if (dateStr) {
                    toggleDateSelection(dateStr);
                }
            });

            // Form submission
            $('#assignmentForm').on('submit', function(e) {
                if (selectedDates.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one date for the ride assignments.');
                    return false;
                }
                return true;
            });

            console.log('Event listeners initialized');

        } catch (error) {
            console.error('Error initializing event listeners:', error);
        }
    }

    // =============================================================================
    // INITIALIZATION
    // =============================================================================

    function initialize() {
        try {
            console.log('=== INITIALIZING SYSTEM ===');
            
            // Log subscription dates
            console.log('Subscription Period:', {
                start: subscriptionStartDate,
                end: subscriptionEndDate,
                startFormatted: subscriptionStartDate.toLocaleDateString('en-AU'),
                endFormatted: subscriptionEndDate.toLocaleDateString('en-AU')
            });

            // ✅ Calculate and log 30-day range
            const thirtyDaysLater = new Date(subscriptionStartDate);
            thirtyDaysLater.setDate(thirtyDaysLater.getDate() + 30);
            
            console.log('Valid Date Range (30 days):', {
                from: subscriptionStartDate.toLocaleDateString('en-AU'),
                to: thirtyDaysLater.toLocaleDateString('en-AU'),
                totalDays: 30
            });

            // Check if dates are valid
            if (isNaN(subscriptionStartDate.getTime()) || isNaN(subscriptionEndDate.getTime())) {
                console.error('Invalid subscription dates!');
                alert('Error: Invalid subscription dates. Please check the data.');
                return;
            }

            // Initialize
            initializeEventListeners();
            generateCalendar(currentDate);
            calculateCommissions();

            console.log('=== SYSTEM INITIALIZED SUCCESSFULLY ===');

        } catch (error) {
            console.error('=== INITIALIZATION FAILED ===', error);
        }
    }

    // Start application
    initialize();

    // Debug helper
    window.DebugCalendar = {
        recalculate: calculateCommissions,
        regenerateCalendar: () => generateCalendar(currentDate),
        getSelectedDates: () => selectedDates,
        clearDates: () => {
            selectedDates = [];
            updateSelectedDatesDisplay();
            generateCalendar(currentDate);
        }
    };
});

// Toastr notifications
@if(session('success'))
    toastr.success("{{ session('success') }}");
@endif

@if(session('error'))
    toastr.error("{{ session('error') }}");
@endif
        </script>
    @endpush
@endsection
