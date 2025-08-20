{{-- File: Modules/RideManagement/Resources/views/ride-assignment/table-rows.blade.php --}}

@forelse ($data as $index => $ride)
<tr class="ride-row" data-ride-id="{{ $ride->id }}">
    <td>
        <input type="checkbox" class="form-check-input ride-checkbox" value="{{ $ride->id }}">
    </td>
    <td>{{ $index + 1 }}</td>
    <td>
        <div class="ride-details">
            <h6 class="mb-1 fw-bold">{{ $ride->ride_title }}</h6>
            <div class="d-flex flex-wrap gap-1">
                <span class="badge bg-secondary">{{ $ride->ride_type_display }}</span>
                @if($ride->is_recurring)
                    <span class="badge bg-info">
                        <i class="fas fa-repeat"></i> Recurring
                    </span>
                @endif
                @if($ride->subscription)
                    <span class="badge bg-primary">
                        <i class="fas fa-star"></i> Subscription
                    </span>
                @endif
            </div>
            <small class="text-muted">
                <i class="fas fa-calendar"></i> {{ $ride->formatted_ride_date }}
            </small>
        </div>
    </td>
    <td>
        <div class="driver-info">
            @if($ride->driver)
                <div class="d-flex align-items-center">
                    <div class="avatar-sm me-2">
                        @if($ride->driver->profile_picture)
                            <img src="{{ asset($ride->driver->profile_picture) }}" class="rounded-circle" width="32" height="32" alt="Driver">
                        @else
                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="fw-bold">{{ $ride->driver->name }}</div>
                        <small class="text-muted">{{ $ride->driver->email }}</small>
                        @if($ride->driver->phone)
                            <div><small class="text-info">{{ $ride->driver->phone }}</small></div>
                        @endif
                    </div>
                </div>
            @else
                <span class="text-warning">
                    <i class="fas fa-exclamation-triangle"></i> Unassigned
                </span>
            @endif
        </div>
    </td>
    <td>
        <div class="parent-info">
            @if($ride->parent)
                <div class="mb-2">
                    <div class="fw-bold">{{ $ride->parent->name }}</div>
                    <small class="text-muted">{{ $ride->parent->email }}</small>
                </div>
            @endif
            @if($ride->kid)
                <div class="kid-info">
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-child"></i> 
                        {{ $ride->kid->first_name }} {{ $ride->kid->last_name }}
                    </span>
                    @if($ride->kid->age)
                        <small class="text-muted">({{ $ride->kid->age }}y)</small>
                    @endif
                </div>
            @endif
        </div>
    </td>
    <td>
        <div class="schedule-info">
            <div class="fw-bold">{{ $ride->formatted_pickup_time }}</div>
            @if($ride->estimated_dropoff_time)
                <small class="text-muted">
                    Est: {{ $ride->formatted_estimated_dropoff_time }}
                </small>
            @endif
            @if($ride->duration_display)
                <div><small class="text-info">{{ $ride->duration_display }}</small></div>
            @endif
            @if($ride->is_recurring && $ride->recurring_days)
                <div class="mt-1">
                    <small class="text-secondary">
                        <i class="fas fa-repeat"></i> {{ $ride->recurring_days_display }}
                    </small>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div class="route-info">
            <div class="pickup mb-1">
                <i class="fas fa-map-marker-alt text-success"></i>
                <small>{{ Str::limit($ride->pickup_location, 25) }}</small>
            </div>
            <div class="dropoff mb-1">
                <i class="fas fa-map-marker-alt text-danger"></i>
                <small>{{ Str::limit($ride->dropoff_location, 25) }}</small>
            </div>
            @if($ride->distance_km)
                <div>
                    <small class="text-muted">
                        <i class="fas fa-route"></i> {{ number_format($ride->distance_km, 1) }} km
                    </small>
                </div>
            @endif
        </div>
    </td>
    <td>
        <div class="fare-info">
            <div class="fw-bold h6 text-primary mb-1">{{ $ride->formatted_ride_fare }}</div>
            @if($ride->driver_commission > 0)
                <small class="text-success d-block">
                    <i class="fas fa-coins"></i> {{ $ride->formatted_driver_commission }}
                </small>
            @endif
            @if($ride->platform_fee > 0)
                <small class="text-info d-block">
                    Platform: ৳{{ number_format($ride->platform_fee, 2) }}
                </small>
            @endif
        </div>
    </td>
    <td>
        <div class="status-info text-center">
            {!! $ride->status_badge !!}
            @if($ride->isPastDue())
                <div class="mt-1">
                    <span class="badge bg-warning">
                        <i class="fas fa-clock"></i> Overdue
                    </span>
                </div>
            @endif
        </div>
    </td>
    <td>
        @if ($ride->trashed())
            {{-- Trashed ride actions --}}
            <div class="action-buttons d-flex flex-wrap gap-1">
                <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $ride->id }}" title="Restore">
                    <i class="fas fa-undo"></i>
                </button>
                <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $ride->id }}" title="Delete Permanently">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        @else
            {{-- Active ride actions --}}
            <div class="action-buttons d-flex flex-wrap gap-1">
                {{-- Always available actions --}}
                <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $ride->id }}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-gradient-info btn-sm showBtn" data-id="{{ $ride->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                
                {{-- Status-based actions --}}
                @if($ride->canBeAccepted())
                    <button class="btn btn-gradient-success btn-sm acceptBtn" data-id="{{ $ride->id }}" title="Accept Ride">
                        <i class="fas fa-check"></i>
                    </button>
                @endif
                
                @if($ride->canBeStarted())
                    <button class="btn btn-gradient-warning btn-sm startBtn" data-id="{{ $ride->id }}" title="Start Ride">
                        <i class="fas fa-play"></i>
                    </button>
                @endif
                
                @if($ride->canBeCompleted())
                    <button class="btn btn-gradient-success btn-sm completeBtn" data-id="{{ $ride->id }}" title="Complete Ride">
                        <i class="fas fa-flag-checkered"></i>
                    </button>
                @endif
                
                @if($ride->canBeCancelled())
                    <button class="btn btn-gradient-danger btn-sm cancelBtn" data-id="{{ $ride->id }}" title="Cancel Ride">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
                
                @if($ride->isActive())
                    <button class="btn btn-gradient-secondary btn-sm noShowBtn" data-id="{{ $ride->id }}" title="Mark No Show">
                        <i class="fas fa-user-times"></i>
                    </button>
                @endif
                
                {{-- Additional actions --}}
                @if(!$ride->isCompleted() && !$ride->isCancelled())
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-gradient-info btn-sm dropdown-toggle" data-bs-toggle="dropdown" title="More Actions">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="#" data-action="duplicate" data-id="{{ $ride->id }}">
                                    <i class="fas fa-copy me-2"></i>Duplicate
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#" data-action="reschedule" data-id="{{ $ride->id }}">
                                    <i class="fas fa-calendar-alt me-2"></i>Reschedule
                                </a>
                            </li>
                            @if($ride->is_recurring)
                                <li>
                                    <a class="dropdown-item" href="#" data-action="stop-recurring" data-id="{{ $ride->id }}">
                                        <i class="fas fa-stop me-2"></i>Stop Recurring
                                    </a>
                                </li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="#" data-action="send-notification" data-id="{{ $ride->id }}">
                                    <i class="fas fa-bell me-2"></i>Send Notification
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
                
                {{-- Delete action --}}
                <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $ride->id }}" title="Move to Trash">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="10" class="text-center py-4">
        <div class="empty-state">
            <i class="fas fa-car-side fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Ride Assignments Found</h5>
            <p class="text-muted">There are no ride assignments matching your current filters.</p>
            <button class="btn btn-gradient-primary" id="createFirstRide">
                <i class="fas fa-plus me-2"></i>Create First Ride Assignment
            </button>
        </div>
    </td>
</tr>
@endforelse

<script>
// Additional JavaScript for row actions
$(document).ready(function() {
    // Handle dropdown actions
    $(document).on('click', '[data-action]', function(e) {
        e.preventDefault();
        const action = $(this).data('action');
        const rideId = $(this).data('id');
        
        switch(action) {
            case 'duplicate':
                duplicateRide(rideId);
                break;
            case 'reschedule':
                rescheduleRide(rideId);
                break;
            case 'stop-recurring':
                stopRecurringRide(rideId);
                break;
            case 'send-notification':
                sendNotification(rideId);
                break;
        }
    });
    
    // Restore button
    $(document).on('click', '.restoreBtn', function() {
        const rideId = $(this).data('id');
        restoreRide(rideId);
    });
    
    // Force delete button
    $(document).on('click', '.forceDeleteBtn', function() {
        const rideId = $(this).data('id');
        forceDeleteRide(rideId);
    });
    
    // No show button
    $(document).on('click', '.noShowBtn', function() {
        const rideId = $(this).data('id');
        markAsNoShow(rideId);
    });
    
    // Create first ride button
    $(document).on('click', '#createFirstRide', function() {
        $('#addRideBtn').click();
    });
});

function duplicateRide(rideId) {
    Swal.fire({
        title: 'Duplicate Ride?',
        text: 'This will create a new ride with the same details.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, duplicate it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement duplicate functionality
            console.log('Duplicate ride:', rideId);
        }
    });
}

function rescheduleRide(rideId) {
    // Open a modal or form to reschedule the ride
    console.log('Reschedule ride:', rideId);
}

function stopRecurringRide(rideId) {
    Swal.fire({
        title: 'Stop Recurring Rides?',
        text: 'This will stop all future recurring rides for this series.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, stop recurring!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement stop recurring functionality
            console.log('Stop recurring ride:', rideId);
        }
    });
}

function sendNotification(rideId) {
    Swal.fire({
        title: 'Send Notification?',
        text: 'This will send a notification to the driver and parent.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, send it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Implement send notification functionality
            console.log('Send notification for ride:', rideId);
        }
    });
}

function restoreRide(rideId) {
    $.ajax({
        url: `{{ url('admin/ride-assignments/restore') }}/${rideId}`,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                Swal.fire('Restored!', response.message, 'success');
                loadRideAssignments();
            } else {
                Swal.fire('Error!', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error!', 'Failed to restore ride.', 'error');
        }
    });
}

function forceDeleteRide(rideId) {
    Swal.fire({
        title: 'Permanently Delete?',
        text: 'This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete permanently!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('admin/ride-assignments/force-delete') }}/${rideId}`,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Deleted!', response.message, 'success');
                        loadRideAssignments();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to delete ride.', 'error');
                }
            });
        }
    });
}

function markAsNoShow(rideId) {
    Swal.fire({
        title: 'Mark as No Show?',
        text: 'This will mark the ride as no show.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, mark as no show!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `{{ url('admin/ride-assignments/mark-no-show') }}/${rideId}`,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Updated!', response.message, 'success');
                        loadRideAssignments();
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Failed to update ride.', 'error');
                }
            });
        }
    });
}
</script>