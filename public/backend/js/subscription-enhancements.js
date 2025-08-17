// Enhanced JavaScript for Subscription Module
// Add this to your existing scripts or create a new file: subscription-enhancements.js

$(document).ready(function() {
    
    // Enhanced Show Modal Handler for Subscriptions
    $(document).on('click', '.showBtn', function() {
        const id = $(this).data('id');
        const moduleName = 'subscription'; // Can be passed as data attribute
        
        if (window.location.pathname.includes('/subscriptions')) {
            showSubscriptionDetails(id);
        } else if (window.location.pathname.includes('/plans')) {
            showPlanDetails(id);
        }
    });

    // Plan Details Handler
    function showPlanDetails(id) {
        $.ajax({
            url: `/plans/show/${id}`,
            method: 'GET',
            success: function(response) {
                const plan = response.data;
                
                // Populate basic info
                $('#planShowModal #name').text(plan.name || '-');
                $('#planShowModal #slug').text(plan.slug || '-');
                $('#planShowModal #description').text(plan.description || 'No description provided');
                $('#planShowModal #pickup_type_name').text(plan.pickup_type?.name || 'N/A');
                
                // Populate pricing info
                $('#planShowModal #price').text(plan.formatted_price || '-');
                $('#planShowModal #sell_price').text(plan.formatted_sell_price || '-');
                $('#planShowModal #currency').text(plan.currency || '-');
                $('#planShowModal #interval_display').text(plan.interval_display || '-');
                
                // Status badge
                const statusBadge = plan.status === 'active' 
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
                $('#planShowModal #status_badge').html(statusBadge);
                
                // Features
                let featuresHtml = '';
                if (plan.features && plan.features.length > 0) {
                    featuresHtml = '<div class="row">';
                    plan.features.forEach((feature, index) => {
                        featuresHtml += `
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check text-success me-2"></i>
                                    <span>${feature}</span>
                                </div>
                            </div>
                        `;
                    });
                    featuresHtml += '</div>';
                } else {
                    featuresHtml = '<p class="text-muted">No features listed</p>';
                }
                $('#planShowModal #features_list').html(featuresHtml);
                
                // Statistics
                $('#planShowModal #total_subscriptions').text(plan.subscriptions_count || '0');
                $('#planShowModal #active_subscriptions').text(plan.active_subscriptions_count || '0');
                $('#planShowModal #sort_order').text(plan.sort_order || '0');
                
                $('#planShowModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load plan details');
            }
        });
    }

    // Subscription Details Handler
    function showSubscriptionDetails(id) {
        $.ajax({
            url: `/subscriptions/show/${id}`,
            method: 'GET',
            success: function(response) {
                const subscription = response.data;
                
                // User Information
                $('#subscriptionShowModal #user_name').text(subscription.user?.name || 'N/A');
                $('#subscriptionShowModal #user_email').text(subscription.user?.email || 'N/A');
                
                // Plan Information
                $('#subscriptionShowModal #plan_name').text(subscription.plan?.name || 'Plan Deleted');
                $('#subscriptionShowModal #plan_price').text(subscription.plan?.formatted_price || 'N/A');
                $('#subscriptionShowModal #plan_interval').text(subscription.plan?.interval_display || 'N/A');
                
                // Subscription Details
                $('#subscriptionShowModal #subscription_name').text(subscription.name || '-');
                $('#subscriptionShowModal #stripe_subscription_id').text(subscription.stripe_id || 'N/A');
                $('#subscriptionShowModal #created_at').text(subscription.created_at || '-');
                
                // Status badges
                const statusBadge = subscription.status === 'active' 
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-secondary">Inactive</span>';
                $('#subscriptionShowModal #subscription_status_badge').html(statusBadge);
                
                const stripeStatusBadge = getStripeStatusBadge(subscription.stripe_status);
                $('#subscriptionShowModal #stripe_status_badge').html(stripeStatusBadge);
                
                // Payment Information
                if (subscription.card_brand && subscription.card_last_four) {
                    $('#subscriptionShowModal #payment_card_brand').text(subscription.card_brand.toUpperCase());
                    $('#subscriptionShowModal #card_last_four_display').text('••••' + subscription.card_last_four);
                } else {
                    $('#subscriptionShowModal #payment_card_brand').text('N/A');
                    $('#subscriptionShowModal #card_last_four_display').text('N/A');
                }
                $('#subscriptionShowModal #card_expiration_display').text(subscription.card_expiration || 'N/A');
                
                // Important Dates
                $('#subscriptionShowModal #trial_ends_at_display').text(subscription.trial_ends_at_formatted || 'No Trial');
                $('#subscriptionShowModal #ends_at_display').text(subscription.ends_at_formatted || 'No End Date');
                $('#subscriptionShowModal #canceled_at_display').text(subscription.canceled_at_formatted || 'Not Canceled');
                
                // Cancellation reason
                if (subscription.cancellation_reason) {
                    $('#subscriptionShowModal #cancellation_reason_display').text(subscription.cancellation_reason);
                    $('#subscriptionShowModal #cancellation_reason_section').show();
                } else {
                    $('#subscriptionShowModal #cancellation_reason_section').hide();
                }
                
                $('#subscriptionShowModal').modal('show');
            },
            error: function() {
                toastr.error('Failed to load subscription details');
            }
        });
    }

    // Helper function for stripe status badges
    function getStripeStatusBadge(status) {
        const badges = {
            'active': '<span class="badge bg-success">Active</span>',
            'canceled': '<span class="badge bg-danger">Canceled</span>',
            'incomplete': '<span class="badge bg-warning">Incomplete</span>',
            'incomplete_expired': '<span class="badge bg-danger">Incomplete Expired</span>',
            'past_due': '<span class="badge bg-warning">Past Due</span>',
            'trialing': '<span class="badge bg-info">Trialing</span>',
            'unpaid': '<span class="badge bg-danger">Unpaid</span>'
        };
        return badges[status] || `<span class="badge bg-secondary">${status}</span>`;
    }

    // Enhanced form validation
    $('#planForm').on('submit', function(e) {
        // Additional validation for plan form
        const price = parseFloat($('#price').val());
        const sellPrice = parseFloat($('#sell_price').val());
        
        if (sellPrice > price) {
            toastr.warning('Selling price should not be higher than regular price');
            return false;
        }
    });

    $('#subscriptionForm').on('submit', function(e) {
        // Additional validation for subscription form
        const trialEnds = new Date($('#trial_ends_at').val());
        const subscriptionEnds = new Date($('#ends_at').val());
        const now = new Date();
        
        if ($('#trial_ends_at').val() && trialEnds < now) {
            toastr.warning('Trial end date should be in the future');
            return false;
        }
        
        if ($('#trial_ends_at').val() && $('#ends_at').val() && subscriptionEnds < trialEnds) {
            toastr.warning('Subscription end date should be after trial end date');
            return false;
        }
    });

    // Auto-fill subscription name based on user and plan selection
    $('#user_id, #plan_id').on('change', function() {
        const userId = $('#user_id').val();
        const planId = $('#plan_id').val();
        
        if (userId && planId) {
            const userName = $('#user_id option:selected').text().split(' (')[0];
            const planName = $('#plan_id option:selected').text().split(' - ')[0];
            $('#name').val(`${userName} - ${planName}`);
        }
    });

    // Real-time search functionality
    let searchTimeout;
    $('#searchInput').on('keyup', function() {
        const searchTerm = $(this).val();
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (searchTerm.length >= 3 || searchTerm.length === 0) {
                performSearch(searchTerm);
            }
        }, 300);
    });

    function performSearch(term) {
        const currentPath = window.location.pathname;
        let searchUrl = '';
        
        if (currentPath.includes('/plans')) {
            searchUrl = '/plans/search';
        } else if (currentPath.includes('/subscriptions')) {
            searchUrl = '/subscriptions/search';
        }
        
        if (searchUrl) {
            $.ajax({
                url: searchUrl,
                method: 'GET',
                data: { term: term },
                success: function(response) {
                    if (response.success) {
                        updateTableWithSearchResults(response.data);
                    }
                },
                error: function() {
                    toastr.error('Search failed');
                }
            });
        }
    }

    function updateTableWithSearchResults(data) {
        // This would update the DataTable with search results
        // Implementation depends on your specific table structure
        if ($.fn.DataTable.isDataTable('#datatable-responsive')) {
            $('#datatable-responsive').DataTable().clear();
            // Add new data and redraw
            $('#datatable-responsive').DataTable().destroy();
            
            // Generate new HTML for table rows
            let html = '';
            if (window.location.pathname.includes('/plans')) {
                html = generatePlanTableRows(data.data || data);
            } else if (window.location.pathname.includes('/subscriptions')) {
                html = generateSubscriptionTableRows(data.data || data);
            }
            
            $('#datatable-responsive tbody').html(html);
            $('#datatable-responsive').DataTable({ responsive: true });
        }
    }

    function generatePlanTableRows(plans) {
        let html = '';
        plans.forEach((plan, index) => {
            const statusBadge = plan.status === 'active' 
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>';
            
            const pickupTypeBadge = plan.pickup_type 
                ? `<span class="badge bg-info">${plan.pickup_type.name}</span>`
                : '<span class="text-muted">N/A</span>';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div>
                                <strong>${plan.name}</strong><br>
                                <small class="text-muted">${plan.slug}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>
                            ${plan.price !== plan.sell_price ? 
                                `<span class="text-muted text-decoration-line-through">${plan.currency} ${parseFloat(plan.price).toFixed(2)}</span><br>` : ''}
                            <strong class="text-primary">${plan.currency} ${parseFloat(plan.sell_price).toFixed(2)}</strong>
                        </div>
                    </td>
                    <td>${plan.currency}</td>
                    <td><span class="badge">${plan.interval_display}</span></td>
                    <td>${pickupTypeBadge}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-gradient-primary btn-sm editBtn" data-id="${plan.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-gradient-info btn-sm showBtn" data-id="${plan.id}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-gradient-warning btn-sm duplicateBtn" data-id="${plan.id}" title="Duplicate">
                                <i class="fas fa-copy"></i>
                            </button>
                            <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="${plan.id}" title="Move to Trash">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        return html;
    }

    function generateSubscriptionTableRows(subscriptions) {
        let html = '';
        subscriptions.forEach((subscription, index) => {
            const statusBadge = subscription.status === 'active' 
                ? '<span class="badge bg-success">Active</span>'
                : '<span class="badge bg-secondary">Inactive</span>';
            
            const stripeStatusBadge = getStripeStatusBadge(subscription.stripe_status);
            
            const cardInfo = subscription.card_brand && subscription.card_last_four 
                ? `<div><i class="fab fa-cc-${subscription.card_brand.toLowerCase()}"></i> ••••${subscription.card_last_four}</div>`
                : '<span class="text-muted">No Card</span>';

            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>
                        <div>
                            <strong>${subscription.user?.name || 'N/A'}</strong><br>
                            <small class="text-muted">${subscription.user?.email || 'N/A'}</small>
                        </div>
                    </td>
                    <td>
                        ${subscription.plan ? `
                            <div>
                                <strong>${subscription.plan.name}</strong><br>
                                <small class="text-muted">${subscription.plan.formatted_price}/${subscription.plan.interval_display}</small>
                            </div>
                        ` : '<span class="text-muted">Plan Deleted</span>'}
                    </td>
                    <td>${statusBadge}</td>
                    <td>${stripeStatusBadge}</td>
                    <td>${subscription.trial_ends_at_formatted || '<span class="text-muted">No Trial</span>'}</td>
                    <td>${subscription.ends_at_formatted || '<span class="text-muted">No End Date</span>'}</td>
                    <td>${cardInfo}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-gradient-primary btn-sm editBtn" data-id="${subscription.id}" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-gradient-info btn-sm showBtn" data-id="${subscription.id}" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${subscription.is_canceled ? 
                                `<button class="btn btn-gradient-success btn-sm reactivateBtn" data-id="${subscription.id}" title="Reactivate">
                                    <i class="fas fa-play"></i>
                                </button>` : 
                                `<button class="btn btn-gradient-warning btn-sm cancelBtn" data-id="${subscription.id}" title="Cancel">
                                    <i class="fas fa-pause"></i>
                                </button>`
                            }
                            <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="${subscription.id}" title="Move to Trash">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        return html;
    }

    // Bulk actions functionality
    $('#selectAll').on('change', function() {
        $('.row-select').prop('checked', this.checked);
        toggleBulkActions();
    });

    $(document).on('change', '.row-select', function() {
        const totalCheckboxes = $('.row-select').length;
        const checkedCheckboxes = $('.row-select:checked').length;
        
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
        toggleBulkActions();
    });

    function toggleBulkActions() {
        const checkedCount = $('.row-select:checked').length;
        if (checkedCount > 0) {
            $('#bulkActions').show();
            $('#selectedCount').text(checkedCount);
        } else {
            $('#bulkActions').hide();
        }
    }

    // Bulk delete functionality
    $('#bulkDelete').on('click', function() {
        const selectedIds = $('.row-select:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            toastr.warning('Please select items to delete');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: `You are about to delete ${selectedIds.length} item(s).`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                performBulkDelete(selectedIds);
            }
        });
    });

    function performBulkDelete(ids) {
        const currentPath = window.location.pathname;
        let deleteUrl = '';
        
        if (currentPath.includes('/plans')) {
            deleteUrl = '/plans/bulk-delete';
        } else if (currentPath.includes('/subscriptions')) {
            deleteUrl = '/subscriptions/bulk-delete';
        }

        $.ajax({
            url: deleteUrl,
            type: 'POST',
            data: {
                ids: ids,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire('Deleted!', response.message, 'success');
                getData(); // Reload the table
                $('#bulkActions').hide();
                $('#selectAll').prop('checked', false);
            },
            error: function() {
                Swal.fire('Error!', 'Failed to delete selected items.', 'error');
            }
        });
    }

    // Export functionality
    $('#exportBtn').on('click', function() {
        const currentPath = window.location.pathname;
        let exportUrl = '';
        
        if (currentPath.includes('/plans')) {
            exportUrl = '/plans/export';
        } else if (currentPath.includes('/subscriptions')) {
            exportUrl = '/subscriptions/export';
        }

        if (exportUrl) {
            window.open(exportUrl, '_blank');
        }
    });

    // Statistics auto-refresh
    function refreshStats() {
        const currentPath = window.location.pathname;
        let statsUrl = '';
        
        if (currentPath.includes('/plans')) {
            statsUrl = '/plans/stats';
        } else if (currentPath.includes('/subscriptions')) {
            statsUrl = '/subscriptions/stats';
        }

        if (statsUrl) {
            $.get(statsUrl, function(response) {
                if (response.success) {
                    updateStatsDisplay(response.data);
                }
            }).fail(function() {
                console.warn('Failed to refresh statistics');
            });
        }
    }

    function updateStatsDisplay(stats) {
        if (window.location.pathname.includes('/plans')) {
            $('#total-plans').text(stats.total || 0);
            $('#active-plans').text(stats.active || 0);
            $('#inactive-plans').text(stats.inactive || 0);
        } else if (window.location.pathname.includes('/subscriptions')) {
            // Update subscription stats if elements exist
            const elements = {
                'total': stats.subscription_stats?.total,
                'active': stats.subscription_stats?.active,
                'inactive': stats.subscription_stats?.inactive,
                'on_trial': stats.subscription_stats?.on_trial,
                'expired': stats.subscription_stats?.expired,
                'canceled': stats.subscription_stats?.canceled
            };

            Object.keys(elements).forEach(key => {
                const element = $(`#${key}-count`);
                if (element.length && elements[key] !== undefined) {
                    element.text(elements[key]);
                }
            });

            // Update revenue stats
            if (stats.revenue_stats) {
                $('#monthly-revenue').text(' + parseFloat(stats.revenue_stats.monthly_revenue || 0).toFixed(2)');
                $('#yearly-revenue').text(' + parseFloat(stats.revenue_stats.yearly_revenue || 0).toFixed(2)');
                $('#total-active-value').text(' + parseFloat(stats.revenue_stats.total_active_value || 0).toFixed(2)');
            }
        }
    }

    // Auto-refresh stats every 30 seconds
    setInterval(refreshStats, 30000);

    // Notification system for subscription events
    function showNotification(type, title, message) {
        if (typeof toastr !== 'undefined') {
            toastr[type](message, title);
        } else {
            // Fallback to browser notification
            if (Notification.permission === 'granted') {
                new Notification(title, { body: message });
            }
        }
    }

    // Request notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Initialize tooltips and popovers
    $('[data-bs-toggle="tooltip"]').tooltip();
    $('[data-bs-toggle="popover"]').popover();

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + N = New item
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            if (window.location.pathname.includes('/plans')) {
                $('#addPlanBtn').click();
            } else if (window.location.pathname.includes('/subscriptions')) {
                $('#addSubscriptionBtn').click();
            }
        }
        
        // Escape = Close modals
        if (e.key === 'Escape') {
            $('.modal').modal('hide');
        }
    });
});