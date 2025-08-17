<div class="modal fade" id="planModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="planModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="planModalLabel">Create / Edit Plan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="planForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="plan_id">

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="pickup_type_id">Pickup Type <span class="text-danger">*</span></label>
                            <select name="pickup_type_id" id="pickup_type_id" class="form-control" required>
                                <option value="">Select Pickup Type</option>
                                @foreach($pickupTypes ?? [] as $pickupType)
                                    <option value="{{ $pickupType->id }}">{{ $pickupType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name">Plan Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" 
                                placeholder="Enter plan description..."></textarea>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="price">Regular Price <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="price" class="form-control" step="0.01" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="sell_price">Selling Price <span class="text-danger">*</span></label>
                            <input type="number" name="sell_price" id="sell_price" class="form-control" step="0.01" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="currency">Currency <span class="text-danger">*</span></label>
                            <input type="text" name="currency" id="currency" class="form-control" value="AUD" maxlength="3" required>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="interval">Billing Interval <span class="text-danger">*</span></label>
                            <select name="interval" id="interval" class="form-control" required>
                                <option value="">Select Interval</option>
                                <option value="day">Daily</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                                <option value="year">Yearly</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="interval_count">Interval Count <span class="text-danger">*</span></label>
                            <input type="number" name="interval_count" id="interval_count" class="form-control" min="1" value="1" required>
                            <small class="text-muted">e.g., 1 = every month, 3 = every 3 months</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="sort_order">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" value="0" min="0">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="features">Features</label>
                            <textarea name="features" id="features" rows="4" class="form-control"
                                placeholder="Enter plan features separated by commas (e.g., Feature 1, Feature 2, Feature 3)"></textarea>
                            <small class="text-muted">Separate each feature with a comma</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient-primary btn-sm p-2">
                        <i class="fas fa-save me-1"></i>Save Plan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Plan Show Modal -->
<div class="modal fade" id="planShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="planShowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-primary shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="planShowModalLabel">Plan Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Plan Name</label>
                                        <p id="name" class="form-control-plaintext text-dark fw-bold"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Slug</label>
                                        <p id="slug" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label fw-semibold">Description</label>
                                        <p id="description" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Pickup Type</label>
                                        <p id="pickup_type_name" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Status</label>
                                        <span id="status_badge"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Pricing Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Regular Price</label>
                                        <p id="price" class="form-control-plaintext text-dark fw-bold text-success"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Selling Price</label>
                                        <p id="sell_price" class="form-control-plaintext text-dark fw-bold text-primary"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Currency</label>
                                        <p id="currency" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold">Billing Cycle</label>
                                        <p id="interval_display" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-star me-2"></i>Plan Features</h6>
                            </div>
                            <div class="card-body">
                                <div id="features_list" class="row"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistics</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Total Subscriptions</label>
                                        <p id="total_subscriptions" class="form-control-plaintext text-dark fw-bold"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Active Subscriptions</label>
                                        <p id="active_subscriptions" class="form-control-plaintext text-success fw-bold"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Sort Order</label>
                                        <p id="sort_order" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Auto generate slug from name
document.getElementById('name').addEventListener('input', function() {
    const name = this.value;
    const slug = name.toLowerCase()
        .replace(/[^\w\s-]/g, '') // Remove special characters
        .replace(/\s+/g, '-')     // Replace spaces with hyphens
        .replace(/-+/g, '-')      // Replace multiple hyphens with single hyphen
        .trim('-');               // Remove leading/trailing hyphens
    
    document.getElementById('slug').value = slug;
});

// Modal open function for additional initialization
function modalOpen() {
    // Any additional modal initialization code can go here
    console.log('Plan modal opened');
}
</script>