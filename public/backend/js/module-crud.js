// module-crud.js
function initModuleCrud(config) {
    const {
        moduleName, // e.g. 'role', 'permission'
        routeRole,
        parentId,
        tableId,
        modalId,
        userShowModal,
        formId,
        createBtnId,
        trashedBtnId,
        baseUrl,
        fields = [], // array of field names for dynamic forms
    } = config;

    let currentView = "active";

    const $table = $(`#${tableId}`);
    const $modal = $(`#${modalId}`);
    const $showModal = $(`#${userShowModal}`);
    const $form = $(`#${formId}`);
    const $createBtn = $(`#${createBtnId}`);
    const $trashedBtn = $(`#${trashedBtnId}`);

    // Load Data
    function getData(url = `${baseUrl}/get/data`) {
        let finalUrl = url;

        if (routeRole) {
            finalUrl += `?role=${routeRole}`;
        }
        if (parentId) {
            finalUrl += `?parent=${parentId}`;
        }

        console.log("finalUrl", finalUrl);

        $.ajax({
            url: finalUrl,
            method: "GET",
            success: function (response) {
                if ($.fn.DataTable.isDataTable(`#${tableId}`)) {
                    $table.DataTable().destroy();
                }
                $table.find("tbody").html(response.html);
                $table.DataTable({ responsive: true });
            },
            error: function (xhr) {
                console.error(`Error fetching ${moduleName} data`, xhr);
            },
        });
    }

    // Trashed Toggle
    $trashedBtn.on("click", function (e) {
        e.preventDefault();
        if (currentView === "active") {
            getData(`${baseUrl}/get/data?trashed=true&role=${routeRole}`);
            currentView = "trashed";
            $(this).text("Back to Active");
        } else {
            getData();
            currentView = "active";
            $(this).text("Trashed");
        }
    });

    // Show Create Modal
    $createBtn.on("click", function (e) {
        e.preventDefault();

        $modal.find(".modal-title").text(`Create ${capitalize(moduleName)}`);
        $form[0].reset();
        $form.find('[name="id"]').val("");

        $modal.find("select").val(null).trigger("change");
        $modal
            .find(".image-upload-preview img")
            .attr("src", `backend/img/default.jpg`);
        $modal.find('[name="id"]').val("");
        $modal.find(".error-message, .success-message").hide();

        $modal.modal("show");
    });

    // Edit Data
    $(document).on("click", `.editBtn`, function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        console.log(id);

        $.ajax({
            url: `${baseUrl}/edit/${id}`,
            method: "GET",
            success: function (data) {
                console.log(data);

                $modal
                    .find(".modal-title")
                    .text(`Edit ${capitalize(moduleName)}`);
                $form.find('[name="id"]').val(data.id);

                fields.forEach((field) => {
                    const value = data[field];

                    // Handle image previews if any matching image preview exists
                    if (
                        $form.find(
                            `.image-upload-preview[data-target-input="${field}"] img`,
                        ).length > 0
                    ) {
                        const imageUrl = value || "/backend/img/default.jpg";
                        $form
                            .find(
                                `.image-upload-preview[data-target-input="${field}"] img`,
                            )
                            .attr("src", imageUrl);
                        return;
                    }

                    // Default case for input, select, textarea, etc.
                    $form
                        .find(`[name="${field}"]`)
                        .val(value)
                        .trigger("change");

                    $form
                        .find(`.select_option[name="${field}"]`)
                        .each(function () {
                            $(this)
                                .attr("data-selected", value)
                                .val(value)
                                .trigger("change");

                            // Save selected values globally for cascading dropdowns
                            const capitalizedField =
                                field.charAt(0).toUpperCase() + field.slice(1);
                            window[`selected${capitalizedField}`] = value;
                        });
                });

                // Now run the cascading loads using the globals you set above
                if (window.selectedCountry_id) {
                    window.loadStates(window.selectedCountry_id, function () {
                        $("#state_id")
                            .val(window.selectedState_id)
                            .trigger("change");
                        if (window.selectedState_id) {
                            window.loadCities(
                                window.selectedState_id,
                                function () {
                                    $("#city_id")
                                        .val(window.selectedCity_id)
                                        .trigger("change");
                                },
                            );
                        }
                    });
                }

                $modal.modal("show");
            },
            error: function () {
                toastr.error(`Failed to load ${moduleName} data`);
            },
        });
    });

    //show Btn
    $(document).on("click", `.showBtn`, function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $.ajax({
            url: `${baseUrl}/show/${id}`,
            method: "GET",
            success: function (data) {
                console.log(data);
                const formatDate = (dateString) => {
                    if (!dateString) return "N/A";
                    const dateValue = new Date(dateString);
                    return isNaN(dateValue.getTime())
                        ? "N/A"
                        : dateValue.toLocaleDateString();
                };

                fields.forEach((field) => {
                    let value = data[field] ?? "";

                    console.log(field);
                    console.log(value);

                    if (field === "dob" || field === "created_at") {
                        value = formatDate(value);
                    }

                    if (
                        $showModal.find(
                            `.image-upload-preview[data-target-input="${field}"] img`,
                        ).length > 0
                    ) {
                        const imageUrl = value || "/backend/img/default.jpg";
                        $showModal
                            .find(
                                `.image-upload-preview[data-target-input="${field}"] img`,
                            )
                            .attr("src", imageUrl);
                        return;
                    }

                    const emergencyContacts = data.emergency_contacts;

                    // 2. Check if the array exists and is not empty
                    if (emergencyContacts && emergencyContacts.length > 0) {
                        let contactsHtml = ''; // Start with an empty string to build the HTML

                        // 3. Loop through each contact object in the array
                        emergencyContacts.forEach(contact => {
                            // 4. For each contact, create an HTML block and add it to our string
                            contactsHtml += `
                                <div class="border rounded p-3 mb-2">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Name</label>
                                            <p class="form-control-plaintext text-dark">${contact.name || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Relationship</label>
                                            <p class="form-control-plaintext text-dark">${contact.relationship || 'N/A'}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold">Phone</label>
                                            <p class="form-control-plaintext text-dark">${contact.phone || 'N/A'}</p>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        // 5. Put the generated HTML into the container div
                        $showModal.find('#emergency_contacts_container').html(contactsHtml);

                    } else {
                        // 6. If there are no contacts, show a message
                        $showModal.find('#emergency_contacts_container').html('<p class="text-muted">No emergency contacts listed.</p>');
                    }
                    // 3. Populate Parent Information (NEW BLOCK)
                    const parentData = data.parent;
                    if (parentData) {
                        // Populate simple parent text fields
                        $showModal.find("#parent_first_name").text(parentData.first_name || "N/A");
                        $showModal.find("#parent_last_name").text(parentData.last_name || "N/A");
                        $showModal.find("#parent_email").text(parentData.email || "N/A");
                        $showModal.find("#parent_phone").text(parentData.phone || "N/A");
                        $showModal.find("#parent_gender").text(parentData.gender || "N/A");

                        // Format and populate parent DOB
                        $showModal.find("#parent_dob").text(formatDate(parentData.dob));

                        // Handle Parent Photo
                        const parentImageUrl = parentData.photo || "/backend/img/default.jpg";
                        $showModal
                            .find('.image-upload-preview[data-target-input="parent_photo"] img')
                            .attr("src", parentImageUrl);

                        // Construct and display the full parent address
                        const fullParentAddress = [
                            parentData.address,
                            parentData.city,
                            parentData.state,
                            parentData.country,
                        ]
                            .filter(Boolean) // Removes any empty or null values
                            .join(", ");
                        $showModal.find("#parent_address").text(fullParentAddress || "N/A");
                    }

                    // Regular text display
                    $showModal.find(`#${field}`).text(value);
                });

                const urlName = (baseUrl || "").trim().toLowerCase();

                // Show driverFields only if role is 'driver'
                if ((data.role || "").trim().toLowerCase() === "driver") {
                    $showModal.find("#driverFields").show();
                } else {
                    $showModal.find("#driverFields").hide();
                }

                if ((data.role || "").trim().toLowerCase() === "parent") {
                    $showModal.find("#driverFields").hide();
                    $showModal.find("#parentFields").show();
                    let kidsHtml = `
                            <div class="accordion mt-3" id="kidsAccordion">
                        `;

                    data.kids.forEach((kid, index) => {
                        const collapseId = `collapseKid${index}`;
                        const headingId = `headingKid${index}`;

                        kidsHtml += `
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="${headingId}">
                                        <button class="accordion-button ${index !== 0 ? "collapsed" : ""}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="true" aria-controls="${collapseId}">
                                            Kid ${index + 1}: ${kid.first_name || ""} ${kid.last_name || ""}
                                        </button>
                                    </h2>
                                    <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? "show" : ""}" aria-labelledby="${headingId}" data-bs-parent="#kidsAccordion">
                                        <div class="accordion-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3"><label>First Name</label><p class="form-control-plaintext text-dark fw-semibold">${kid.first_name || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>Last Name</label><p class="form-control-plaintext text-dark fw-semibold">${kid.last_name || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>DOB</label><p class="form-control-plaintext text-dark fw-semibold">${kid.dob || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>Gender</label><p class="form-control-plaintext text-dark fw-semibold">${kid.gender || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>Height (cm)</label><p class="form-control-plaintext text-dark fw-semibold">${kid.height_cm || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>Weight (kg)</label><p class="form-control-plaintext text-dark fw-semibold">${kid.weight_kg || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>School Name</label><p class="form-control-plaintext text-dark fw-semibold">${kid.school_name || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>School Address</label><p class="form-control-plaintext text-dark fw-semibold">${kid.school_address || "-"}</p></div>
                                                <div class="col-md-6 mb-3"><label>Emergency Contact</label><p class="form-control-plaintext text-dark fw-semibold">${kid.emergency_contact || "-"}</p></div>
                                                <div class="col-md-6 mb-3">
                                                    <label>Photo</label>
                                                    <div class="image-upload-preview" style="width: 117px; height: 117px;">
                                                        <img src="${kid.photo || "/backend/img/default.jpg"}" alt="Kid Photo" class="preview-img" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                    });

                    kidsHtml += `</div>`; // Close accordion

                    $("#kidsContent").html(kidsHtml);
                    $showModal.find("#parentFields").show();
                }

                // populateKidModal(data);
                $showModal.modal("show");
            },
            error: function () {
                toastr.error(`Failed to load ${moduleName} data`);
            },
        });
    });

    // Submit
    $form.on("submit", function (e) {
        e.preventDefault();

        const id = $form.find('[name="id"]').val();
        const isEdit = !!id;
        const url = isEdit ? `${baseUrl}/update/${id}` : `${baseUrl}/store`;
        const method = isEdit ? "PUT" : "POST";

        const form = $form[0];
        const formData = new FormData(form);

        // Force Laravel to recognize PUT via hidden field
        if (isEdit) {
            formData.append("_method", "PUT");
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log(response);

                $modal.modal("hide");
                toastr.success(response.message);
                getData(
                    currentView === "trashed"
                        ? `${baseUrl}/get/data?trashed=true`
                        : `${baseUrl}/get/data`,
                );
            },
            error: function (xhr) {
                console.log(xhr);

                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    // Clear previous error messages
                    $form.find(".text-danger").remove();

                    // Loop through each error and show under the input
                    for (let field in errors) {
                        const input = $form.find(`[name="${field}"]`);

                        // Append each error message for this field
                        errors[field].forEach((msg) => {
                            const errorMsg = `<small class="text-danger d-block">${msg}</small>`;
                            input.after(errorMsg);
                        });
                    }
                } else {
                    toastr.error(`Error saving ${moduleName}`);
                }
            },
        });
    });

    // Delete Role
    $(document).on("click", ".deleteBtn", function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "You can restore it later from trash.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/delete/${id}`,
                    type: "POST", // use POST with method override
                    data: {
                        _method: "DELETE",
                    },
                    success: function (response) {
                        Swal.fire("Deleted!", response.message, "success");
                        getData(`${baseUrl}/get/data`);
                    },
                    error: function () {
                        Swal.fire(
                            "Error!",
                            "Failed to delete the role.",
                            "error",
                        );
                    },
                });
            }
        });
    });

    // Restore Role
    $(document).on("click", ".restoreBtn", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "You want to restore.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, Restore it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/restore/${id}`,
                    method: "POST",
                    success: function (response) {
                        Swal.fire({
                            icon: "success",
                            title: "Restored!",
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        getData(`${baseUrl}/get/data?trashed=true`);
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Failed",
                            text: "Failed to restore role.",
                        });
                    },
                });
            }
        });
    });

    // Force Delete Role
    $(document).on("click", ".forceDeleteBtn", function () {
        const id = $(this).data("id");

        Swal.fire({
            title: "Are you sure?",
            text: "This will permanently delete the role. This action cannot be undone!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${baseUrl}/force-delete/${id}`,
                    type: "POST", // use POST with _method override
                    data: {
                        _method: "DELETE",
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: "success",
                            title: "Deleted!",
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        getData(`${baseUrl}/get/data?trashed=true`);
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Failed to delete permanently.",
                        });
                    },
                });
            }
        });
    });

    getData(); // Load initial data

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // Return the getData function so it can be called externally
    return {
        getData: getData,
        currentView: currentView,
    };
}
