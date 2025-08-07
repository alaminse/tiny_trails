<div class="modal fade" id="kidModal"data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="kidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border border-primary">
            <div class="modal-header btn-gradient-primary">
                <h5 class="modal-title" id="kidModalLabel">Create / Edit kid </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <form id="kidForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" id="kid_id">

                <div class="modal-body">
                    <div class="row">
                        <select id="user_id" name="user_id" class="form-control">
                            <option value="">Select Parent</option>

                        </select>
                        <div class="col-md-4 mb-3">
                            <label for="first_name">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="last_name">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control" required>
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
                            <label for="school_name">School Name</label>
                            <input type="text" name="school_name" id="school_name" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="school_address">School Address</label>
                            <input type="text" name="school_address" id="school_address" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="emergency_contact">Emergency Contact</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" class="form-control">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="photo">Photo</label>
                            <div class="image-upload-preview" data-target-input="photo"
                                style="width: 117px; height: 117px;">
                                <img src="{{ asset('backend/img/default.jpg') }}" alt="Kid Photo"
                                    class="preview-img" />
                                <div class="drag-drop-overlay">Drop image here</div>
                            </div>
                            <input type="file" name="photo" id="photo" class="image-upload-input"
                                accept="image/*" style="display:none;">
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
