/**
 * Subscription Create Form JavaScript
 * Location: public/js/subscription-create.js
 */

let currentStep = 1;
let formData = {};

function initializeSubscriptionForm() {
    // Initialize form validation and events
    bindFormEvents();
    setupCardFormatting();
    setupAutoFill();

    // Test connection on page load
    setTimeout(() => {
        testPayWayConnection();
    }, 1000);
}

function bindFormEvents() {
    // Form submission
    $('#subscription-form').on('submit', handleFormSubmission);

    // Test connection button
    $('#testConnectionBtn').on('click', testPayWayConnection);

    // Validate form button
    $('#validateFormBtn').on('click', validateAllFields);

    // Auto-fill events
    $('#user_id').on('change', autoFillUserInfo);
    $('#plan_id').on('change', handlePlanSelection);

    // Real-time validation
    $('.form-control, .form-select').on('blur', function() {
        validateField($(this));
    });
}

function setupCardFormatting() {
    // Card number formatting
    $('#card_number').on('input', function(e) {
        let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let matches = value.match(/\d{4,16}/g);
        let match = matches && matches[0] || '';
        let parts = [];

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4));
        }

        if (parts.length) {
            e.target.value = parts.join(' ');
        } else {
            e.target.value = value;
        }

        // Auto-detect card brand
        detectCardBrand(value);
    });

    // CVN number only
    $('#cvn').on('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });

    // Postal code number only
    $('#billing_address_postal_code').on('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
}

function setupAutoFill() {
    // Auto-fill test data for development
    if (window.location.hostname === 'localhost' || window.location.hostname.includes('test')) {
        fillTestData();
    }
}

function fillTestData() {
    // Fill with PayWay test card data
    $('#card_number').val('4564 7100 0000 0004');
    $('#expiry_month').val('02');
    $('#expiry_year').val('2029');
    $('#cvn').val('847');
    $('#cardholder_name').val('Test Card');

    // Fill basic billing info
    $('#billing_name').val('Test User');
    $('#billing_email').val('test@example.com');
    $('#billing_address_street1').val('123 Test Street');
    $('#billing_address_city').val('Sydney');
    $('#billing_address_state').val('NSW');
    $('#billing_address_postal_code').val('2000');
}

function nextStep(step) {
    if (validateCurrentStep()) {
        // Hide current section
        $(`#section-${currentStep}`).hide();

        // Update step indicators
        $(`.step`).removeClass('active');
        $(`#step-${currentStep}`).addClass('completed');
        $(`#step-${step}`).addClass('active');

        // Show next section
        $(`#section-${step}`).show();

        // Update current step
        currentStep = step;

        // Update review if we're on step 4
        if (step === 4) {
            updateReviewSummary();
        }

        // Scroll to top
        $('html, body').animate({ scrollTop: 0 }, 300);
    }
}

function prevStep(step) {
    // Hide current section
    $(`#section-${currentStep}`).hide();

    // Update step indicators
    $(`.step`).removeClass('active');
    $(`#step-${currentStep}`).removeClass('completed');
    $(`#step-${step}`).addClass('active');

    // Show previous section
    $(`#section-${step}`).show();

    // Update current step
    currentStep = step;

    // Scroll to top
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function validateCurrentStep() {
    let isValid = true;
    let fieldsToValidate = [];

    switch(currentStep) {
        case 1:
            fieldsToValidate = ['user_id', 'plan_id', 'name'];
            break;
        case 2:
            fieldsToValidate = ['card_number', 'expiry_month', 'expiry_year', 'cvn', 'cardholder_name'];
            break;
        case 3:
            fieldsToValidate = ['billing_name', 'billing_email', 'billing_address[street1]', 'billing_address[city]', 'billing_address[state]', 'billing_address[postal_code]'];
            break;
    }

    fieldsToValidate.forEach(fieldName => {
        const field = $(`[name="${fieldName}"]`);
        if (!validateField(field)) {
            isValid = false;
        }
    });

    if (!isValid) {
        showAlert('error', 'Please fill in all required fields correctly before proceeding.');
    }

    return isValid;
}

function validateField(field) {
    const fieldName = field.attr('name');
    const value = field.val().trim();
    const isRequired = field.attr('required') !== undefined;

    // Clear previous validation
    field.removeClass('is-invalid');
    field.siblings('.invalid-feedback').text('');

    // Required field validation
    if (isRequired && !value) {
        field.addClass('is-invalid');
        field.siblings('.invalid-feedback').text('This field is required.');
        return false;
    }

    // Specific field validations
    switch(fieldName) {
        case 'card_number':
            if (value && !isValidCardNumber(value.replace(/\s/g, ''))) {
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text('Please enter a valid card number.');
                return false;
            }
            break;

        case 'billing_email':
            if (value && !isValidEmail(value)) {
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text('Please enter a valid email address.');
                return false;
            }
            break;

        case 'cvn':
            if (value && (value.length < 3 || value.length > 4)) {
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text('CVN must be 3-4 digits.');
                return false;
            }
            break;

        case 'billing_address[postal_code]':
            if (value && !/^\d{4}$/.test(value)) {
                field.addClass('is-invalid');
                field.siblings('.invalid-feedback').text('Postal code must be 4 digits.');
                return false;
            }
            break;
    }

    // Field is valid
    field.addClass('is-valid');
    return true;
}

function validateAllFields() {
    let isValid = true;

    // Validate all form fields
    $('.form-control, .form-select').each(function() {
        if (!validateField($(this))) {
            isValid = false;
        }
    });

    if (isValid) {
        showAlert('success', 'All fields are valid! You can now create the subscription.');
    } else {
        showAlert('error', 'Please fix the validation errors before submitting.');
    }

    return isValid;
}

async function handleFormSubmission(e) {
    e.preventDefault();

    if (!validateAllFields()) {
        return;
    }

    const submitBtn = $('#submitBtn');
    const originalText = submitBtn.html();

    // Show loading state
    submitBtn.prop('disabled', true);
    submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i> Creating Subscription...');

    try {
        const formData = new FormData(e.target);

        const response = await fetch('/admin/payway/subscription/create', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            showAlert('success', 'Subscription created successfully!');

            // Show success details
            $('#summary-content').html(`
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle me-2"></i>Subscription Created Successfully!</h6>
                    <p><strong>Subscription ID:</strong> ${result.data.subscription_id}</p>
                    <p><strong>PayWay Customer ID:</strong> ${result.data.payway_customer_id}</p>
                    <p><strong>Verification Status:</strong> ${result.data.verification_status}</p>
                </div>
            `);

            // Redirect after 3 seconds
            setTimeout(() => {
                window.location.href = `/admin/subscriptions`;
            }, 3000);

        } else {
            showAlert('error', result.message);

            // Show validation errors if present
            if (result.errors) {
                displayValidationErrors(result.errors);
            }
        }

    } catch (error) {
        console.error('Subscription creation failed:', error);
        showAlert('error', 'An unexpected error occurred. Please try again.');
    } finally {
        // Reset button
        submitBtn.prop('disabled', false);
        submitBtn.html(originalText);
    }
}

async function testPayWayConnection() {
    const statusDiv = $('#connection-status');
    const testBtn = $('#testConnectionBtn');

    statusDiv.show().html(`
        <div class="connection-status">
            <i class="fas fa-spinner fa-spin me-2"></i>Testing PayWay connection...
        </div>
    `);

    testBtn.prop('disabled', true);

    try {
        const response = await fetch('/admin/payway/test-connection');
        const result = await response.json();

        if (result.success) {
            statusDiv.html(`
                <div class="connection-status connection-success">
                    <i class="fas fa-check-circle me-2"></i>PayWay connection successful!
                    <br><small>Client: ${result.data.clientName || 'Unknown'} (${result.data.clientNumber || 'Unknown'})</small>
                </div>
            `);
        } else {
            statusDiv.html(`
                <div class="connection-status connection-error">
                    <i class="fas fa-exclamation-triangle me-2"></i>PayWay connection failed: ${result.message}
                </div>
            `);
        }
    } catch (error) {
        statusDiv.html(`
            <div class="connection-status connection-error">
                <i class="fas fa-exclamation-triangle me-2"></i>Connection test failed: ${error.message}
            </div>
        `);
    } finally {
        testBtn.prop('disabled', false);
    }
}

function autoFillUserInfo() {
    const userId = $('#user_id').val();
    const selectedOption = $('#user_id option:selected');

    if (userId) {
        const userName = selectedOption.data('name');
        const userEmail = selectedOption.data('email');

        // Auto-fill billing information
        $('#billing_name').val(userName || '');
        $('#billing_email').val(userEmail || '');
        $('#cardholder_name').val(userName || '');

        // Generate subscription name
        if (userName) {
            $('#name').val(`${userName}'s Subscription`);
        }
    }
}

function handlePlanSelection() {
    const planId = $('#plan_id').val();
    const selectedOption = $('#plan_id option:selected');

    if (planId) {
        const planName = selectedOption.data('name');
        const planPrice = selectedOption.data('price');

        // If it's a test plan, auto-fill test data
        if (planName && planName.toLowerCase().includes('test')) {
            fillTestData();
        }

        // Update summary if visible
        if (currentStep === 4) {
            updateReviewSummary();
        }
    }
}

function updateReviewSummary() {
    const userData = {
        name: $('#user_id option:selected').text(),
        id: $('#user_id').val()
    };

    const planData = {
        name: $('#plan_id option:selected').data('name'),
        price: $('#plan_id option:selected').data('price')
    };

    const subscriptionData = {
        name: $('#name').val(),
        trial_days: $('#trial_days').val(),
        status: $('#status').val()
    };

    const cardData = {
        number: $('#card_number').val(),
        expiry: `${$('#expiry_month').val()}/${$('#expiry_year').val()}`,
        holder: $('#cardholder_name').val()
    };

    const billingData = {
        name: $('#billing_name').val(),
        email: $('#billing_email').val(),
        address: `${$('#billing_address_street1').val()}, ${$('#billing_address_city').val()}, ${$('#billing_address_state').val()} ${$('#billing_address_postal_code').val()}`
    };

    $('#summary-content').html(`
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-user me-2"></i>User Information</h6>
                <p><strong>User:</strong> ${userData.name}</p>
                <p><strong>Plan:</strong> ${planData.name} - ${parseFloat(planData.price || 0).toFixed(2)}</p>
                <p><strong>Subscription:</strong> ${subscriptionData.name}</p>
                <p><strong>Trial Days:</strong> ${subscriptionData.trial_days}</p>
                <p><strong>Status:</strong> ${subscriptionData.status}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-credit-card me-2"></i>Payment Information</h6>
                <p><strong>Card:</strong> ****${cardData.number.replace(/\s/g, '').slice(-4)}</p>
                <p><strong>Expiry:</strong> ${cardData.expiry}</p>
                <p><strong>Holder:</strong> ${cardData.holder}</p>
                <p><strong>Billing:</strong> ${billingData.name}</p>
                <p><strong>Email:</strong> ${billingData.email}</p>
                <p><strong>Address:</strong> ${billingData.address}</p>
            </div>
        </div>
    `);
}

function detectCardBrand(cardNumber) {
    const number = cardNumber.replace(/\s/g, '');
    let brand = 'unknown';

    if (/^4/.test(number)) brand = 'visa';
    else if (/^5[1-5]/.test(number)) brand = 'mastercard';
    else if (/^3[47]/.test(number)) brand = 'amex';
    else if (/^6(?:011|5)/.test(number)) brand = 'discover';

    // Update card icon (if you have one)
    updateCardIcon(brand);
}

function updateCardIcon(brand) {
    // Implementation for updating card brand icon
    const iconClass = `fab fa-cc-${brand}`;
    // Update icon in UI if needed
}

function isValidCardNumber(cardNumber) {
    // Luhn algorithm for card validation
    const digits = cardNumber.replace(/\D/g, '');
    let sum = 0;
    let isEven = false;

    for (let i = digits.length - 1; i >= 0; i--) {
        let digit = parseInt(digits.charAt(i), 10);

        if (isEven) {
            digit *= 2;
            if (digit > 9) {
                digit -= 9;
            }
        }

        sum += digit;
        isEven = !isEven;
    }

    return sum % 10 === 0 && digits.length >= 13;
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showAlert(type, message) {
    const alertClass = type === 'error' ? 'alert-danger' : `alert-${type}`;
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);

    $('#alerts-container').prepend(alert);

    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.fadeOut(() => alert.remove());
    }, 5000);

    // Scroll to top to show alert
    $('html, body').animate({ scrollTop: 0 }, 300);
}

function displayValidationErrors(errors) {
    // Clear previous errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    // Display new errors
    Object.keys(errors).forEach(field => {
        const input = $(`[name="${field}"]`);
        if (input.length) {
            input.addClass('is-invalid');
            input.siblings('.invalid-feedback').text(errors[field][0]);
        }
    });

    // Show first error field
    const firstErrorField = $('.is-invalid').first();
    if (firstErrorField.length) {
        // Find which step the error is in
        const section = firstErrorField.closest('.form-section');
        if (section.length) {
            const sectionId = section.attr('id');
            const stepNumber = sectionId.replace('section-', '');

            // Navigate to that step
            if (stepNumber != currentStep) {
                // Reset steps and go to error step
                $('.form-section').hide();
                $('.step').removeClass('active completed');
                $(`#step-${stepNumber}`).addClass('active');
                $(`#section-${stepNumber}`).show();
                currentStep = parseInt(stepNumber);
            }
        }

        // Focus on first error field
        firstErrorField.focus();
    }
}

// Keyboard shortcuts
$(document).on('keydown', function(e) {
    // Alt + N for next step
    if (e.altKey && e.key === 'n' && currentStep < 4) {
        e.preventDefault();
        nextStep(currentStep + 1);
    }

    // Alt + P for previous step
    if (e.altKey && e.key === 'p' && currentStep > 1) {
        e.preventDefault();
        prevStep(currentStep - 1);
    }

    // Alt + T for test connection
    if (e.altKey && e.key === 't') {
        e.preventDefault();
        testPayWayConnection();
    }

    // Alt + V for validate
    if (e.altKey && e.key === 'v' && currentStep === 4) {
        e.preventDefault();
        validateAllFields();
    }
});
