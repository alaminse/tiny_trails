function initModuleCrud(config) {
    const {
        moduleName, // e.g. 'role', 'permission'
        tableId,
        modalId,
        formId,
        createBtnId,
        trashedBtnId,
        baseUrl,
        fields = [], // array of field names for dynamic forms
    } = config;

    let currentView = "active";

    const $table = $(`#${tableId}`);
    const $modal = $(`#${modalId}`);
    const $form = $(`#${formId}`);
    const $createBtn = $(`#${createBtnId}`);
    const $trashedBtn = $(`#${trashedBtnId}`);

    // Load Data
    function getData(url = `${baseUrl}/get/data`) {
        $.ajax({
            url,
            method: "GET",
            success: function (response) {
                console.log(response);

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
            getData(`${baseUrl}/get/data?trashed=true`);
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
        $modal.modal("show");
    });

    // Edit Data
    $(document).on("click", `.editBtn`, function (e) {
        e.preventDefault();
        const id = $(this).data("id");

        $.ajax({
            url: `${baseUrl}/edit/${id}`,
            method: "GET",
            success: function (data) {
                console.log(data);

                $modal
                    .find(".modal-title")
                    .text(`Edit ${capitalize(moduleName)}`);
                $form.find('[name="id"]').val(data.id);

                // Dynamically set field values
                fields.forEach((field) => {
                    $form.find(`[name="${field}"]`).val(data[field]);
                });

                $modal.modal("show");
            },
            error: function () {
                toastr.error(`Failed to load ${moduleName} data`);
            },
        });
    });

    // Submit Form (Create/Update)
    $form.on("submit", function (e) {
        e.preventDefault();

        const id = $form.find('[name="id"]').val();
        const url = id ? `${baseUrl}/update/${id}` : `${baseUrl}/store`;
        const method = id ? "PUT" : "POST";

        $.ajax({
            url,
            type: method,
            data: $form.serialize(),
            success: function (response) {
                console.log(response);

                $modal.modal("hide");
                toastr.success(response.message);
                getData(
                    currentView === "trashed"
                        ? `${baseUrl}/get/data?trashed=true`
                        : `${baseUrl}/get/data`
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

    // Delete, Restore, Force Delete — same as before
    // Use `.delete{ModuleName}Btn`, `.restore{ModuleName}Btn`, etc.

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
                            "error"
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
}
