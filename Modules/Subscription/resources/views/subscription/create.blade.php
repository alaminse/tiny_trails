{{-- resources/views/subscription/create.blade.php --}}
@extends('backend.app')
@section('title', 'Create New Subscription')
@section('css')
    <style>
        .payment-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .test-info {
            background: #e3f2fd;
            border: 1px solid #2196f3;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        .form-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        .connection-status {
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .connection-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .connection-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .step {
            flex: 1;
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            margin: 0 5px;
            background: #e9ecef;
            color: #6c757d;
        }
        .step.active {
            background: #007bff;
            color: white;
        }
        .step.completed {
            background: #28a745;
            color: white;
        }

        /* Map Styles */
        .map-container {
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #ddd;
            margin-bottom: 20px;
        }
        .location-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .location-marker {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        .pickup-marker {
            background-color: #28a745;
        }
        .dropoff-marker {
            background-color: #dc3545;
        }
        .location-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .location-button {
            flex: 1;
            min-width: 200px;
        }
        .location-button.active {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
    </style>
@endsection

@section('content')
    @include('backend.includes.header', ['mainTitle' => 'Subscriptions', 'subTitle' => 'Create New Subscription with PayWay'])

    <div class="app-content">
        <div class="container-fluid">

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step-1">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <div>User & Plan</div>
                </div>
                <div class="step" id="step-2">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                    <div>Locations</div>
                </div>
                <div class="step" id="step-3">
                    <i class="fas fa-credit-card fa-2x"></i>
                    <div>Payment Info</div>
                </div>
                <div class="step" id="step-4">
                    <i class="fas fa-address-card fa-2x"></i>
                    <div>Billing Address</div>
                </div>
                <div class="step" id="step-5">
                    <i class="fas fa-check-circle fa-2x"></i>
                    <div>Review & Create</div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-plus-circle me-2"></i>Create New Subscription
                            </h4>
                            <div class="card-tools">
                                <button type="button" class="btn btn-info btn-sm" id="testConnectionBtn">
                                    <i class="fas fa-wifi me-1"></i>Test PayWay Connection
                                </button>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Connection Status -->
                            <div id="connection-status" style="display: none;"></div>

                            <!-- Alerts Container -->
                            <div id="alerts-container"></div>

                            <!-- PayWay Test Info -->
                            <div class="test-info">
                                <h6><i class="fas fa-info-circle me-2"></i>PayWay Test Mode</h6>
                                <p class="mb-2"><strong>Test Card:</strong> <code>4564710000000004</code></p>
                                <p class="mb-2"><strong>CVN:</strong> <code>847</code></p>
                                <p class="mb-0"><strong>Expiry:</strong> <code>02/29</code></p>
                            </div>

                            <form id="subscription-form" method="POST" action="/admin/payway/subscription/create">
                                @csrf

                                <!-- Step 1: User & Plan Selection -->
                                <div class="form-section" id="section-1">
                                    <h5 class="mb-3"><i class="fas fa-users me-2"></i>User & Plan Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="user_id" class="form-label">Select User <span class="text-danger">*</span></label>
                                                <select name="user_id" id="user_id" class="form-select" required>
                                                    <option value="">Choose User...</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" data-name="{{ $user->name }}" data-email="{{ $user->email }}">
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="plan_id" class="form-label">Select Plan <span class="text-danger">*</span></label>
                                                <select name="plan_id" id="plan_id" class="form-select" required>
                                                    <option value="">Choose Plan...</option>
                                                    @foreach($plans as $plan)
                                                        <option value="{{ $plan->id }}"
                                                                data-price="{{ $plan->sell_price > 0 ? $plan->sell_price : $plan->price }}"
                                                                data-name="{{ $plan->name }}">
                                                            {{ $plan->name }} - ${{ number_format($plan->sell_price > 0 ? $plan->sell_price : $plan->price, 2) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Subscription Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control" value="Default Subscription" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="trial_days" class="form-label">Trial Days</label>
                                                <input type="number" name="trial_days" id="trial_days" class="form-control" min="0" max="365" value="0">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select name="status" id="status" class="form-select" required>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-end">
                                        <button type="button" class="btn btn-primary" onclick="nextStep(2)">
                                            Next: Locations <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 2: Location Selection -->
                                <div class="form-section" id="section-2" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2"></i>Location Selection</h5>

                                    <!-- Location Selection Buttons -->
                                    <div class="location-buttons">
                                        <button type="button" class="btn btn-success location-button" id="selectPickupBtn">
                                            <i class="fas fa-map-pin me-2"></i>Select Pickup Location
                                        </button>
                                        <button type="button" class="btn btn-danger location-button" id="selectDropoffBtn">
                                            <i class="fas fa-map-pin me-2"></i>Select Drop-off Location
                                        </button>
                                        <button type="button" class="btn btn-info location-button" id="useCurrentLocationBtn">
                                            <i class="fas fa-crosshairs me-2"></i>Use Current Location
                                        </button>
                                    </div>

                                    <!-- Map Container -->
                                    <div class="map-container">
                                        <div id="map" style="height: 100%; width: 100%;">
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <div class="text-center">
                                                    <div class="spinner-border text-primary mb-3" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mb-0">Loading Google Maps...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Location Information Display -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="location-info" id="pickup-info" style="display: none;">
                                                <h6><span class="location-marker pickup-marker"></span>Pickup Location</h6>
                                                <p id="pickup-address" class="mb-1"></p>
                                                <small class="text-muted" id="pickup-coords"></small>
                                                <input type="hidden" name="pickup_location[address]" id="pickup_address_hidden">
                                                <input type="hidden" name="pickup_location[latitude]" id="pickup_latitude">
                                                <input type="hidden" name="pickup_location[longitude]" id="pickup_longitude">
                                                <input type="hidden" name="pickup_location[street1]" id="pickup_street1">
                                                <input type="hidden" name="pickup_location[city]" id="pickup_city">
                                                <input type="hidden" name="pickup_location[state]" id="pickup_state">
                                                <input type="hidden" name="pickup_location[postal_code]" id="pickup_postal_code">
                                                <input type="hidden" name="pickup_location[country_code]" id="pickup_country_code" value="AU">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="location-info" id="dropoff-info" style="display: none;">
                                                <h6><span class="location-marker dropoff-marker"></span>Drop-off Location</h6>
                                                <p id="dropoff-address" class="mb-1"></p>
                                                <small class="text-muted" id="dropoff-coords"></small>
                                                <input type="hidden" name="dropoff_location[address]" id="dropoff_address_hidden">
                                                <input type="hidden" name="dropoff_location[latitude]" id="dropoff_latitude">
                                                <input type="hidden" name="dropoff_location[longitude]" id="dropoff_longitude">
                                                <input type="hidden" name="dropoff_location[street1]" id="dropoff_street1">
                                                <input type="hidden" name="dropoff_location[city]" id="dropoff_city">
                                                <input type="hidden" name="dropoff_location[state]" id="dropoff_state">
                                                <input type="hidden" name="dropoff_location[postal_code]" id="dropoff_postal_code">
                                                <input type="hidden" name="dropoff_location[country_code]" id="dropoff_country_code" value="AU">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Selection Status -->
                                    <div class="alert alert-info" id="location-instruction">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span id="location-instruction-text">Click "Select Pickup Location" and then click on the map to set pickup point.</span>
                                    </div>

                                    <!-- Address Search -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="location_search" class="form-label">Search Location (Optional)</label>
                                                <div class="input-group">
                                                    <input type="text" id="location_search" class="form-control" placeholder="Type address, suburb, or landmark...">
                                                    <button type="button" class="btn btn-outline-primary" id="searchLocationBtn">
                                                        <i class="fas fa-search"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">You can search for a location and then fine-tune by clicking on the map.</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Navigation Buttons -->
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(1)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="nextToPaymentBtn" onclick="nextStep(3)" disabled>
                                            Next: Payment Info <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 3: Payment Information -->
                                <div class="form-section" id="section-3" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Information</h5>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="card_number" class="form-label">Card Number <span class="text-danger">*</span></label>
                                                <input type="text" name="card_number" id="card_number" class="form-control"
                                                       placeholder="4564 7100 0000 0004" required maxlength="19" pattern="[0-9\s]{13,19}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="expiry_month" class="form-label">Month <span class="text-danger">*</span></label>
                                                <select name="expiry_month" id="expiry_month" class="form-select" required>
                                                    <option value="">MM</option>
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">
                                                            {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="expiry_year" class="form-label">Year <span class="text-danger">*</span></label>
                                                <select name="expiry_year" id="expiry_year" class="form-select" required>
                                                    <option value="">YYYY</option>
                                                    @for($i = date('Y'); $i <= date('Y') + 20; $i++)
                                                        <option value="{{ $i }}">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="cvn" class="form-label">CVN <span class="text-danger">*</span></label>
                                                <input type="text" name="cvn" id="cvn" class="form-control"
                                                       placeholder="847" required maxlength="4" pattern="[0-9]{3,4}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="cardholder_name" class="form-label">Cardholder Name <span class="text-danger">*</span></label>
                                                <input type="text" name="cardholder_name" id="cardholder_name" class="form-control"
                                                       placeholder="Test Card" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(2)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(4)">
                                            Next: Billing Address <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 4: Billing Information -->
                                <div class="form-section" id="section-4" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-address-card me-2"></i>Billing Information</h5>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="billing_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_name" id="billing_name" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="billing_email" class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="billing_email" id="billing_email" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                                <input type="tel" name="billing_phone" id="billing_phone" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_street1" class="form-label">Street Address <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[street1]" id="billing_address_street1" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_street2" class="form-label">Street Address 2 (Optional)</label>
                                                <input type="text" name="billing_address[street2]" id="billing_address_street2" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_city" class="form-label">City <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[city]" id="billing_address_city" class="form-control" required>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_state" class="form-label">State <span class="text-danger">*</span></label>
                                                <select name="billing_address[state]" id="billing_address_state" class="form-select" required>
                                                    <option value="">Choose State...</option>
                                                    <option value="NSW">New South Wales</option>
                                                    <option value="VIC">Victoria</option>
                                                    <option value="QLD">Queensland</option>
                                                    <option value="WA">Western Australia</option>
                                                    <option value="SA">South Australia</option>
                                                    <option value="TAS">Tasmania</option>
                                                    <option value="NT">Northern Territory</option>
                                                    <option value="ACT">Australian Capital Territory</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="billing_address_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                                <input type="text" name="billing_address[postal_code]" id="billing_address_postal_code"
                                                       class="form-control" required pattern="[0-9]{4}" maxlength="4">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="billing_address_country_code" class="form-label">Country <span class="text-danger">*</span></label>
                                                <select name="billing_address[country_code]" id="billing_address_country_code" class="form-select" required>
                                                    <option value="AU" selected>Australia</option>
                                                    <option value="NZ">New Zealand</option>
                                                    <option value="US">United States</option>
                                                    <option value="GB">United Kingdom</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(3)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep(5)">
                                            Review & Create <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Step 5: Review & Submit -->
                                <div class="form-section" id="section-5" style="display: none;">
                                    <h5 class="mb-3"><i class="fas fa-check-circle me-2"></i>Review & Create Subscription</h5>

                                    <div id="review-summary" class="payment-card">
                                        <h6>Subscription Summary</h6>
                                        <div id="summary-content">
                                            <!-- Will be populated by JavaScript -->
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-secondary" onclick="prevStep(4)">
                                            <i class="fas fa-arrow-left me-1"></i> Previous
                                        </button>
                                        <div>
                                            <button type="button" class="btn btn-info me-2" id="validateFormBtn">
                                                <i class="fas fa-check me-1"></i> Validate Form
                                            </button>
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-credit-card me-1"></i> Create Subscription
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Enhanced Google Maps Integration with Complete Error Handling -->
<script>
    // Configuration - Secure API key loading
    const GOOGLE_MAPS_CONFIG = {
        apiKey: "{{ env('GOOGLE_MAPS_API_KEY', '') }}",
        libraries: ['places', 'geometry'],
        region: 'AU',
        version: 'weekly'
    };

    let map;
    let pickupMarker;
    let dropoffMarker;
    let selectingMode = null; // 'pickup' or 'dropoff'
    let geocoder;
    let placesService;
    let autocompleteService;
    let mapInitialized = false;
    let mapLoadAttempted = false;

    // Load Google Maps dynamically with proper error handling
    function loadGoogleMaps() {
        if (mapLoadAttempted) return;
        mapLoadAttempted = true;

        if (!GOOGLE_MAPS_CONFIG.apiKey) {
            showMapError('Google Maps API key not configured. Please add GOOGLE_MAPS_API_KEY to your .env file.', 'CONFIG_ERROR');
            return;
        }

        // Set up global error handlers before loading
        window.gm_authFailure = function() {
            showMapError('Google Maps authentication failed. Please check your API key and billing configuration.', 'AUTH_ERROR');
        };

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_CONFIG.apiKey}&libraries=${GOOGLE_MAPS_CONFIG.libraries.join(',')}&callback=initMap&region=${GOOGLE_MAPS_CONFIG.region}&v=${GOOGLE_MAPS_CONFIG.version}&loading=async`;
        script.async = true;
        script.defer = true;
        script.onerror = function() {
            showMapError('Failed to load Google Maps script. Please check your internet connection and API configuration.', 'SCRIPT_LOAD_ERROR');
        };
        document.head.appendChild(script);

        // Timeout for map loading
        setTimeout(() => {
            if (!mapInitialized) {
                showMapError('Google Maps took too long to load. Please refresh the page and try again.', 'TIMEOUT_ERROR');
            }
        }, 15000);
    }

    // Enhanced error handling with specific solutions
    function showMapError(message, errorType = 'UNKNOWN_ERROR') {
        console.error('Google Maps Error:', errorType, message);

        let troubleshootingSteps = '';
        let actionButton = '';

        switch(errorType) {
            case 'BILLING_ERROR':
                troubleshootingSteps = `
                    <div class="mt-3">
                        <h6 class="text-warning">⚠️ Billing Required</h6>
                        <div class="alert alert-warning small">
                            <strong>Google Maps requires billing to be enabled:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Go to <a href="https://console.cloud.google.com/billing" target="_blank">Google Cloud Console - Billing</a></li>
                                <li>Enable billing (Google provides $200/month free credit)</li>
                                <li>Refresh this page after enabling billing</li>
                            </ol>
                        </div>
                    </div>
                `;
                break;
            case 'API_NOT_ACTIVATED':
                troubleshootingSteps = `
                    <div class="mt-3">
                        <h6 class="text-info">🔧 APIs Required</h6>
                        <div class="alert alert-info small">
                            <strong>Enable these APIs in Google Cloud Console:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Maps JavaScript API</li>
                                <li>Places API</li>
                                <li>Geocoding API</li>
                            </ul>
                            <a href="https://console.cloud.google.com/apis/library" target="_blank" class="btn btn-sm btn-info mt-2">Open API Library</a>
                        </div>
                    </div>
                `;
                break;
            case 'AUTH_ERROR':
            case 'CONFIG_ERROR':
                troubleshootingSteps = `
                    <div class="mt-3">
                        <div class="alert alert-danger small">
                            <strong>🔑 Configuration Issue:</strong><br>
                            Please check your .env file and ensure GOOGLE_MAPS_API_KEY is set correctly.
                        </div>
                    </div>
                `;
                break;
            default:
                actionButton = `
                    <button class="btn btn-outline-primary btn-sm me-2" onclick="retryMapLoad()">
                        <i class="fas fa-redo me-1"></i>Retry Loading Maps
                    </button>
                `;
        }

        const mapContainer = document.getElementById('map');
        mapContainer.innerHTML = `
            <div class="map-error d-flex align-items-center justify-content-center h-100">
                <div class="text-center p-4" style="max-width: 500px;">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3 text-warning"></i>
                    <h5>Maps Unavailable</h5>
                    <p class="mb-2">${message}</p>
                    ${troubleshootingSteps}
                    <div class="mt-3">
                        ${actionButton}
                        <div class="mt-2">
                            <small class="text-muted">You can still enter locations manually below.</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.getElementById('manual-location-entry').style.display = 'block';
        updateInstruction('Maps unavailable. Please use manual location entry below.');
    }

    // Retry map loading function
    function retryMapLoad() {
        mapLoadAttempted = false;
        mapInitialized = false;
        document.getElementById('manual-location-entry').style.display = 'none';

        const mapContainer = document.getElementById('map');
        mapContainer.innerHTML = `
            <div class="d-flex align-items-center justify-content-center h-100" id="map-loading">
                <div class="text-center">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Retrying...</span>
                    </div>
                    <p class="mb-0">Retrying Google Maps...</p>
                </div>
            </div>
        `;

        updateInstruction('Retrying to load Google Maps...');
        loadGoogleMaps();
    }

    // Enhanced manual location setting with better Australian parsing
    function setManualLocation(type) {
        const address = document.getElementById(`manual_${type}_address`).value.trim();
        if (!address) {
            alert('Please enter an address first.');
            return;
        }

        const locationData = parseManualAddress(address);
        updateLocationDisplay(type, locationData);
        document.getElementById(`manual_${type}_address`).value = '';

        // Update instructions and check completion
        const hasPickup = document.getElementById('pickup_latitude').value !== '';
        const hasDropoff = document.getElementById('dropoff_latitude').value !== '';

        if (hasPickup && hasDropoff) {
            updateInstruction('✅ Both locations set manually! You can now proceed to the next step.');
        } else {
            const nextType = type === 'pickup' ? 'drop-off' : 'pickup';
            updateInstruction(`${type.charAt(0).toUpperCase() + type.slice(1)} location set. Now enter the ${nextType} location.`);
        }

        checkLocationCompletion();
    }

    // Enhanced Australian address parsing
    function parseManualAddress(address) {
        const parts = address.split(',').map(s => s.trim());

        // Extract Australian postcode (4 digits)
        const postcodeMatch = address.match(/\b(\d{4})\b/);
        const postcode = postcodeMatch ? postcodeMatch[1] : '2000';

        // Australian state/territory mapping with major cities
        const stateMap = {
            'nsw': 'NSW', 'new south wales': 'NSW', 'sydney': 'NSW', 'newcastle': 'NSW', 'wollongong': 'NSW',
            'vic': 'VIC', 'victoria': 'VIC', 'melbourne': 'VIC', 'geelong': 'VIC', 'ballarat': 'VIC',
            'qld': 'QLD', 'queensland': 'QLD', 'brisbane': 'QLD', 'gold coast': 'QLD', 'cairns': 'QLD',
            'wa': 'WA', 'western australia': 'WA', 'perth': 'WA', 'fremantle': 'WA',
            'sa': 'SA', 'south australia': 'SA', 'adelaide': 'SA',
            'tas': 'TAS', 'tasmania': 'TAS', 'hobart': 'TAS', 'launceston': 'TAS',
            'nt': 'NT', 'northern territory': 'NT', 'darwin': 'NT', 'alice springs': 'NT',
            'act': 'ACT', 'australian capital territory': 'ACT', 'canberra': 'ACT'
        };

        let state = 'NSW'; // Default
        let city = 'Unknown';

        // Smart state detection
        for (const part of parts) {
            const lower = part.toLowerCase().trim();
            if (stateMap[lower]) {
                state = stateMap[lower];
                break;
            }
            // Check for partial matches in city names
            for (const [key, value] of Object.entries(stateMap)) {
                if (lower.includes(key) && key.length > 3) { // Avoid matching short abbreviations
                    state = value;
                    city = part; // The part that contains the city name
                    break;
                }
            }
        }

        // Extract city/suburb (usually second part or before postcode)
        if (parts.length > 1) {
            city = parts[1].replace(/\d{4}/, '').trim();
        } else {
            // Single string - extract city before postcode
            const cityMatch = address.match(/,?\s*([A-Za-z\s]+?)\s*,?\s*\d{4}/);
            if (cityMatch) {
                city = cityMatch[1].trim();
            } else {
                city = parts[0].replace(/\d+.*$/, '').trim(); // Remove numbers from end
            }
        }

        return {
            address: address,
            latitude: 0, // Will be geocoded on backend if needed
            longitude: 0,
            street1: parts[0] || address,
            city: city || 'Unknown',
            state: state,
            postal_code: postcode,
            country_code: 'AU'
        };
    }

    // Initialize Google Map with comprehensive error handling
    function initMap() {
        try {
            const defaultLocation = { lat: -33.8688, lng: 151.2093 }; // Sydney CBD

            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: defaultLocation,
                mapTypeControl: true,
                streetViewControl: false,
                fullscreenControl: false,
                gestureHandling: 'cooperative',
                styles: [
                    {
                        featureType: "poi",
                        elementType: "labels",
                        stylers: [{ visibility: "on" }]
                    }
                ]
            });

            geocoder = new google.maps.Geocoder();

            // Initialize Places services with error handling
            try {
                if (google.maps.places.PlacesService) {
                    placesService = new google.maps.places.PlacesService(map);
                }
                if (google.maps.places.AutocompleteService) {
                    autocompleteService = new google.maps.places.AutocompleteService();
                }
            } catch (placesError) {
                console.warn('Places service initialization failed:', placesError);
            }

            mapInitialized = true;

            // Hide loading indicator
            const loading = document.getElementById('map-loading');
            if (loading) loading.style.display = 'none';

            // Map click handler
            map.addListener('click', function(event) {
                if (selectingMode) {
                    setLocationMarker(event.latLng, selectingMode);
                }
            });

            // Initialize location services
            initLocationServices();

            console.log('🗺️ Google Maps initialized successfully');
            updateInstruction('✅ Google Maps loaded! Click "Select Pickup Location" to start selecting locations on the map.');

        } catch (error) {
            console.error('Error initializing Google Maps:', error);

            // Handle specific error types
            if (error.message && error.message.toLowerCase().includes('billing')) {
                showMapError('Google Maps billing is not enabled. Billing must be enabled even for development.', 'BILLING_ERROR');
            } else if (error.message && error.message.toLowerCase().includes('apinotactivated')) {
                showMapError('Required Google Maps APIs are not enabled in Google Cloud Console.', 'API_NOT_ACTIVATED');
            } else {
                showMapError(`Failed to initialize Google Maps: ${error.message}`, 'INIT_ERROR');
            }
        }
    }

    // Initialize location-related event handlers
    function initLocationServices() {
        // Pickup location button
        document.getElementById('selectPickupBtn').addEventListener('click', function() {
            if (!mapInitialized) {
                alert('Map is not ready yet. Please wait for it to load or use manual entry below.');
                return;
            }
            selectingMode = 'pickup';
            updateInstruction('📍 Click anywhere on the map to select pickup location');
            this.classList.add('active');
            document.getElementById('selectDropoffBtn').classList.remove('active');
        });

        // Dropoff location button
        document.getElementById('selectDropoffBtn').addEventListener('click', function() {
            if (!mapInitialized) {
                alert('Map is not ready yet. Please wait for it to load or use manual entry below.');
                return;
            }
            selectingMode = 'dropoff';
            updateInstruction('📍 Click anywhere on the map to select drop-off location');
            this.classList.add('active');
            document.getElementById('selectPickupBtn').classList.remove('active');
        });

        // Current location button
        document.getElementById('useCurrentLocationBtn').addEventListener('click', function() {
            getCurrentLocation();
        });

        // Search location
        document.getElementById('searchLocationBtn').addEventListener('click', function() {
            searchLocation();
        });

        // Enter key for search
        document.getElementById('location_search').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchLocation();
            }
        });
    }

    // Enhanced location search with modern Places API
    function searchLocation() {
        const query = document.getElementById('location_search').value.trim();
        if (!query) {
            alert('Please enter a location to search for.');
            return;
        }

        // Try modern autocomplete service first
        if (autocompleteService && mapInitialized) {
            const request = {
                input: query,
                componentRestrictions: { country: 'AU' },
                types: ['establishment', 'geocode']
            };

            autocompleteService.getPlacePredictions(request, function(predictions, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK && predictions && predictions.length > 0) {
                    const prediction = predictions[0];

                    if (placesService) {
                        placesService.getDetails({
                            placeId: prediction.place_id,
                            fields: ['geometry', 'formatted_address', 'name']
                        }, function(place, status) {
                            if (status === google.maps.places.PlacesServiceStatus.OK && place.geometry) {
                                map.setCenter(place.geometry.location);
                                map.setZoom(15);
                                document.getElementById('location_search').value = '';
                                updateInstruction('🔍 Location found! Now click "Select Pickup" or "Select Drop-off" and click on the map.');
                            } else {
                                fallbackTextSearch(query);
                            }
                        });
                    } else {
                        fallbackTextSearch(query);
                    }
                } else {
                    fallbackTextSearch(query);
                }
            });
        } else {
            fallbackTextSearch(query);
        }
    }

    // Fallback search method for older API or when modern API fails
    function fallbackTextSearch(query) {
        if (!placesService || !mapInitialized) {
            alert('Search service not available. Please use manual location entry below.');
            return;
        }

        const request = {
            query: query + ', Australia',
            fields: ['name', 'geometry', 'formatted_address'],
        };

        placesService.textSearch(request, function(results, status) {
            if (status === google.maps.places.PlacesServiceStatus.OK && results[0]) {
                const place = results[0];
                map.setCenter(place.geometry.location);
                map.setZoom(15);
                document.getElementById('location_search').value = '';
                updateInstruction('🔍 Location found! Now click "Select Pickup" or "Select Drop-off" and click on the map.');
            } else {
                alert('Location not found. Please try a different search term or use manual entry below.');
            }
        });
    }

    // Set location marker on map
    function setLocationMarker(latLng, type) {
        if (!geocoder) {
            alert('Geocoding service not available. Please try again.');
            return;
        }

        // Show loading state
        updateInstruction('🔄 Getting address information...');

        geocoder.geocode({ location: latLng }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const address = results[0].formatted_address;
                const addressComponents = results[0].address_components;
                const locationData = parseAddressComponents(addressComponents, address, latLng);

                if (type === 'pickup') {
                    if (pickupMarker) pickupMarker.setMap(null);
                    pickupMarker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: 'Pickup Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        }
                    });
                    updateLocationDisplay('pickup', locationData);
                } else if (type === 'dropoff') {
                    if (dropoffMarker) dropoffMarker.setMap(null);
                    dropoffMarker = new google.maps.Marker({
                        position: latLng,
                        map: map,
                        title: 'Drop-off Location',
                        icon: {
                            url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
                        }
                    });
                    updateLocationDisplay('dropoff', locationData);
                }

                // Reset selection mode
                selectingMode = null;
                document.getElementById('selectPickupBtn').classList.remove('active');
                document.getElementById('selectDropoffBtn').classList.remove('active');

                const hasPickup = document.getElementById('pickup_latitude').value !== '';
                const hasDropoff = document.getElementById('dropoff_latitude').value !== '';

                if (hasPickup && hasDropoff) {
                    updateInstruction('✅ Both locations selected! You can now proceed to the next step.');
                } else {
                    const nextType = type === 'pickup' ? 'drop-off' : 'pickup';
                    updateInstruction(`✅ ${type.charAt(0).toUpperCase() + type.slice(1)} location selected! Now select the ${nextType} location.`);
                }

                checkLocationCompletion();
            } else {
                alert('Unable to get address for this location. Please try another location or use manual entry.');
                selectingMode = null;
                document.getElementById('selectPickupBtn').classList.remove('active');
                document.getElementById('selectDropoffBtn').classList.remove('active');
            }
        });
    }

    // Parse address components from Google Maps API
    function parseAddressComponents(components, fullAddress, latLng) {
        let street1 = '';
        let city = '';
        let state = '';
        let postalCode = '';
        let countryCode = 'AU';

        components.forEach(component => {
            const types = component.types;
            if (types.includes('street_number') || types.includes('route')) {
                street1 += component.long_name + ' ';
            } else if (types.includes('locality') || types.includes('sublocality')) {
                city = component.long_name;
            } else if (types.includes('administrative_area_level_1')) {
                state = component.short_name;
            } else if (types.includes('postal_code')) {
                postalCode = component.long_name;
            } else if (types.includes('country')) {
                countryCode = component.short_name;
            }
        });

        return {
            address: fullAddress,
            latitude: latLng.lat(),
            longitude: latLng.lng(),
            street1: street1.trim() || fullAddress.split(',')[0],
            city: city || 'Unknown',
            state: state || 'NSW',
            postal_code: postalCode || '2000',
            country_code: countryCode
        };
    }

    // Update location display in UI
    function updateLocationDisplay(type, locationData) {
        const prefix = type === 'pickup' ? 'pickup' : 'dropoff';

        document.getElementById(`${prefix}-info`).style.display = 'block';
        document.getElementById(`${prefix}-address`).textContent = locationData.address;

        if (locationData.latitude !== 0 && locationData.longitude !== 0) {
            document.getElementById(`${prefix}-coords`).textContent =
                `${locationData.latitude.toFixed(6)}, ${locationData.longitude.toFixed(6)}`;
        } else {
            document.getElementById(`${prefix}-coords`).textContent = 'Manually entered';
        }

        // Set all hidden form fields
        document.getElementById(`${prefix}_address_hidden`).value = locationData.address;
        document.getElementById(`${prefix}_latitude`).value = locationData.latitude;
        document.getElementById(`${prefix}_longitude`).value = locationData.longitude;
        document.getElementById(`${prefix}_street1`).value = locationData.street1;
        document.getElementById(`${prefix}_city`).value = locationData.city;
        document.getElementById(`${prefix}_state`).value = locationData.state;
        document.getElementById(`${prefix}_postal_code`).value = locationData.postal_code;
        document.getElementById(`${prefix}_country_code`).value = locationData.country_code;

        console.log(`${type} location set:`, locationData);
    }

    // Get current location using browser geolocation
    function getCurrentLocation() {
        if (navigator.geolocation) {
            updateInstruction('🔄 Getting your current location...');

            navigator.geolocation.getCurrentPosition(function(position) {
                const latLng = new google.maps.LatLng(
                    position.coords.latitude,
                    position.coords.longitude
                );

                if (map && mapInitialized) {
                    map.setCenter(latLng);
                    map.setZoom(15);
                    updateInstruction('📍 Current location found! Now click "Select Pickup" or "Select Drop-off" and then click on the map.');
                } else {
                    updateInstruction('Current location found, but map not ready. Please use manual entry below.');
                }
            }, function(error) {
                let errorMsg = 'Unable to get your location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        errorMsg += 'Location access denied by user.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMsg += 'Location information unavailable.';
                        break;
                    case error.TIMEOUT:
                        errorMsg += 'Location request timed out.';
                        break;
                    default:
                        errorMsg += 'An unknown error occurred.';
                        break;
                }
                alert(errorMsg);
                updateInstruction('Unable to get current location. Please select locations manually.');
            });
        } else {
            alert('Geolocation is not supported by this browser.');
            updateInstruction('Geolocation not supported. Please select locations manually.');
        }
    }

    // Update instruction text with better formatting
    function updateInstruction(text) {
        const instructionElement = document.getElementById('location-instruction-text');
        if (instructionElement) {
            instructionElement.textContent = text;
        }
    }

    // Check if both locations are selected and enable next button
    function checkLocationCompletion() {
        const hasPickup = document.getElementById('pickup_latitude').value !== '';
        const hasDropoff = document.getElementById('dropoff_latitude').value !== '';
        const nextBtn = document.getElementById('nextToPaymentBtn');

        if (hasPickup && hasDropoff) {
            nextBtn.disabled = false;
            nextBtn.classList.remove('btn-secondary');
            nextBtn.classList.add('btn-primary');
            nextBtn.innerHTML = 'Next: Payment Info <i class="fas fa-arrow-right ms-1"></i>';
        } else {
            nextBtn.disabled = true;
            nextBtn.classList.remove('btn-primary');
            nextBtn.classList.add('btn-secondary');
            nextBtn.innerHTML = 'Select Both Locations First <i class="fas fa-arrow-right ms-1"></i>';
        }
    }

    // Enhanced step navigation with validation
    function nextStep(step) {
        // Validate current step
        if (!validateCurrentStep(getCurrentStep())) {
            return;
        }

        // Hide all sections
        document.querySelectorAll('.form-section').forEach(section => {
            section.style.display = 'none';
        });

        // Show target section
        document.getElementById('section-' + step).style.display = 'block';

        // Update step indicators
        document.querySelectorAll('.step').forEach(s => {
            s.classList.remove('active');
            s.classList.remove('completed');
        });

        // Mark previous steps as completed
        for (let i = 1; i < step; i++) {
            document.getElementById('step-' + i).classList.add('completed');
        }

        // Mark current step as active
        document.getElementById('step-' + step).classList.add('active');

        // Special handling for specific steps
        if (step === 2 && !mapLoadAttempted) {
            // Load Google Maps when reaching location step
            setTimeout(() => loadGoogleMaps(), 500); // Small delay for UI update
        } else if (step === 5) {
            // Populate review summary
            populateReviewSummary();
        }

        // Smooth scroll to top
        document.querySelector('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function prevStep(step) {
        nextStep(step);
    }

    // Get current active step number
    function getCurrentStep() {
        const activeStep = document.querySelector('.step.active');
        return activeStep ? parseInt(activeStep.id.split('-')[1]) : 1;
    }

    // Validate current step before allowing navigation
    function validateCurrentStep(step) {
        switch(step) {
            case 1:
                if (!document.getElementById('user_id').value) {
                    alert('Please select a user before proceeding.');
                    document.getElementById('user_id').focus();
                    return false;
                }
                if (!document.getElementById('plan_id').value) {
                    alert('Please select a subscription plan before proceeding.');
                    document.getElementById('plan_id').focus();
                    return false;
                }
                if (!document.getElementById('name').value.trim()) {
                    alert('Please enter a subscription name.');
                    document.getElementById('name').focus();
                    return false;
                }
                return true;

            case 2:
                const hasPickup = document.getElementById('pickup_latitude').value !== '';
                const hasDropoff = document.getElementById('dropoff_latitude').value !== '';
                if (!hasPickup || !hasDropoff) {
                    alert('Please select both pickup and drop-off locations before proceeding.');
                    return false;
                }
                return true;

            case 3:
                // Validate payment fields
                const paymentFields = ['card_number', 'expiry_month', 'expiry_year', 'cvn', 'cardholder_name'];
                for (const field of paymentFields) {
                    if (!document.getElementById(field).value.trim()) {
                        alert(`Please enter ${field.replace('_', ' ')} before proceeding.`);
                        document.getElementById(field).focus();
                        return false;
                    }
                }
                return true;

            case 4:
                // Validate billing fields
                const billingFields = ['billing_name', 'billing_email', 'billing_phone', 'billing_address_street1', 'billing_address_city', 'billing_address_postal_code'];
                for (const field of billingFields) {
                    if (!document.getElementById(field).value.trim()) {
                        alert(`Please enter ${field.replace('billing_', '').replace('_', ' ')} before proceeding.`);
                        document.getElementById(field).focus();
                        return false;
                    }
                }
                if (!document.getElementById('billing_address_state').value) {
                    alert('Please select billing state before proceeding.');
                    document.getElementById('billing_address_state').focus();
                    return false;
                }
                return true;

            default:
                return true;
        }
    }

    // Enhanced review summary with better formatting
    function populateReviewSummary() {
        const userSelect = document.getElementById('user_id');
        const planSelect = document.getElementById('plan_id');
        const pickupAddress = document.getElementById('pickup_address_hidden').value;
        const dropoffAddress = document.getElementById('dropoff_address_hidden').value;
        const cardNumber = document.getElementById('card_number').value;
        const billingName = document.getElementById('billing_name').value;

        const userName = userSelect.selectedOptions[0]?.textContent || 'Not selected';
        const planName = planSelect.selectedOptions[0]?.textContent || 'Not selected';
        const subscriptionName = document.getElementById('name').value;
        const trialDays = document.getElementById('trial_days').value;

        // Mask card number for security (show only last 4 digits)
        const maskedCard = cardNumber.replace(/\d(?=\d{4})/g, "*");

        let summary = `
            <div class="row">
                <div class="col-md-6">
                    <strong>User:</strong> ${userName}<br>
                    <strong>Plan:</strong> ${planName}<br>
                    <strong>Subscription:</strong> ${subscriptionName}<br>
                    <strong>Trial Days:</strong> ${trialDays} days<br>
                    <strong>Status:</strong> ${document.getElementById('status').value}
                </div>
                <div class="col-md-6">
                    <strong>Pickup:</strong> ${pickupAddress || 'Not set'}<br>
                    <strong>Drop-off:</strong> ${dropoffAddress || 'Not set'}<br>
                    <strong>Card:</strong> ${maskedCard}<br>
                    <strong>Billing:</strong> ${billingName}
                </div>
            </div>
        `;

        document.getElementById('summary-content').innerHTML = summary;
    }

    // Global error handler for Google Maps API errors
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('Google Maps')) {
            if (e.message.includes('BillingNotEnabledMapError')) {
                showMapError('Billing is not enabled for Google Maps. Please enable billing in Google Cloud Console.', 'BILLING_ERROR');
            } else if (e.message.includes('ApiNotActivatedMapError')) {
                showMapError('Required Google Maps APIs are not enabled. Please enable Maps JavaScript API and Places API.', 'API_NOT_ACTIVATED');
            } else if (e.message.includes('InvalidKeyMapError')) {
                showMapError('Invalid Google Maps API key. Please check your API key configuration.', 'AUTH_ERROR');
            } else if (e.message.includes('RefererNotAllowedMapError')) {
                showMapError('Domain not allowed for this API key. Please add your domain to API key restrictions.', 'AUTH_ERROR');
            }
        }
    });

    // Enhanced form validation function
    function validateForm() {
        let isValid = true;
        let errors = [];

        // Check all required fields
        const requiredFields = [
            { id: 'user_id', name: 'User' },
            { id: 'plan_id', name: 'Plan' },
            { id: 'name', name: 'Subscription Name' },
            { id: 'pickup_latitude', name: 'Pickup Location' },
            { id: 'dropoff_latitude', name: 'Drop-off Location' },
            { id: 'card_number', name: 'Card Number' },
            { id: 'expiry_month', name: 'Expiry Month' },
            { id: 'expiry_year', name: 'Expiry Year' },
            { id: 'cvn', name: 'CVN' },
            { id: 'cardholder_name', name: 'Cardholder Name' },
            { id: 'billing_name', name: 'Billing Name' },
            { id: 'billing_email', name: 'Billing Email' },
            { id: 'billing_phone', name: 'Billing Phone' },
            { id: 'billing_address_street1', name: 'Street Address' },
            { id: 'billing_address_city', name: 'City' },
            { id: 'billing_address_state', name: 'State' },
            { id: 'billing_address_postal_code', name: 'Postal Code' }
        ];

        for (const field of requiredFields) {
            const element = document.getElementById(field.id);
            if (!element || !element.value.trim()) {
                errors.push(`Missing: ${field.name}`);
                isValid = false;
            }
        }

        // Validate email format
        const email = document.getElementById('billing_email').value;
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push('Invalid email format');
            isValid = false;
        }

        // Validate Australian postal code
        const postcode = document.getElementById('billing_address_postal_code').value;
        if (postcode && !/^\d{4}$/.test(postcode)) {
            errors.push('Invalid postal code (must be 4 digits)');
            isValid = false;
        }

        // Display results
        if (!isValid) {
            alert('Form validation failed:\n\n' + errors.join('\n'));
        } else {
            alert('Form validation successful! All required fields are completed correctly.');
        }

        return isValid;
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Google Maps subscription form initialized');

        // Auto-populate billing details when user is selected
        document.getElementById('user_id').addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            if (selectedOption && selectedOption.dataset.name) {
                document.getElementById('billing_name').value = selectedOption.dataset.name;
                document.getElementById('billing_email').value = selectedOption.dataset.email;
                document.getElementById('cardholder_name').value = selectedOption.dataset.name;

                // Update subscription name
                const planOption = document.getElementById('plan_id').selectedOptions[0];
                if (planOption && planOption.dataset.name) {
                    document.getElementById('name').value = `${selectedOption.dataset.name} - ${planOption.dataset.name}`;
                }
            }
        });

        // Auto-update subscription name when plan changes
        document.getElementById('plan_id').addEventListener('change', function() {
            const selectedOption = this.selectedOptions[0];
            const userOption = document.getElementById('user_id').selectedOptions[0];

            if (selectedOption && userOption && selectedOption.dataset.name && userOption.dataset.name) {
                document.getElementById('name').value = `${userOption.dataset.name} - ${selectedOption.dataset.name}`;
            }
        });

        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedInputValue = value.match(/.{1,4}/g)?.join(' ');
            if (formattedInputValue && formattedInputValue.length <= 19) {
                e.target.value = formattedInputValue;
            } else {
                e.target.value = value;
            }
        });

        // CVN input validation
        document.getElementById('cvn').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 4);
        });

        // Postal code validation
        document.getElementById('billing_address_postal_code').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '').slice(0, 4);
        });

        // Validate form button
        const validateBtn = document.getElementById('validateFormBtn');
        if (validateBtn) {
            validateBtn.addEventListener('click', function() {
                validateForm();
            });
        }

        // Initialize external subscription management if available
        if (typeof initializeSubscriptionForm === 'function') {
            try {
                initializeSubscriptionForm();
                console.log('External subscription management initialized');
            } catch (error) {
                console.warn('External subscription management failed to initialize:', error);
            }
        }
    });

    // Development helper functions
    window.debugMaps = function() {
        console.log('Map Debug Info:');
        console.log('- Map Initialized:', mapInitialized);
        console.log('- Map Load Attempted:', mapLoadAttempted);
        console.log('- API Key Set:', !!GOOGLE_MAPS_CONFIG.apiKey);
        console.log('- Pickup Location:', document.getElementById('pickup_latitude').value);
        console.log('- Dropoff Location:', document.getElementById('dropoff_latitude').value);
        console.log('- Current Step:', getCurrentStep());
    };

    // Expose functions globally for external scripts
    window.nextStep = nextStep;
    window.prevStep = prevStep;
    window.setManualLocation = setManualLocation;
    window.retryMapLoad = retryMapLoad;
</script>

<!-- External subscription management script (if exists) -->
<script>
    // Check if external script exists and initialize
    document.addEventListener('DOMContentLoaded', function() {
        // Try to load external subscription management
        const externalScript = document.querySelector('script[src*="subscription-management.js"]');
        if (externalScript) {
            console.log('External subscription management script detected');
        }

        // Initialize jQuery-dependent code if jQuery is available
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                console.log('jQuery available - enhanced form features loaded');

                // Add any jQuery-specific enhancements here
                $('.form-select, .form-control').on('focus', function() {
                    $(this).removeClass('is-invalid');
                });
            });
        }
    });
</script>
@endpush
