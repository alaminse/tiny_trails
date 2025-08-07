<div class="modal fade" id="userShowModal"data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="userShowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="userShowModalLabel">Show</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>First Name</label>
                        <p id="first_name" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Last Name</label>
                        <p id="last_name" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Email</label>
                        <p id="email" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Phone</label>
                        <p id="phone" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Date of Birth</label>
                        <p id="dob" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Gender</label>
                        <p id="gender" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Height (cm)</label>
                        <p id="height_cm" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Weight (kg)</label>
                        <p id="weight_kg" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Country</label>
                        <p id="country_id" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>State</label>
                        <p id="state_id" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>City</label>
                        <p id="city_id" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Address</label>
                        <p id="address" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <p id="status" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Role</label>
                        <p id="role" class="form-control-plaintext text-dark fw-semibold"></p>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Photo</label>
                        <div class="image-upload-preview" data-target-input="photo"
                            style="width: 117px; height: 117px;">
                            <img src="/backend/img/default.jpg" alt="Photo Preview" class="preview-img" />
                        </div>
                    </div>
                </div>

                <!-- Driver Fields (shown if needed) -->
                <div id="driverFields"
                    style="border: 1px solid #ddd; padding: 10px; margin-top: 20px; border-radius: 5px;">
                    <h4 style="border-bottom: 2px solid blue">Driver Details</h4>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Driving License Number</label>
                            <p id="driving_license_number" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Driving License Expiry</label>
                            <p id="driving_license_expiry" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Model</label>
                            <p id="car_model" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Make</label>
                            <p id="car_make" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Year</label>
                            <p id="car_year" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Color</label>
                            <p id="car_color" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Plate Number</label>
                            <p id="car_plate_number" class="form-control-plaintext text-dark fw-semibold"></p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Driving License Image</label>
                            <div class="image-upload-preview" data-target-input="driving_license_image"
                                style="width: 117px; height: 117px;">
                                <img src="/backend/img/default.jpg" alt="License Preview" class="preview-img" />
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Car Image</label>
                            <div class="image-upload-preview" data-target-input="car_image"
                                style="width: 117px; height: 117px;">
                                <img src="/backend/img/default.jpg" alt="Car Preview" class="preview-img" />
                            </div>
                        </div>
                    </div>
                </div>
                <div id="parentFields"
                    style="border: 1px solid #ddd; padding: 10px; margin-top: 20px; border-radius: 5px; display: none;">
                    <h4 style="border-bottom: 2px solid blue">Kids Details</h4>
                    <div id="kidsContent"></div>   <!-- Dynamic insert -->
                </div>


            </div>


        </div>
    </div>
</div>
