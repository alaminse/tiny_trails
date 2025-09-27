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

        .calendar-day.selected {
            background: #007bff;
            color: white;
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
                                                    <span>{{ $subscription->user->first_name }}
                                                        {{ $subscription->user->last_name }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Phone:</span>
                                                    <span>{{ $subscription->user->phone }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Kid:</span>
                                                    <span>{{ $subscription->kid->first_name }}
                                                        {{ $subscription->kid->last_name }}</span>
                                                </li>
                                                <li class="list-group-item px-0 d-flex justify-content-between">
                                                    <span class="fw-semibold text-muted">Plan:</span>
                                                    <span>{{ $subscription->plan->name }}
                                                        ({{ ucfirst($subscription->plan->interval) }}ly)</span>
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

                /**
                 * Calculate working days between two dates (excluding weekends)
                 * @param {Date} startDate - Start date
                 * @param {Date} endDate - End date
                 * @returns {number} Number of working days
                 */
                function calculateWorkingDaysBetweenDates(startDate, endDate) {
                    let workingDays = 0;
                    let currentDate = new Date(startDate);

                    while (currentDate <= endDate) {
                        const dayOfWeek = currentDate.getDay();
                        // Skip weekends (Sunday = 0, Saturday = 6)
                        if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                            workingDays++;
                        }
                        currentDate.setDate(currentDate.getDate() + 1);
                    }

                    return workingDays;
                }

                /**
                 * Format date to YYYY-MM-DD string
                 * @param {Date} date - Date to format
                 * @returns {string} Formatted date string
                 */
                function formatDateString(date) {
                    return date.toISOString().split('T')[0];
                }

                /**
                 * Check if date is weekend (Saturday or Sunday)
                 * @param {Date} date - Date to check
                 * @returns {boolean} True if weekend
                 */
                function isWeekend(date) {
                    const dayOfWeek = date.getDay();
                    return dayOfWeek === 0 || dayOfWeek === 6; // Sunday = 0, Saturday = 6
                }

                /**
                 * Check if date is within subscription period
                 * @param {Date} date - Date to check
                 * @returns {boolean} True if within subscription period
                 */
                function isWithinSubscriptionPeriod(date) {
                    return date >= subscriptionStartDate && date <= subscriptionEndDate;
                }

                /**
                 * Check if date is in the past
                 * @param {Date} date - Date to check
                 * @returns {boolean} True if date is in the past
                 */
                function isPastDate(date) {
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    return date < today;
                }

                /**
                 * Safely parse float value with fallback
                 * @param {string} value - Value to parse
                 * @param {number} fallback - Fallback value if parsing fails
                 * @returns {number} Parsed number or fallback
                 */
                function safeParseFloat(value, fallback = 0) {
                    const parsed = parseFloat(value);
                    return isNaN(parsed) ? fallback : parsed;
                }

                // =============================================================================
                // COMMISSION CALCULATION FUNCTIONS
                // =============================================================================

                /**
                 * Calculate and update commission displays
                 * Main calculation function that handles all commission logic
                 */
                function calculateCommissions() {
                    try {
                        // Get input values with safe parsing
                        const baseFare = safeParseFloat($('#base_fare').val());
                        const driverCommissionPercent = safeParseFloat($('#driver_commission_percent').val());
                        const platformFeePercent = safeParseFloat($('#platform_fee').val());

                        // Calculate total working days from subscription period
                        const totalWorkingDays = calculateWorkingDaysBetweenDates(subscriptionStartDate,
                            subscriptionEndDate);
                        $('#totalWorkingDays').val(totalWorkingDays);

                        const selectedDaysCount = selectedDates.length;

                        // Validate inputs
                        if (baseFare <= 0) {
                            console.warn('Base fare must be greater than 0');
                            return;
                        }

                        // Single day calculations - percentage based
                        const driverEarningsPerDay = (baseFare * (driverCommissionPercent / 100)) / totalWorkingDays;
                        const platformCommissionPerDay = (baseFare * (platformFeePercent / 100)) / totalWorkingDays;

                        // Total calculations for ALL working days in subscription period (automatic)
                        const totalDriverEarnings = driverEarningsPerDay * totalWorkingDays;
                        const totalPlatformCommission = platformCommissionPerDay * totalWorkingDays;

                        // Selected days calculations (manual selection)
                        const selectedDriverEarnings = driverEarningsPerDay * selectedDaysCount;
                        const selectedPlatformCommission = platformCommissionPerDay * selectedDaysCount;
                        const selectedRevenue = baseFare * selectedDaysCount;

                        // Create data object for UI update
                        const calculationData = {
                            // Basic values
                            baseFare,
                            driverCommissionPercent,
                            platformFeePercent,
                            totalWorkingDays,
                            selectedDaysCount,

                            // Per day calculations
                            driverEarningsPerDay,
                            platformCommissionPerDay,

                            // Total calculations
                            totalDriverEarnings,
                            totalPlatformCommission,

                            // Selected calculations
                            selectedDriverEarnings,
                            selectedPlatformCommission,
                            selectedRevenue
                        };

                        // Update UI with calculated data
                        updateCommissionDisplay(calculationData);

                        // Debug logging
                        console.log('Commission Calculation Complete:', {
                            inputs: {
                                baseFare,
                                driverPercent: driverCommissionPercent,
                                platformPercent: platformFeePercent
                            },
                            perDay: {
                                driver: driverEarningsPerDay,
                                platform: platformCommissionPerDay
                            },
                            totals: {
                                workingDays: totalWorkingDays,
                                driver: totalDriverEarnings,
                                platform: totalPlatformCommission,
                            }
                        });

                    } catch (error) {
                        console.error('Error in calculateCommissions:', error);
                    }
                }

                /**
                 * Update commission display elements in the UI
                 * @param {Object} data - Calculated commission data
                 */
                function updateCommissionDisplay(data) {
                    try {
                        // Update working days counter with badges
                        $('#selected_days_count').html(
                            `<span>${data.selectedDaysCount} selected</span> /
                            <span>${data.totalWorkingDays} total working days</span>`
                        );

                        // Update single day displays with calculation breakdown
                        $('#driver_earnings_per_day').html(
                            `<strong>AUD ${data.driverEarningsPerDay.toFixed(2)}</strong>`
                        );

                        $('#platform_commission_per_day').html(
                            `<strong>AUD ${data.platformCommissionPerDay.toFixed(2)}</strong>`
                        );

                        // Update total displays for all working days (removed selected totals)
                        $('#total_driver_earnings').html(
                            `<strong>AUD ${data.totalDriverEarnings.toFixed(2)}</strong>
                            <small class="text-muted">(${data.driverEarningsPerDay.toFixed(2)} × ${data.totalWorkingDays} days)</small>`
                        );

                        $('#total_platform_commission').html(
                            `<strong>AUD ${data.totalPlatformCommission.toFixed(2)}</strong>
                            <small class="text-muted">(${data.platformCommissionPerDay.toFixed(2)} × ${data.totalWorkingDays} days)</small>`
                        );

                        $('#total_revenue').html(
                            `<strong>AUD ${data.baseFare.toFixed(2)}</strong>
                            <small class="text-muted">(${data.totalWorkingDays} days)</small>`
                        );

                    } catch (error) {
                        console.error('Error updating commission display:', error);
                    }
                }

                // =============================================================================
                // CALENDAR GENERATION FUNCTIONS
                // =============================================================================

                /**
                 * Generate calendar for given month/year
                 * @param {Date} date - Date representing the month to display
                 */
                function generateCalendar(date) {
                    try {
                        const year = date.getFullYear();
                        const month = date.getMonth();
                        const firstDay = new Date(year, month, 1);
                        const startDate = new Date(firstDay);

                        // Set start date to first day of calendar (may be from previous month)
                        startDate.setDate(startDate.getDate() - firstDay.getDay());

                        // Update calendar header
                        $('#currentMonth').text(monthNames[month] + ' ' + year);

                        // Generate complete calendar HTML
                        let calendarHTML = generateCalendarHeaders();
                        calendarHTML += generateCalendarDays(startDate, month);

                        // Update calendar display
                        $('#calendarGrid').html(calendarHTML);

                        console.log(`Calendar generated for ${monthNames[month]} ${year}`);

                    } catch (error) {
                        console.error('Error generating calendar:', error);
                    }
                }

                /**
                 * Generate calendar day headers (Sun, Mon, Tue, etc.)
                 * @returns {string} HTML for calendar headers
                 */
                function generateCalendarHeaders() {
                    let headerHTML = '';
                    dayHeaders.forEach(day => {
                        headerHTML += `<div class="calendar-day-header">${day}</div>`;
                    });
                    return headerHTML;
                }

                /**
                 * Generate calendar days for the month
                 * @param {Date} startDate - First date to display (may be from previous month)
                 * @param {number} currentMonth - Current month being displayed (0-11)
                 * @returns {string} HTML for calendar days
                 */
                function generateCalendarDays(startDate, currentMonth) {
                    let daysHTML = '';
                    let currentCalendarDate = new Date(startDate);

                    // Generate 6 weeks of days (42 days total)
                    for (let i = 0; i < 42; i++) {
                        const dateStr = formatDateString(currentCalendarDate);
                        const isCurrentMonth = currentCalendarDate.getMonth() === currentMonth;
                        const isSelected = selectedDates.includes(dateStr);

                        const dayClasses = getDayClasses(currentCalendarDate, isCurrentMonth, isSelected);

                        daysHTML += `<div class="${dayClasses}" data-date="${dateStr}" title="${formatDateForDisplay(currentCalendarDate)}">
                            ${currentCalendarDate.getDate()}
                        </div>`;

                        currentCalendarDate.setDate(currentCalendarDate.getDate() + 1);
                    }

                    return daysHTML;
                }

                /**
                 * Get CSS classes for calendar day based on its state
                 * @param {Date} date - Date for the calendar day
                 * @param {boolean} isCurrentMonth - Whether date is in current displayed month
                 * @param {boolean} isSelected - Whether date is selected
                 * @returns {string} Space-separated CSS classes
                 */
                function getDayClasses(date, isCurrentMonth, isSelected) {
                    let classes = ['calendar-day'];

                    // Add state-based classes
                    if (isWeekend(date)) classes.push('weekend');
                    if (!isCurrentMonth) classes.push('other-month');
                    if (isWeekend(date) || !isWithinSubscriptionPeriod(date) || isPastDate(date)) {
                        classes.push('disabled');
                    }
                    if (isSelected) classes.push('selected');

                    return classes.join(' ');
                }

                /**
                 * Format date for display in tooltips
                 * @param {Date} date - Date to format
                 * @returns {string} Formatted date string
                 */
                function formatDateForDisplay(date) {
                    return date.toLocaleDateString('en-AU', {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                }

                // =============================================================================
                // DATE SELECTION FUNCTIONS
                // =============================================================================

                function updateSelectedDatesDisplay() {
                    try {
                        if (selectedDates.length === 0) {
                            $('#selectedDatesDisplay').hide();
                        } else {
                            $('#selectedDatesDisplay').show();
                            displaySelectedDatesAccordion(); // Replace displaySelectedDateTags() with this
                        }

                        // Update hidden form input for submission
                        $('#selectedDatesInput').val(JSON.stringify(selectedDates));

                        console.log(`Selected dates updated: ${selectedDates.length} dates selected`);

                    } catch (error) {
                        console.error('Error updating selected dates display:', error);
                    }
                }

                /**
                 * Display selected date tags in a user-friendly format
                 */
                function displaySelectedDateTags() {
                    try {
                        let tagsHTML = '';

                        // Sort dates and create tags
                        selectedDates.sort().forEach(date => {
                            const formattedDate = new Date(date + 'T00:00:00').toLocaleDateString('en-AU', {
                                month: 'short',
                                day: 'numeric'
                            });
                            tagsHTML +=
                                `<span class="selected-date-tag" data-date="${date}">${formattedDate}</span>`;
                        });

                        $('#selectedDatesTags').html(tagsHTML);

                    } catch (error) {
                        console.error('Error displaying selected date tags:', error);
                    }
                }

                /**
                 * Handle date selection/deselection
                 * @param {string} dateStr - Date string in YYYY-MM-DD format
                 */
                function toggleDateSelection(dateStr) {
                    try {
                        if (selectedDates.includes(dateStr)) {
                            // Remove date from selection
                            selectedDates = selectedDates.filter(d => d !== dateStr);
                            $(`.calendar-day[data-date="${dateStr}"]`).removeClass('selected');
                            console.log(`Date deselected: ${dateStr}`);
                        } else {
                            // Add date to selection
                            selectedDates.push(dateStr);
                            $(`.calendar-day[data-date="${dateStr}"]`).addClass('selected');
                            console.log(`Date selected: ${dateStr}`);
                        }

                        // Update displays and recalculate
                        updateSelectedDatesDisplay();

                    } catch (error) {
                        console.error('Error toggling date selection:', error);
                    }
                }

                /**
                 * Clear all selected dates
                 */
                function clearAllSelectedDates() {
                    selectedDates = [];
                    $('.calendar-day').removeClass('selected');
                    updateSelectedDatesDisplay();
                    console.log('All selected dates cleared');
                }

                // =============================================================================
                // EVENT HANDLERS
                // =============================================================================

                /**
                 * Initialize all event listeners
                 */
                function initializeEventListeners() {
                    try {
                        // Commission input changes - trigger recalculation
                        $('#base_fare, #driver_commission_percent, #platform_fee').on('input change', function() {
                            calculateCommissions();
                        });

                        // Calendar navigation - previous month
                        $('#prevMonth').on('click', function(e) {
                            e.preventDefault();
                            currentDate.setMonth(currentDate.getMonth() - 1);
                            generateCalendar(currentDate);
                        });

                        // Calendar navigation - next month
                        $('#nextMonth').on('click', function(e) {
                            e.preventDefault();
                            currentDate.setMonth(currentDate.getMonth() + 1);
                            generateCalendar(currentDate);
                        });

                        // Date selection - handle clicks on calendar days
                        $(document).on('click', '.calendar-day:not(.disabled):not(.other-month)', function(e) {
                            e.preventDefault();
                            const dateStr = $(this).data('date');
                            if (dateStr) {
                                toggleDateSelection(dateStr);
                            }
                        });

                        // Form submission validation
                        $('#assignmentForm').on('submit', function(e) {
                            if (selectedDates.length === 0) {
                                e.preventDefault();
                                alert('Please select at least one date for the ride assignments.');
                                return false;
                            }

                            console.log('Form submitted with selected dates:', selectedDates);
                            return true;
                        });

                        console.log('Event listeners initialized successfully');

                    } catch (error) {
                        console.error('Error initializing event listeners:', error);
                    }
                }

                // =============================================================================
                // INITIALIZATION & STARTUP
                // =============================================================================

                /**
                 * Validate that required data is available
                 * @returns {boolean} True if validation passes
                 */
                function validateInitialization() {
                    // Check if subscription dates are properly loaded
                    if (!subscriptionStartDate || !subscriptionEndDate) {
                        console.error('Subscription dates not properly loaded from Laravel');
                        return false;
                    }

                    // Check if required DOM elements exist
                    const requiredElements = [
                        '#base_fare', '#driver_commission_percent', '#platform_fee',
                        '#calendarGrid', '#currentMonth', '#selectedDatesInput'
                    ];

                    for (let element of requiredElements) {
                        if ($(element).length === 0) {
                            console.error(`Required element not found: ${element}`);
                            return false;
                        }
                    }

                    return true;
                }

                /**
                 * Initialize the complete application
                 * Main entry point for the commission and calendar system
                 */
                function initialize() {
                    try {
                        console.log('Initializing Commission & Calendar System...');

                        // Validate prerequisites
                        if (!validateInitialization()) {
                            console.error('Initialization failed - missing required data or elements');
                            return;
                        }

                        // Initialize components in correct order
                        initializeEventListeners();
                        generateCalendar(currentDate);

                        // Force initial calculation after DOM is ready
                        setTimeout(function() {
                            calculateCommissions();
                        }, 100);

                        // Log initialization success with debug info
                        const totalWorkingDays = calculateWorkingDaysBetweenDates(subscriptionStartDate,
                            subscriptionEndDate);

                        console.log('✅ Commission & Calendar System Initialized Successfully');
                        console.log('📅 Subscription Period:', {
                            start: subscriptionStartDate.toLocaleDateString('en-AU'),
                            end: subscriptionEndDate.toLocaleDateString('en-AU'),
                            totalWorkingDays: totalWorkingDays
                        });
                        console.log('💰 Initial Commission Values:', {
                            baseFare: safeParseFloat($('#base_fare').val()),
                            driverPercent: safeParseFloat($('#driver_commission_percent').val()),
                            platformPercent: safeParseFloat($('#platform_fee').val())
                        });

                    } catch (error) {
                        console.error('❌ Failed to initialize Commission & Calendar System:', error);
                    }
                }

                function displaySelectedDatesAccordion() {
                    try {
                        let accordionHTML = '';

                        selectedDates.sort().forEach((date, index) => {
                            // Fix: Add timezone handling to prevent date shifting
                            const [year, month, day] = date.split('-').map(Number);
                            const dateObj = new Date(year, month - 1, day + 1);

                            let formattedDate = dateObj.toLocaleString("en-AU", {
                                timeZone: "Australia/Sydney",
                                weekday: "short",   // Mon, Tue, etc.
                                year: "numeric",
                                month: "short",     // Jan, Feb, etc.
                                day: "2-digit"
                            });

                            const accordionId = `accordion-${date.replace(/-/g, '')}`;
                            const collapseId = `collapse-${date.replace(/-/g, '')}`;

                            accordionHTML += `
                                <div class="accordion-item mb-2">
                                    <div class="accordion-header" id="${accordionId}">
                                        <button class="accordion-button collapsed d-flex justify-content-between align-items-center w-100" style="padding: 7px;"
                                                type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}"
                                                aria-expanded="false" aria-controls="${collapseId}"> ${formattedDate}
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
                        console.error('Error displaying selected dates accordion:', error);
                    }
                }

                function generateDateAssignmentForm(date, index) {
                    // Generate driver options from Laravel data
                    let driversOptions = '<option value="">Select Driver</option>';

                    @if(isset($drivers))
                        @foreach($drivers as $driver)
                            driversOptions += `<option value="{{ $driver->id }}">{{ $driver->driver_name }} - {{ $driver->user->phone }}</option>`;
                        @endforeach
                    @endif

                    return `
                        <div class="date-assignment-row">
                            <div class="row g-3 align-items-end">
                                <!-- Driver Selection -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Assigned Driver:</label>
                                    <select class="form-select driver-select" name="driver[${date}]" data-date="${date}">
                                        ${driversOptions}
                                    </select>
                                </div>

                                <!-- Date -->
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Date:</label>
                                    <input type="date" class="form-control" name="ride_date[${date}]" value="${date}" data-date="${date}">
                                </div>

                                <!-- Pickup + Dropoff -->
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
                // START APPLICATION
                // =============================================================================

                // Start the application when DOM is ready
                initialize();

                // Expose useful functions to global scope for debugging
                window.CommissionCalendar = {
                    calculateCommissions,
                    generateCalendar,
                    clearAllSelectedDates,
                    getSelectedDates: () => selectedDates,
                    getTotalWorkingDays: () => calculateWorkingDaysBetweenDates(subscriptionStartDate,
                        subscriptionEndDate)
                };
            });

            @if(session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        </script>
    @endpush
@endsection
