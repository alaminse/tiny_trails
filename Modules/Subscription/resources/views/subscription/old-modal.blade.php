<!-- Subscription Create/Edit Modal -->
<div class="modal fade" id="subscriptionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="subscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="subscriptionModalLabel">Create / Edit Subscription</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="subscriptionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="subscription_id">

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="user_id">User <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value="">Select User</option>
                                @foreach($users ?? [] as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="plan_id">Plan <span class="text-danger">*</span></label>
                            <select name="plan_id" id="plan_id" class="form-control" required>
                                <option value="">Select Plan</option>
                                @foreach($plans ?? [] as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} - {{ $plan->formatted_sell_price }}/{{ $plan->interval_display }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="name">Subscription Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="default" required>
                            <small class="text-muted">Descriptive name for this subscription</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stripe_id">Stripe Subscription ID</label>
                            <input type="text" name="stripe_id" id="stripe_id" class="form-control">
                            <small class="text-muted">Leave empty if not using Stripe</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="stripe_status">Stripe Status <span class="text-danger">*</span></label>
                            <select name="stripe_status" id="stripe_status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="canceled">Canceled</option>
                                <option value="incomplete">Incomplete</option>
                                <option value="incomplete_expired">Incomplete Expired</option>
                                <option value="past_due">Past Due</option>
                                <option value="trialing">Trialing</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="trial_ends_at">Trial Ends At</label>
                            <input type="datetime-local" name="trial_ends_at" id="trial_ends_at" class="form-control">
                            <small class="text-muted">Leave empty if no trial period</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="ends_at">Subscription Ends At</label>
                            <input type="datetime-local" name="ends_at" id="ends_at" class="form-control">
                            <small class="text-muted">Leave empty for no end date</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="canceled_at">Canceled At</label>
                            <input type="datetime-local" name="canceled_at" id="canceled_at" class="form-control">
                            <small class="text-muted">Leave empty if not canceled</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="card_brand">Card Brand</label>
                            <select name="card_brand" id="card_brand" class="form-control">
                                <option value="">Select Card Brand</option>
                                <option value="visa">Visa</option>
                                <option value="mastercard">Mastercard</option>
                                <option value="amex">American Express</option>
                                <option value="discover">Discover</option>
                                <option value="diners">Diners Club</option>
                                <option value="jcb">JCB</option>
                                <option value="unionpay">UnionPay</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="card_last_four">Card Last Four Digits</label>
                            <input type="text" name="card_last_four" id="card_last_four" class="form-control" maxlength="4" pattern="[0-9]{4}">
                            <small class="text-muted">Enter only the last 4 digits</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="card_expiration">Card Expiration</label>
                            <input type="text" name="card_expiration" id="card_expiration" class="form-control" placeholder="MM/YY">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="cancellation_reason">Cancellation Reason</label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="3" class="form-control" 
                                placeholder="Enter reason for cancellation (if applicable)"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient-primary btn-sm p-2">
                        <i class="fas fa-save me-1"></i>Save Subscription
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Subscription Show Modal -->
<div class="modal fade" id="subscriptionShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="subscriptionShowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-primary shadow-sm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="subscriptionShowModalLabel">Subscription Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">
                    <!-- User Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>User Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Name</label>
                                        <p id="user_name" class="form-control-plaintext text-dark fw-bold"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Email</label>
                                        <p id="user_email" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Plan Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Plan Name</label>
                                        <p id="plan_name" class="form-control-plaintext text-dark fw-bold"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Price</label>
                                        <p id="plan_price" class="form-control-plaintext text-success fw-bold"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Billing Cycle</label>
                                        <p id="plan_interval" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Details -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Subscription Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Subscription Name</label>
                                        <p id="subscription_name" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Status</label>
                                        <div id="subscription_status_badge"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Stripe Status</label>
                                        <div id="stripe_status_badge"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Stripe ID</label>
                                        <p id="stripe_subscription_id" class="form-control-plaintext text-dark font-monospace"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Created At</label>
                                        <p id="created_at" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Card Brand</label>
                                        <p id="payment_card_brand" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Last Four Digits</label>
                                        <p id="card_last_four_display" class="form-control-plaintext text-dark font-monospace"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Expiration</label>
                                        <p id="card_expiration_display" class="form-control-plaintext text-dark"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dates & Status -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calendar me-2"></i>Important Dates</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Trial Ends At</label>
                                        <p id="trial_ends_at_display" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Subscription Ends At</label>
                                        <p id="ends_at_display" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Canceled At</label>
                                        <p id="canceled_at_display" class="form-control-plaintext text-dark"></p>
                                    </div>
                                    <div class="col-md-12" id="cancellation_reason_section" style="display: none;">
                                        <label class="form-label fw-semibold">Cancellation Reason</label>
                                        <p id="cancellation_reason_display" class="form-control-plaintext text-dark"></p>
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
// Format card expiration input
document.getElementById('card_expiration').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Format card last four input
document.getElementById('card_last_four').addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>