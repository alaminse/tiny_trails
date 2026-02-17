{{-- resources/views/vendor/userrolepermission/components/user_modal.blade.php --}}

<div class="modal fade" id="userModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="userModalLabel">Create / Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="user_id">

                <div class="modal-body">
                    {{-- Personal Information Section --}}
                    <fieldset class="border rounded p-3 mb-4">
                        <legend class="float-none w-auto px-3">Personal Information</legend>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="password">Password (<small class="form-text text-muted">Leave blank to keep
                                        current password</small>)</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dob">Date of Birth</label>
                                <input type="date" name="dob" id="dob" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Contact & Location Section --}}
                    <fieldset class="border rounded p-3 mb-4">
                        <legend class="float-none w-auto px-3">Contact & Location</legend>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="address">Address</label>
                                <textarea name="address" id="address" rows="2" class="form-control"></textarea>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="country_id">Country</label>
                                <select name="country_id" id="country_id" class="form-control" data-selected="">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ ucfirst($country->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="state_id">State</label>
                                <select name="state_id" id="state_id" class="form-control" data-selected="">
                                    <option value="">Select State</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="city_id">City</label>
                                <select name="city_id" id="city_id" class="form-control" data-selected="">
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>

                    {{-- Role-Specific Details Section --}}
                    <div id="roleSpecificFields">
                        {{-- Parent Details --}}
                        <div id="parentFields" class="border rounded p-3 mb-4" style="display: none;">
                            <h5 class="mb-3">Parent Details</h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="height_cm">Height (cm)</label>
                                    <input type="number" name="height_cm" id="height_cm" class="form-control" min="0"
                                        step="any">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="weight_kg">Weight (kg)</label>
                                    <input type="number" name="weight_kg" id="weight_kg" class="form-control" min="0"
                                        step="any">
                                </div>
                            </div>
                        </div>

                        {{-- Driver Details --}}
                        <div id="driverFields" class="border rounded p-3 mb-4" style="display: none;">
                            <h5 class="mb-3">Driver Details</h5>

                            <!-- License Information -->
                            <fieldset class="border rounded p-3 mb-3">
                                <legend class="float-none w-auto px-3">License Information</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="driving_license_number">Driving License Number</label>
                                        <input type="text" name="driving_license_number" id="driving_license_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="licence_card_number">License Card Number</label>
                                        <input type="text" name="licence_card_number" id="licence_card_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="licence_type">License Type</label>
                                        <input type="text" name="licence_type" id="licence_type" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="driving_license_expiry">License Expiry Date</label>
                                        <input type="date" name="driving_license_expiry" id="driving_license_expiry" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label>License Address</label>
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="text" name="licence_address_line_1" id="licence_address_line_1" class="form-control" placeholder="Address Line 1">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" name="licence_address_line_2" id="licence_address_line_2" class="form-control" placeholder="Address Line 2">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <input type="text" name="licence_city" id="licence_city" class="form-control" placeholder="City">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <input type="text" name="licence_state" id="licence_state" class="form-control" placeholder="State">
                                            </div>
                                            <div class="col-md-4 mb-2">
                                                <input type="text" name="licence_postal_code" id="licence_postal_code" class="form-control" placeholder="Postal Code">
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <input type="text" name="licence_country" id="licence_country" class="form-control" placeholder="Country">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="driving_license_image">Driving License Image</label>
                                        <div class="image-upload-preview" data-target-input="driving_license_image" style="width: 150px; height: 100px;">
                                            <img src="{{ asset('backend/img/default.jpg') }}" alt="License Preview" class="preview-img" />
                                            <div class="drag-drop-overlay">Drop image here</div>
                                        </div>
                                        <input type="file" name="driving_license_image" id="driving_license_image" class="image-upload-input" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Vehicle Information -->
                            <fieldset class="border rounded p-3 mb-3">
                                <legend class="float-none w-auto px-3">Vehicle Information</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="car_make">Car Make</label>
                                        <input type="text" name="car_make" id="car_make" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="car_model">Car Model</label>
                                        <input type="text" name="car_model" id="car_model" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="car_year">Car Year</label>
                                        <input type="number" name="car_year" id="car_year" class="form-control" min="1900" max="2100">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="car_color">Car Color</label>
                                        <input type="text" name="car_color" id="car_color" class="form-control">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="car_plate_number">Car Plate Number</label>
                                        <input type="text" name="car_plate_number" id="car_plate_number" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="car_image">Car Image</label>
                                        <div class="image-upload-preview" data-target-input="car_image" style="width: 150px; height: 100px;">
                                            <img src="{{ asset('backend/img/default.jpg') }}" alt="Car Preview" class="preview-img" />
                                            <div class="drag-drop-overlay">Drop image here</div>
                                        </div>
                                        <input type="file" name="car_image" id="car_image" class="image-upload-input" accept="image/*" style="display:none;">
                                    </div>
                                </div>
                            </fieldset>

                            <!-- Compliance & Verification -->
                            <fieldset class="border rounded p-3 mb-3">
                                <legend class="float-none w-auto px-3">Compliance & Verification</legend>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="wwc_card_number">WWC Card Number</label>
                                        <input type="text" name="wwc_card_number" id="wwc_card_number" class="form-control">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="wwc_expiry_date">WWC Expiry Date</label>
                                        <input type="date" name="wwc_expiry_date" id="wwc_expiry_date" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="wwc_card_image">WWC Card Image</label>
                                        <div class="image-upload-preview" data-target-input="wwc_card_image" style="width: 150px; height: 100px;">
                                            <img src="{{ asset('backend/img/default.jpg') }}" alt="WWC Preview" class="preview-img" />
                                            <div class="drag-drop-overlay">Drop image here</div>
                                        </div>
                                        <input type="file" name="wwc_card_image" id="wwc_card_image" class="image-upload-input" accept="image/*" style="display:none;">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="police_clearance_ref">Police Clearance Reference</label>
                                        <input type="text" name="police_clearance_ref" id="police_clearance_ref" class="form-control">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="police_clearance_image">Police Clearance Image</label>
                                        <div class="image-upload-preview" data-target-input="police_clearance_image" style="width: 150px; height: 100px;">
                                            <img src="{{ asset('backend/img/default.jpg') }}" alt="Police Clearance Preview" class="preview-img" />
                                            <div class="drag-drop-overlay">Drop image here</div>
                                        </div>
                                        <input type="file" name="police_clearance_image" id="police_clearance_image" class="image-upload-input" accept="image/*" style="display:none;">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="other_qualifications">Other Qualifications</label>
                                        <textarea name="other_qualifications" id="other_qualifications" rows="3" class="form-control"></textarea>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    {{-- System Settings & Photo Section --}}
                    <fieldset class="border rounded p-3 mb-4">
                        <legend class="float-none w-auto px-3">System Settings & Photo</legend>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status">User Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="role">Role</label>
                                    <select name="role" id="role" class="form-control" required>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ $role->name == $roleName ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="photo">Profile Photo</label>
                                    <div class="image-upload-preview" data-target-input="photo" style="width: 117px; height: 117px;">
                                        <img src="{{ asset('backend/img/default.jpg') }}" alt="Photo Preview" class="preview-img" />
                                        <div class="drag-drop-overlay">Drop image here</div>
                                    </div>
                                    <input type="file" name="photo" id="photo" class="image-upload-input" accept="image/*" style="display:none;">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-gradient-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
