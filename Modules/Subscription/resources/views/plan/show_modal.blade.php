<!-- Kid & Parent Details Modal -->
<div class="modal fade" id="kidShowModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
  aria-labelledby="kidShowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content border-primary shadow-sm">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="kidShowModalLabel">Kid Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <!-- Kid Basic Info -->
            <h5 class="mb-0">Personal Information (Kid)</h5>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">First Name</label>
                <p id="first_name" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Last Name</label>
                <p id="last_name" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Date of Birth</label>
                <p id="dob" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Gender</label>
                <p id="gender" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Height (cm)</label>
                <p id="height_cm" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Weight (kg)</label>
                <p id="weight_kg" class="form-control-plaintext text-dark"></p>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">School Name</label>
                <p id="school_name" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-semibold">School Address</label>
                <p id="school_address" class="form-control-plaintext text-dark"></p>
              </div>

              <div class="col-md-6">
                <label class="form-label fw-semibold">Emergency Contact</label>
                <p id="emergency_contact" class="form-control-plaintext text-dark"></p>
              </div>

                <div class="col-md-4 mb-3">
                    <label>Photo</label>
                    <div class="image-upload-preview" data-target-input="photo"
                        style="width: 117px; height: 117px;">
                        <img src="/backend/img/default.jpg" alt="Photo Preview" class="preview-img" />
                    </div>
                </div>
            </div>

        <!-- Parent Info -->
            <h5 class="mb-0">Parent Information</h5>
            <div class="row g-3">

              <div class="col-md-4">
                <label class="form-label fw-semibold">First Name</label>
                <p id="parent_first_name" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Last Name</label>
                <p id="parent_last_name" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Email</label>
                <p id="parent_email" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Phone</label>
                <p id="parent_phone" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Date of Birth</label>
                <p id="parent_dob" class="form-control-plaintext text-dark"></p>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Gender</label>
                <p id="parent_gender" class="form-control-plaintext text-dark"></p>
              </div>

               <div class="col-md-4 mb-3">
                    <label>Parent Photo</label>
                    <div class="image-upload-preview" data-target-input="parent_photo"
                        style="width: 117px; height: 117px;">
                        <img src="/backend/img/default.jpg" alt="Parent Photo Preview" class="preview-img" />
                    </div>
                </div>
            </div>

      </div> <!-- modal-body -->
    </div> <!-- modal-content -->
  </div> <!-- modal-dialog -->
</div> <!-- modal -->
