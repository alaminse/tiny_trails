<!-- Ride Assignment Modal -->
<div class="modal fade" id="rideModal" tabindex="-1" aria-labelledby="rideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rideModalLabel">
                    <i class="fas fa-route me-2"></i>
                    <span id="modalTitle">Add Ride Assignment</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="rideForm" method="POST">
                @csrf
                <input type="hidden" id="method" name="_method" value="POST">
                <input type="hidden" id="ride_id" name="ride_id">
                
                <div class="modal-body">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Basic Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ride_title" class="form-label">Ride Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="ride_title" name="ride_title" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Financial Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-dollar-sign me-2"></i>Financial Information
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="ride_fare" class="form-label">Ride Fare <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="ride_fare" name="ride_fare" min="0" required>
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="driver_commission" class="form-label">Driver Commission</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="driver_commission" name="driver_commission" min="0">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="platform_fee" class="form-label">Platform Fee</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" id="platform_fee" name="platform_fee" min="0">
                            </div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Instructions & Notes -->
                        <div class="col-md-6 mb-3">
                            <label for="special_instructions" class="form-label">Special Instructions</label>
                            <textarea class="form-control" id="special_instructions" name="special_instructions" rows="3" maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="1000"></textarea>
                            <div class="form-text">Maximum 1000 characters</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Cancellation Reason (only show when editing cancelled rides) -->
                        <div class="col-12 mb-3" id="cancellation_section" style="display: none;">
                            <label for="cancellation_reason" class="form-label">Cancellation Reason</label>
                            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="2" maxlength="500"></textarea>
                            <div class="form-text">Maximum 500 characters</div>
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Save Ride Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Handle recurring checkbox
    $('#is_recurring').on('change', function() {
        if ($(this).is(':checked')) {
            $('#recurring_options').show();
            $('#recurring_end_date').attr('required', true);
        } else {
            $('#recurring_options').hide();
            $('#recurring_end_date').attr('required', false);
            $('input[name="recurring_days[]"]').prop('checked', false);
        }
    });
    
    // Handle status change
    $('#status').on('change', function() {
        if ($(this).val() === 'cancelled') {
            $('#cancellation_section').show();
        } else {
            $('#cancellation_section').hide();
        }
    });
    
    // Auto-calculate commission when fare changes
    $('#ride_fare').on('input', function() {
        const fare = parseFloat($(this).val()) || 0;
        const commissionRate = 15; // 15% default
        const commission = (fare * commissionRate) / 100;
        const platformFee = fare - commission;
        
        $('#driver_commission').val(commission.toFixed(2));
        $('#platform_fee').val(platformFee.toFixed(2));
    });
    
    // Validate pickup time vs dropoff time
    $('#pickup_time, #estimated_dropoff_time').on('change', function() {
        const pickupTime = $('#pickup_time').val();
        const dropoffTime = $('#estimated_dropoff_time').val();
        
        if (pickupTime && dropoffTime && dropoffTime <= pickupTime) {
            $('#estimated_dropoff_time')[0].setCustomValidity('Dropoff time must be after pickup time');
        } else {
            $('#estimated_dropoff_time')[0].setCustomValidity('');
        }
    });
    
    // Validate recurring end date
    $('#ride_date, #recurring_end_date').on('change', function() {
        const rideDate = $('#ride_date').val();
        const endDate = $('#recurring_end_date').val();
        
        if (rideDate && endDate && endDate <= rideDate) {
            $('#recurring_end_date')[0].setCustomValidity('End date must be after ride date');
        } else {
            $('#recurring_end_date')[0].setCustomValidity('');
        }
    });
});
</script>
                        {{-- <div class="col-md-6 mb-3">
                            <label for="ride_type" class="form-label">Ride Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="ride_type" name="ride_type" required>
                                <option value="">Select Ride Type</option>
                                <option value="one_time">One Time</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                                <option value="custom">Custom</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Participants -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-users me-2"></i>Participants
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="driver_id" class="form-label">Driver <span class="text-danger">*</span></label>
                            <select class="form-select" id="driver_id" name="driver_id" required>
                                <option value="">Select Driver</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->email }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="parent_id" class="form-label">Parent <span class="text-danger">*</span></label>
                            <select class="form-select" id="parent_id" name="parent_id" required>
                                <option value="">Select Parent</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="kid_id" class="form-label">Kid</label>
                            <select class="form-select" id="kid_id" name="kid_id">
                                <option value="">Select Kid (Optional)</option>
                                @foreach($kids as $kid)
                                    <option value="{{ $kid->id }}">{{ $kid->first_name }} {{ $kid->last_name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="subscription_id" class="form-label">Subscription</label>
                            <select class="form-select" id="subscription_id" name="subscription_id">
                                <option value="">Select Subscription (Optional)</option>
                                @foreach($subscriptions as $subscription)
                                    <option value="{{ $subscription->id }}">
                                        {{ $subscription->user->name }} - {{ $subscription->plan->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Route Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-map-marked-alt me-2"></i>Route Information
                            </h6>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="pickup_location" class="form-label">Pickup Location <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="pickup_location" name="pickup_location" rows="2" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="dropoff_location" class="form-label">Dropoff Location <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="dropoff_location" name="dropoff_location" rows="2" required></textarea>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="pickup_latitude" class="form-label">Pickup Latitude</label>
                            <input type="number" step="any" class="form-control" id="pickup_latitude" name="pickup_latitude" min="-90" max="90">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="pickup_longitude" class="form-label">Pickup Longitude</label>
                            <input type="number" step="any" class="form-control" id="pickup_longitude" name="pickup_longitude" min="-180" max="180">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="dropoff_latitude" class="form-label">Dropoff Latitude</label>
                            <input type="number" step="any" class="form-control" id="dropoff_latitude" name="dropoff_latitude" min="-90" max="90">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="dropoff_longitude" class="form-label">Dropoff Longitude</label>
                            <input type="number" step="any" class="form-control" id="dropoff_longitude" name="dropoff_longitude" min="-180" max="180">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Schedule Information -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-calendar-alt me-2"></i>Schedule Information
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="ride_date" class="form-label">Ride Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ride_date" name="ride_date" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="pickup_time" class="form-label">Pickup Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="pickup_time" name="pickup_time" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="estimated_dropoff_time" class="form-label">Estimated Dropoff Time</label>
                            <input type="time" class="form-control" id="estimated_dropoff_time" name="estimated_dropoff_time">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <!-- Recurring Options -->
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1">
                                <label class="form-check-label fw-bold" for="is_recurring">
                                    <i class="fas fa-repeat me-2"></i>Recurring Ride
                                </label>
                            </div>
                        </div>
                        
                        <div id="recurring_options" class="col-12" style="display: none;">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Recurring Days</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="monday" name="recurring_days[]" value="monday">
                                            <label class="form-check-label" for="monday">Mon</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="tuesday" name="recurring_days[]" value="tuesday">
                                            <label class="form-check-label" for="tuesday">Tue</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="wednesday" name="recurring_days[]" value="wednesday">
                                            <label class="form-check-label" for="wednesday">Wed</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="thursday" name="recurring_days[]" value="thursday">
                                            <label class="form-check-label" for="thursday">Thu</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="friday" name="recurring_days[]" value="friday">
                                            <label class="form-check-label" for="friday">Fri</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="saturday" name="recurring_days[]" value="saturday">
                                            <label class="form-check-label" for="saturday">Sat</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="sunday" name="recurring_days[]" value="sunday">
                                            <label class="form-check-label" for="sunday">Sun</label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="recurring_end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="recurring_end_date" name="recurring_end_date">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Additional Details -->
                        <div class="col-12 mt-3">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-cog me-2"></i>Additional Details
                            </h6>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="distance_km" class="form-label">Distance (KM)</label>
                            <input type="number" step="0.01" class="form-control" id="distance_km" name="distance_km" min="0">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="estimated_duration_minutes" class="form-label">Duration (Minutes)</label>
                            <input type="number" class="form-control" id="estimated_duration_minutes" name="estimated_duration_minutes" min="1">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="assigned">Assigned</option>
                                <option value="accepted">Accepted</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div> --}}