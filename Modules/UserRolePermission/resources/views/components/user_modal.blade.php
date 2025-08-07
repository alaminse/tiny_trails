<div class="modal fade" id="userModal"data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="userModalLabel">Create / Edit {{ $roleName ??  'User' }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="user_id">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
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
                        <div class="col-md-4 mb-3">
                            <label for="country_id">Country</label>
                            <select name="country_id" id="country_id" class="form-control select_option" data-selected="">
                                <option value="">Select Country</option>
                                @foreach ($countries as $country)
                                        <option value="{{ $country->id }}">{{ ucfirst($country->name) }}</option>
                                    @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="state_id">State</label>
                            <select name="state_id" id="state_id" class="form-control select_option" data-selected="">
                                <option value="">Select State</option>
                                {{-- Populate states here --}}
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="city_id">City</label>
                            <select name="city_id" id="city_id" class="form-control select_option" data-selected="">
                                <option value="">Select City</option>
                                {{-- Populate cities here --}}
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" rows="4" class="form-control"></textarea>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="role">Role</label>
                                <select name="role" id="role" class="form-control select2 select2-danger"
                                    data-dropdown-css-class="select2-danger" style="width: 100%;" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="photo">Photo</label>
                            <div class="image-upload-preview" data-target-input="photo"
                                style="width: 117px; height: 117px;">
                                <img src="{{ asset('backend/img/default.jpg') }}" alt="Photo Preview"
                                    class="preview-img" />
                                <div class="drag-drop-overlay">Drop image here</div>
                            </div>
                            <input type="file" name="photo" id="photo" class="image-upload-input"
                                accept="image/*" style="display:none;">
                        </div>
                    </div>

                    <!-- Driver Fields (hidden initially) -->
                    <div id="driverFields"
                        style="display: none; border: 1px solid #ddd; padding: 10px; margin-top: 20px; border-radius: 5px;">
                        <h4 style="border-bottom: 2px solid blue">Driver Details</h4>

                        <div class="row">
                            <div class="col-md-8 row">
                                <div class="col-md-6 mb-3">
                                    <label for="driving_license_number">Driving License Number</label>
                                    <input type="text" name="driving_license_number" id="driving_license_number"
                                        class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="driving_license_expiry">Driving License Expiry</label>
                                    <input type="date" name="driving_license_expiry" id="driving_license_expiry"
                                        class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="car_model">Car Model</label>
                                    <input type="text" name="car_model" id="car_model" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="car_make">Car Make</label>
                                    <input type="text" name="car_make" id="car_make" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="car_year">Car Year</label>
                                    <input type="number" name="car_year" id="car_year" class="form-control"
                                        min="1900" max="2100">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="car_color">Car Color</label>
                                    <input type="text" name="car_color" id="car_color" class="form-control">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="car_plate_number">Car Plate Number</label>
                                    <input type="text" name="car_plate_number" id="car_plate_number"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="driving_license_image">Driving License Image</label>
                                    <div class="image-upload-preview" data-target-input="driving_license_image"
                                        style="width: 117px; height: 117px;">
                                        <img src="{{ asset('backend/img/default.jpg') }}" alt="License Preview"
                                            class="preview-img" />
                                        <div class="drag-drop-overlay">Drop image here</div>
                                    </div>
                                    <input type="file" name="driving_license_image" id="driving_license_image"
                                        class="image-upload-input" accept="image/*" style="display:none;">
                                </div>
                                <div class="mb-3">
                                    <label for="car_image">Car Image</label>
                                    <div class="image-upload-preview" data-target-input="car_image"
                                        style="width: 117px; height: 117px;">
                                        <img src="{{ asset('backend/img/default.jpg') }}" alt="Car Preview"
                                            class="preview-img" />
                                        <div class="drag-drop-overlay">Drop image here</div>
                                    </div>
                                    <input type="file" name="car_image" id="car_image" class="image-upload-input"
                                        accept="image/*" style="display:none;">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-gradient-primary btn-sm p-2">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
