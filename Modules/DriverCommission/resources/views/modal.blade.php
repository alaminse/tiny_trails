<div class="modal fade" id="commissionModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="commissionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="commissionModalLabel">Create / Edit Commission</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="commissionForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="commission_id">

                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label for="driver_id">Driver <span class="text-danger">*</span></label>
                            <select name="driver_id" id="driver_id" class="form-control" required>
                                <option value="">Select Driver</option>
                                @foreach($drivers ?? [] as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }} ({{ $driver->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="commission_type">Commission Type <span class="text-danger">*</span></label>
                            <select name="commission_type" id="commission_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <option value="per_ride">Per Ride</option>
                                <option value="daily_bonus">Daily Bonus</option>
                                <option value="weekly_bonus">Weekly Bonus</option>
                                <option value="monthly_bonus">Monthly Bonus</option>
                                <option value="referral_bonus">Referral Bonus</option>
                                <option value="penalty">Penalty</option>
                            </select>
                        </div>

                        <div class="col-md-12" id="ride_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ride_assignment_id">Ride Assignment</label>
                                    <select name="ride_assignment_id" id="ride_assignment_id" class="form-control">
                                        <option value="">Select Ride</option>
                                        @foreach($rides ?? [] as $ride)
                                            <option value="{{ $ride->id }}">{{ $ride->ride_title }} - {{ $ride->formatted_ride_date }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="base_fare">Base Fare <span class="text-danger">*</span></label>
                                    <input type="number" name="base_fare" id="base_fare" class="form-control" step="0.01" required>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label for="commission_rate">Commission Rate (%)</label>
                                    <input type="number" name="commission_rate" id="commission_rate" class="form-control" step="0.01" min="0" max="100">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="commission_amount">Commission Amount <span class="text-danger">*</span></label>
                            <input type="number" name="commission_amount" id="commission_amount" class="form-control" step="0.01" required>
                        </div>

                        <div class="col-md-4 mb-3" id="bonus_fields" style="display: none;">
                            <label for="bonus_amount">Bonus Amount</label>
                            <input type="number" name="bonus_amount" id="bonus_amount" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="col-md-4 mb-3" id="penalty_fields" style="display: none;">
                            <label for="penalty_amount">Penalty Amount</label>
                            <input type="number" name="penalty_amount" id="penalty_amount" class="form-control" step="0.01" min="0">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="total_earning">Total Earning</label>
                            <input type="number" name="total_earning" id="total_earning" class="form-control" step="0.01" readonly>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="earning_date">Earning Date <span class="text-danger">*</span></label>
                            <input type="date" name="earning_date" id="earning_date" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="payment_status">Payment Status <span class="text-danger">*</span></label>
                            <select name="payment_status" id="payment_status" class="form-control" required>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="paid">Paid</option>
                                <option value="failed">Failed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bonus_type">Bonus Type</label>
                            <input type="text" name="bonus_type" id="bonus_type" class="form-control" placeholder="e.g., completion_bonus, rating_bonus">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="penalty_type">Penalty Type</label>
                            <input type="text" name="penalty_type" id="penalty_type" class="form-control" placeholder="e.g., late_pickup, cancellation">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_method">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value="">Select Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="paypal">PayPal</option>
                                <option value="stripe">Stripe</option>
                                <option value="cash">Cash</option>
                                <option value="check">Check</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="payment_reference">Payment Reference</label>
                            <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Transaction ID or reference">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control" 
                                placeholder="Description or notes about this commission"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-gradient-primary btn-sm p-2">
                        <i class="fas fa-save me-1"></i>Save Commission
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>