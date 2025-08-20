<!-- Ride Assignment Show Modal -->
<div class="modal fade" id="rideShowModal" tabindex="-1" aria-labelledby="rideShowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="rideShowModalLabel">
                    <i class="fas fa-route me-2"></i>Ride Assignment Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-12 mb-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Ride Title:</strong>
                                        <p id="ride_title" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Date:</strong>
                                        <p id="ride_date_display" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Status:</strong>
                                        <p id="status_badge" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Pickup Time:</strong>
                                        <p id="pickup_time_display" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Estimated Dropoff:</strong>
                                        <p id="estimated_dropoff_time_display" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Ride Type:</strong>
                                        <p id="ride_type_display" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created:</strong>
                                        <p id="created_at" class="mb-2">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Participants -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Driver Information</h6>
                            </div>
                            <div class="card-body">
                                <strong>Name:</strong>
                                <p id="driver_name" class="mb-2">-</p>
                                <strong>Email:</strong>
                                <p id="driver_email" class="mb-2">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>Parent Information</h6>
                            </div>
                            <div class="card-body">
                                <strong>Name:</strong>
                                <p id="parent_name" class="mb-2">-</p>
                                <strong>Email:</strong>
                                <p id="parent_email" class="mb-2">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kid Information (optional) -->
                    <div class="col-12 mb-4" id="kid_section">
                        <div class="card border-warning">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-child me-2"></i>Kid Information</h6>
                            </div>
                            <div class="card-body">
                                <strong>Name:</strong>
                                <p id="kid_name" class="mb-2">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Route Information -->
                    <div class="col-12 mb-4">
                        <div class="card border-secondary">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-map-marked-alt me-2"></i>Route Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong><i class="fas fa-map-marker-alt text-success me-2"></i>Pickup Location:</strong>
                                        <p id="pickup_location" class="mb-3">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong><i class="fas fa-map-marker-alt text-danger me-2"></i>Dropoff Location:</strong>
                                        <p id="dropoff_location" class="mb-3">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Distance:</strong>
                                        <p id="distance_km" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Duration:</strong>
                                        <p id="duration_display" class="mb-2">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Financial Information -->
                    <div class="col-12 mb-4">
                        <div class="card border-dark">
                            <div class="card-header bg-dark text-white">
                                <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Financial Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Ride Fare:</strong>
                                        <p id="ride_fare" class="mb-2 h5 text-primary">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Driver Commission:</strong>
                                        <p id="driver_commission" class="mb-2 h6 text-success">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Platform Fee:</strong>
                                        <p id="platform_fee" class="mb-2 h6 text-info">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recurring Information (if applicable) -->
                    <div class="col-12 mb-4" id="recurring_section">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-repeat me-2"></i>Recurring Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Recurring Days:</strong>
                                        <p id="recurring_days_display" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>End Date:</strong>
                                        <p id="recurring_end_date" class="mb-2">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>Special Instructions</h6>
                            </div>
                            <div class="card-body">
                                <p id="special_instructions" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card border-light">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                            </div>
                            <div class="card-body">
                                <p id="notes" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status Timeline -->
                    <div class="col-12 mb-4">
                        <div class="card border-primary">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Status Timeline</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Accepted At:</strong>
                                        <p id="accepted_at" class="mb-2 small">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Started At:</strong>
                                        <p id="started_at" class="mb-2 small">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Completed At:</strong>
                                        <p id="completed_at" class="mb-2 small">-</p>
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Created At:</strong>
                                        <p id="created_at_timeline" class="mb-2 small">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Cancellation Information (if applicable) -->
                    <div class="col-12 mb-4" id="cancellation_section">
                        <div class="card border-danger">
                            <div class="card-header bg-danger text-white">
                                <h6 class="mb-0"><i class="fas fa-ban me-2"></i>Cancellation Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Cancelled At:</strong>
                                        <p id="cancelled_at" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Cancelled By:</strong>
                                        <p id="cancelled_by_name" class="mb-2">-</p>
                                    </div>
                                    <div class="col-md-12">
                                        <strong>Reason:</strong>
                                        <p id="cancellation_reason" class="mb-2">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Close
                </button>
                <button type="button" class="btn btn-primary" onclick="printRideDetails()">
                    <i class="fas fa-print me-2"></i>Print
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function printRideDetails() {
    const printContent = document.getElementById('rideShowModal').querySelector('.modal-body').innerHTML;
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Ride Assignment Details</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
            <style>
                body { padding: 20px; }
                .card { margin-bottom: 20px; page-break-inside: avoid; }
                @media print {
                    .card { border: 1px solid #dee2e6 !important; }
                    .bg-primary, .bg-info, .bg-success, .bg-warning, .bg-secondary, .bg-dark, .bg-light, .bg-danger {
                        background-color: #f8f9fa !important;
                        color: #000 !important;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2 class="text-center mb-4">Ride Assignment Details</h2>
                ${printContent}
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>