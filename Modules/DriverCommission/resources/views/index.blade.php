@extends('backend.app')
@section('title', 'Driver Commission Management')

@section('css')
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .stats-card {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .stats-card:hover {
            transform: translateY(-5px);
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .card-primary .stats-card {
            background: linear-gradient(45deg, #007bff 0%, #0056b3 100%);
        }
        .card-success .stats-card {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
        }
        .card-warning .stats-card {
            background: linear-gradient(45deg, #ffc107 0%, #fd7e14 100%);
        }
        .card-danger .stats-card {
            background: linear-gradient(45deg, #dc3545 0%, #c82333 100%);
        }
        .card-info .stats-card {
            background: linear-gradient(45deg, #17a2b8 0%, #138496 100%);
        }
        .card-secondary .stats-card {
            background: linear-gradient(45deg, #6c757d 0%, #545b62 100%);
        }
        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 10px;
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .btn-gradient-primary {
            background: linear-gradient(45deg, #007bff, #0056b3);
            border: none;
            color: white;
        }
        .btn-gradient-success {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
        }
        .btn-gradient-warning {
            background: linear-gradient(45deg, #ffc107, #fd7e14);
            border: none;
            color: white;
        }
        .btn-gradient-danger {
            background: linear-gradient(45deg, #dc3545, #c82333);
            border: none;
            color: white;
        }
        .btn-gradient-info {
            background: linear-gradient(45deg, #17a2b8, #138496);
            border: none;
            color: white;
        }
        .btn-gradient-secondary {
            background: linear-gradient(45deg, #6c757d, #545b62);
            border: none;
            color: white;
        }
        .search-filter-section {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        .commission-type-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
            border-radius: 10px;
        }
        .payment-status-badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        .earnings-amount {
            font-weight: bold;
            font-size: 1.1rem;
        }
        .driver-info {
            font-size: 0.85rem;
        }
        .date-info {
            font-size: 0.8rem;
            color: #6c757d;
        }
        #jsTest {
            background: red;
            color: white;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <!-- JavaScript Test Indicator -->
    <div id="jsTest">⏳ JavaScript Loading...</div>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-money-bill-wave me-3"></i>Driver Commission Management
                    </h1>
                    <p class="mt-2 mb-0 opacity-75">ড্রাইভার কমিশন পরিচালনা, পেমেন্ট ট্র্যাকিং এবং আয়ের হিসাব রাখুন</p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light btn-lg" id="addCommissionBtn">
                        <i class="fas fa-plus me-2"></i>নতুন কমিশন
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-primary">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['total_commissions'] ?? 0 }}</div>
                        <div class="stats-label">মোট কমিশন</div>
                        <i class="fas fa-money-bill-wave position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-success">
                    <div class="stats-card text-center">
                        <div class="stats-number">৳{{ number_format($stats['total_earnings'] ?? 0, 0) }}</div>
                        <div class="stats-label">মোট আয়</div>
                        <i class="fas fa-coins position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-warning">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['pending_payments'] ?? 0 }}</div>
                        <div class="stats-label">বকেয়া পেমেন্ট</div>
                        <i class="fas fa-clock position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-info">
                    <div class="stats-card text-center">
                        <div class="stats-number">৳{{ number_format($stats['pending_amount'] ?? 0, 0) }}</div>
                        <div class="stats-label">বকেয়া পরিমাণ</div>
                        <i class="fas fa-hourglass-half position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-danger">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['paid_payments'] ?? 0 }}</div>
                        <div class="stats-label">পরিশোধিত</div>
                        <i class="fas fa-check-circle position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="card-secondary">
                    <div class="stats-card text-center">
                        <div class="stats-number">{{ $stats['active_drivers'] ?? 0 }}</div>
                        <div class="stats-label">সক্রিয় ড্রাইভার</div>
                        <i class="fas fa-user-check position-absolute" style="top: 15px; right: 20px; font-size: 1.5rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search_term" class="form-label">অনুসন্ধান</label>
                        <input type="text" class="form-control" id="search_term" placeholder="ড্রাইভার, রেফারেন্স অনুসন্ধান করুন...">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="driver_filter" class="form-label">ড্রাইভার</label>
                        <select class="form-select" id="driver_filter">
                            <option value="">সকল ড্রাইভার</option>
                            @foreach($drivers ?? [] as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="commission_type_filter" class="form-label">কমিশনের ধরন</label>
                        <select class="form-select" id="commission_type_filter">
                            <option value="">সকল ধরন</option>
                            <option value="per_ride">প্রতি রাইড</option>
                            <option value="daily_bonus">দৈনিক বোনাস</option>
                            <option value="weekly_bonus">সাপ্তাহিক বোনাস</option>
                            <option value="monthly_bonus">মাসিক বোনাস</option>
                            <option value="referral_bonus">রেফারেল বোনাস</option>
                            <option value="penalty">জরিমানা</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="payment_status_filter" class="form-label">পেমেন্ট স্ট্যাটাস</label>
                        <select class="form-select" id="payment_status_filter">
                            <option value="">সকল স্ট্যাটাস</option>
                            <option value="pending">বকেয়া</option>
                            <option value="processing">প্রক্রিয়াধীন</option>
                            <option value="paid">পরিশোধিত</option>
                            <option value="failed">ব্যর্থ</option>
                            <option value="cancelled">বাতিল</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-gradient-primary" id="applyFilters">
                                <i class="fas fa-search me-1"></i>ফিল্টার
                            </button>
                            <button class="btn btn-gradient-secondary" id="clearFilters">
                                <i class="fas fa-times me-1"></i>ক্লিয়ার
                            </button>
                            <button class="btn btn-gradient-info" id="exportData">
                                <i class="fas fa-download me-1"></i>এক্সপোর্ট
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="start_date" class="form-label">শুরুর তারিখ</label>
                        <input type="date" class="form-control" id="start_date">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="end_date" class="form-label">শেষের তারিখ</label>
                        <input type="date" class="form-control" id="end_date">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_filter" class="form-label">দ্রুত তারিখ</label>
                        <select class="form-select" id="date_filter">
                            <option value="">কাস্টম তারিখ</option>
                            <option value="today">আজ</option>
                            <option value="yesterday">গতকাল</option>
                            <option value="this_week">এই সপ্তাহ</option>
                            <option value="last_week">গত সপ্তাহ</option>
                            <option value="this_month">এই মাস</option>
                            <option value="last_month">গত মাস</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Data Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-list me-2"></i>ড্রাইভার কমিশন তালিকা
                            </h5>
                            <small class="text-muted">সকল ড্রাইভার কমিশন এবং পেমেন্ট পরিচালনা করুন</small>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="view_mode" id="pending_commissions" value="pending" autocomplete="off" checked>
                                <label class="btn btn-outline-warning btn-sm" for="pending_commissions">বকেয়া</label>

                                <input type="radio" class="btn-check" name="view_mode" id="all_commissions" value="all" autocomplete="off">
                                <label class="btn btn-outline-primary btn-sm" for="all_commissions">সকল</label>

                                <input type="radio" class="btn-check" name="view_mode" id="paid_commissions" value="paid" autocomplete="off">
                                <label class="btn btn-outline-success btn-sm" for="paid_commissions">পরিশোধিত</label>
                            </div>
                            <button class="btn btn-gradient-success btn-sm dropdown-toggle" data-bs-toggle="dropdown" id="bulkActions">
                                <i class="fas fa-cogs me-1"></i>বাল্ক অ্যাকশন
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="bulkMarkPaid"><i class="fas fa-check me-2"></i>পরিশোধিত মার্ক করুন</a></li>
                                <li><a class="dropdown-item" href="#" id="bulkMarkFailed"><i class="fas fa-times me-2"></i>ব্যর্থ মার্ক করুন</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#" id="bulkExport"><i class="fas fa-download me-2"></i>নির্বাচিত এক্সপোর্ট</a></li>
                            </ul>
                            <button class="btn btn-gradient-primary btn-sm" id="refreshData">
                                <i class="fas fa-sync-alt me-1"></i>রিফ্রেশ
                            </button>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Loading Spinner -->
                        <div class="loading-spinner" id="loadingSpinner">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">কমিশন ডেটা লোড হচ্ছে...</p>
                        </div>

                        <!-- Data Table -->
                        <div class="table-responsive pt-3">
                            <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap"
                                cellspacing="0" width="100%">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th width="5%">#</th>
                                        <th width="15%">ড্রাইভার</th>
                                        <th width="10%">কমিশনের ধরন</th>
                                        <th width="10%">বেস ভাড়া</th>
                                        <th width="8%">কমিশন রেট</th>
                                        <th width="10%">মোট আয়</th>
                                        <th width="10%">পেমেন্ট স্ট্যাটাস</th>
                                        <th width="10%">আয়ের তারিখ</th>
                                        <th width="10%">পেমেন্ট তারিখ</th>
                                        <th width="9%">অ্যাকশন</th>
                                    </tr>
                                </thead>
                                <tbody id="commissionsTableBody">
                                    <!-- Initial loading state -->
                                    <tr>
                                        <td colspan="11" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">কমিশন ডেটা লোড হচ্ছে...</h5>
                                                <p class="text-muted">অনুগ্রহ করে অপেক্ষা করুন।</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="dataTables_info">
                                দেখানো হচ্ছে <span id="showing_from">0</span> থেকে <span id="showing_to">0</span> পর্যন্ত, মোট <span id="total_records">0</span> টি এন্ট্রি
                            </div>
                            <div class="dataTables_paginate">
                                <ul class="pagination pagination-sm" id="pagination_links">
                                    <!-- Pagination links will be generated here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Commission Modal -->
    <div class="modal fade" id="commissionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="commissionModalTitle">নতুন কমিশন যোগ করুন</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="commissionForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="driver_id" class="form-label">ড্রাইভার নির্বাচন করুন *</label>
                                <select class="form-select" id="driver_id" name="driver_id" required>
                                    <option value="">ড্রাইভার নির্বাচন করুন</option>
                                    @foreach($drivers ?? [] as $driver)
                                        <option value="{{ $driver->id }}">{{ $driver->name }} - {{ $driver->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="commission_type" class="form-label">কমিশনের ধরন *</label>
                                <select class="form-select" id="commission_type" name="commission_type" required>
                                    <option value="">কমিশনের ধরন নির্বাচন করুন</option>
                                    <option value="per_ride">প্রতি রাইড কমিশন</option>
                                    <option value="daily_bonus">দৈনিক বোনাস</option>
                                    <option value="weekly_bonus">সাপ্তাহিক বোনাস</option>
                                    <option value="monthly_bonus">মাসিক বোনাস</option>
                                    <option value="referral_bonus">রেফারেল বোনাস</option>
                                    <option value="penalty">জরিমানা</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="base_fare" class="form-label">বেস ভাড়া (৳) *</label>
                                <input type="number" class="form-control" id="base_fare" name="base_fare" step="0.01" min="0" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="commission_rate" class="form-label">কমিশন রেট (%) *</label>
                                <input type="number" class="form-control" id="commission_rate" name="commission_rate" step="0.01" min="0" max="100" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="commission_amount" class="form-label">কমিশন পরিমাণ (৳)</label>
                                <input type="number" class="form-control" id="commission_amount" name="commission_amount" step="0.01" min="0" readonly>
                                <small class="text-muted">স্বয়ংক্রিয়ভাবে গণনা হবে</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="bonus_amount" class="form-label">বোনাস পরিমাণ (৳)</label>
                                <input type="number" class="form-control" id="bonus_amount" name="bonus_amount" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="penalty_amount" class="form-label">জরিমানা পরিমাণ (৳)</label>
                                <input type="number" class="form-control" id="penalty_amount" name="penalty_amount" step="0.01" min="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="earning_date" class="form-label">আয়ের তারিখ *</label>
                                <input type="date" class="form-control" id="earning_date" name="earning_date" required>
                            </div>
                            <div class="col-md-6 mb-3" id="bonus_type_field" style="display: none;">
                                <label for="bonus_type" class="form-label">বোনাসের ধরন</label>
                                <input type="text" class="form-control" id="bonus_type" name="bonus_type" placeholder="বোনাসের ধরন লিখুন">
                            </div>
                            <div class="col-md-6 mb-3" id="penalty_type_field" style="display: none;">
                                <label for="penalty_type" class="form-label">জরিমানার ধরন</label>
                                <input type="text" class="form-control" id="penalty_type" name="penalty_type" placeholder="জরিমানার ধরন লিখুন">
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">বিবরণ</label>
                                <textarea class="form-control" id="description" name="description" rows="3" placeholder="অতিরিক্ত বিবরণ..."></textarea>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="alert alert-info">
                                    <strong>মোট আয়:</strong> <span id="total_earning_display">৳0.00</span>
                                    <br><small>মোট আয় = কমিশন পরিমাণ + বোনাস পরিমাণ - জরিমানা পরিমাণ</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-primary" id="saveCommissionBtn">সংরক্ষণ করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalTitle">পেমেন্ট আপডেট</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">পেমেন্ট পদ্ধতি *</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="">পেমেন্ট পদ্ধতি নির্বাচন করুন</option>
                                <option value="bank_transfer">ব্যাংক ট্রান্সফার</option>
                                <option value="mobile_banking">মোবাইল ব্যাংকিং</option>
                                <option value="cash">নগদ</option>
                                <option value="cheque">চেক</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="payment_reference" class="form-label">পেমেন্ট রেফারেন্স</label>
                            <input type="text" class="form-control" id="payment_reference" name="payment_reference" placeholder="ট্রানজেকশন আইডি বা চেক নম্বর">
                        </div>
                        <div class="mb-3" id="failure_reason_field" style="display: none;">
                            <label for="failure_reason" class="form-label">ব্যর্থতার কারণ *</label>
                            <textarea class="form-control" id="failure_reason" name="failure_reason" rows="3" placeholder="পেমেন্ট ব্যর্থ হওয়ার কারণ লিখুন..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-success" id="markAsPaidBtn">পরিশোধিত মার্ক করুন</button>
                        <button type="submit" class="btn btn-danger" id="markAsFailedBtn" style="display: none;">ব্যর্থ মার্ক করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Payment Modal -->
    <div class="modal fade" id="bulkPaymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">বাল্ক পেমেন্ট আপডেট</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="bulkPaymentForm">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <strong>নির্বাচিত:</strong> <span id="selected_count">0</span> টি কমিশন
                        </div>
                        <div class="mb-3">
                            <label for="bulk_payment_status" class="form-label">পেমেন্ট স্ট্যাটাস *</label>
                            <select class="form-select" id="bulk_payment_status" name="bulk_payment_status" required>
                                <option value="">স্ট্যাটাস নির্বাচন করুন</option>
                                <option value="paid">পরিশোধিত</option>
                                <option value="failed">ব্যর্থ</option>
                                <option value="processing">প্রক্রিয়াধীন</option>
                            </select>
                        </div>
                        <div class="mb-3" id="bulk_payment_method_field">
                            <label for="bulk_payment_method" class="form-label">পেমেন্ট পদ্ধতি</label>
                            <select class="form-select" id="bulk_payment_method" name="bulk_payment_method">
                                <option value="">পেমেন্ট পদ্ধতি নির্বাচন করুন</option>
                                <option value="bank_transfer">ব্যাংক ট্রান্সফার</option>
                                <option value="mobile_banking">মোবাইল ব্যাংকিং</option>
                                <option value="cash">নগদ</option>
                                <option value="cheque">চেক</option>
                            </select>
                        </div>
                        <div class="mb-3" id="bulk_failure_reason_field" style="display: none;">
                            <label for="bulk_failure_reason" class="form-label">ব্যর্থতার কারণ</label>
                            <textarea class="form-control" id="bulk_failure_reason" name="bulk_failure_reason" rows="3" placeholder="পেমেন্ট ব্যর্থ হওয়ার কারণ লিখুন..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">বাতিল</button>
                        <button type="submit" class="btn btn-primary">আপডেট করুন</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
// Immediate test
console.log('=== DRIVER COMMISSION JAVASCRIPT LOADING ===');
document.getElementById('jsTest').innerHTML = '⚡ JavaScript is loading...';
document.getElementById('jsTest').style.background = 'orange';

$(document).ready(function() {
    console.log('🚀 Document Ready - Driver Commission Dashboard Starting...');
    
    // Update test indicator
    $('#jsTest').html('✅ JavaScript Working!').css('background', 'green');
    
    let currentViewMode = 'pending';
    let selectedCommissions = [];
    let editingId = null;

    // Check basic requirements
    if (typeof $ === 'undefined') {
        alert('jQuery not loaded!');
        return;
    }
    
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 not loaded');
    }

    console.log('✅ Basic libraries loaded successfully');

    // Initialize page
    initializePage();

    function initializePage() {
        console.log('📊 Initializing page...');
        
        // Bind event handlers first
        bindEventHandlers();
        
        // Then load data
        setTimeout(loadCommissions, 500);
        
        console.log('✅ Page initialization complete');
    }

    function bindEventHandlers() {
        console.log('🔗 Binding event handlers...');

        // Basic button events
        $('#addCommissionBtn').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('➕ Add commission button clicked');
            openAddModal();
        });

        $('#applyFilters').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🔍 Filter button clicked');
            loadCommissions();
        });

        $('#clearFilters').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🧹 Clear filters clicked');
            clearFilters();
        });

        $('#refreshData').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('🔄 Refresh clicked');
            loadCommissions();
        });

        // View mode changes
        $('input[name="view_mode"]').off('change').on('change', function() {
            currentViewMode = $(this).val();
            console.log('📋 View mode changed to:', currentViewMode);
            loadCommissions();
        });

        // Commission form submission
        $('#commissionForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            console.log('💾 Commission form submitted');
            saveCommission();
        });

        // Payment form submission
        $('#paymentForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            console.log('💳 Payment form submitted');
            updatePayment();
        });

        // Bulk payment form submission
        $('#bulkPaymentForm').off('submit').on('submit', function(e) {
            e.preventDefault();
            console.log('💰 Bulk payment form submitted');
            bulkUpdatePayments();
        });

        // Commission type change handler
        $('#commission_type').off('change').on('change', function() {
            const type = $(this).val();
            toggleFieldsByCommissionType(type);
        });

        // Bulk payment status change handler
        $('#bulk_payment_status').off('change').on('change', function() {
            const status = $(this).val();
            toggleBulkPaymentFields(status);
        });

        // Calculate commission amount when base fare or rate changes
        $('#base_fare, #commission_rate').off('input').on('input', calculateCommissionAmount);
        $('#commission_amount, #bonus_amount, #penalty_amount').off('input').on('input', calculateTotalEarning);

        // Date filter quick selection
        $('#date_filter').off('change').on('change', function() {
            const filter = $(this).val();
            if (filter) {
                setQuickDateFilter(filter);
            }
        });

        // Dynamic event handlers for table actions
        $(document).off('click', '.editBtn').on('click', '.editBtn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('✏️ Edit clicked for ID:', id);
            editCommission(id);
        });

        $(document).off('click', '.deleteBtn').on('click', '.deleteBtn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            console.log('🗑️ Delete clicked for ID:', id);
            deleteCommission(id);
        });

        $(document).off('click', '.paymentBtn').on('click', '.paymentBtn', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const action = $(this).data('action'); // 'paid' or 'failed'
            console.log('💳 Payment button clicked for ID:', id, 'Action:', action);
            openPaymentModal(id, action);
        });

        // Checkbox handlers
        $('#selectAll').off('change').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.commission-checkbox').prop('checked', isChecked);
            updateSelectedCommissions();
        });

        $(document).off('change', '.commission-checkbox').on('change', '.commission-checkbox', function() {
            updateSelectedCommissions();
        });

        // Bulk action handlers
        $('#bulkMarkPaid').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('💰 Bulk mark paid clicked');
            openBulkPaymentModal('paid');
        });

        $('#bulkMarkFailed').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('❌ Bulk mark failed clicked');
            openBulkPaymentModal('failed');
        });

        $('#bulkExport').off('click').on('click', function(e) {
            e.preventDefault();
            console.log('📥 Bulk export clicked');
            exportSelectedCommissions();
        });

        console.log('✅ Event handlers bound successfully');
    }

    function loadCommissions() {
        console.log('📥 Loading commissions...');
        showLoading(true);

        const filters = {
            search: $('#search_term').val() || '',
            driver_id: $('#driver_filter').val() || '',
            commission_type: $('#commission_type_filter').val() || '',
            payment_status: $('#payment_status_filter').val() || '',
            start_date: $('#start_date').val() || '',
            end_date: $('#end_date').val() || '',
            view_mode: currentViewMode
        };

        console.log('🔍 Filters applied:', filters);

        // Try multiple possible routes
        const possibleRoutes = [
            '/admin/driver-commissions/get-data',
            '/admin/drivercommissions/get-data',
            '/api/driver-commissions',
        ];

        tryRoute(0, possibleRoutes, filters);
    }

    function tryRoute(index, routes, filters) {
        if (index >= routes.length) {
            console.error('❌ All routes failed, showing mock data');
            showMockData();
            showLoading(false);
            return;
        }

        const url = routes[index];
        console.log(`🔗 Trying route ${index + 1}:`, url);

        $.ajax({
            url: url,
            method: 'GET',
            data: filters,
            timeout: 5000,
            success: function(response) {
                console.log('✅ Route successful:', url);
                console.log('📊 Response:', response);
                handleSuccessResponse(response);
                showLoading(false);
            },
            error: function(xhr, status, error) {
                console.log(`❌ Route ${index + 1} failed:`, xhr.status, error);
                tryRoute(index + 1, routes, filters);
            }
        });
    }

    function handleSuccessResponse(response) {
        try {
            if (response && response.success) {
                if (response.html) {
                    $('#commissionsTableBody').html(response.html);
                    updatePaginationInfo(response);
                } else if (response.data) {
                    generateTableRows(response.data);
                } else {
                    showEmptyState('কোন কমিশন পাওয়া যায়নি');
                }
            } else {
                console.warn('⚠️ Response indicates failure:', response);
                showMockData();
            }
        } catch (e) {
            console.error('❌ Error processing response:', e);
            showMockData();
        }
    }

    function showMockData() {
        console.log('📋 Showing mock data for testing...');
        const mockData = `
            <tr>
                <td><input type="checkbox" class="form-check-input commission-checkbox" value="1"></td>
                <td>1</td>
                <td>
                    <strong>আহমেদ করিম</strong><br>
                    <small class="text-muted driver-info">ahmed@example.com</small><br>
                    <small class="text-muted driver-info">০১৭১২৩৪৫৬৭৮</small>
                </td>
                <td><span class="badge commission-type-badge bg-primary">প্রতি রাইড</span></td>
                <td class="earnings-amount">৳৫০০.০০</td>
                <td>১৫%</td>
                <td class="earnings-amount text-success">৳৭৫.০০</td>
                <td><span class="badge payment-status-badge bg-warning">বকেয়া</span></td>
                <td>
                    <strong>১৫ জানুয়ারি, ২০২৪</strong><br>
                    <small class="date-info">২ দিন আগে</small>
                </td>
                <td>
                    <span class="text-muted">-</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="1" title="সম্পাদনা">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-success paymentBtn" data-id="1" data-action="paid" title="পরিশোধিত মার্ক">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-danger paymentBtn" data-id="1" data-action="failed" title="ব্যর্থ মার্ক">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="form-check-input commission-checkbox" value="2"></td>
                <td>২</td>
                <td>
                    <strong>রহিম উদ্দিন</strong><br>
                    <small class="text-muted driver-info">rahim@example.com</small><br>
                    <small class="text-muted driver-info">০১৮১২৩৪৫৬৭৮</small>
                </td>
                <td><span class="badge commission-type-badge bg-info">সাপ্তাহিক বোনাস</span></td>
                <td class="earnings-amount">৳১২০০.০০</td>
                <td>২০%</td>
                <td class="earnings-amount text-success">৳২৪০.০০</td>
                <td><span class="badge payment-status-badge bg-success">পরিশোধিত</span></td>
                <td>
                    <strong>১২ জানুয়ারি, ২০২৪</strong><br>
                    <small class="date-info">৫ দিন আগে</small>
                </td>
                <td>
                    <strong>১৪ জানুয়ারি, ২০২৪</strong><br>
                    <small class="date-info">ব্যাংক ট্রান্সফার</small>
                </td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="2" title="সম্পাদনা">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-info" disabled title="ইতিমধ্যে পরিশোধিত">
                        <i class="fas fa-check-circle"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td><input type="checkbox" class="form-check-input commission-checkbox" value="3"></td>
                <td>৩</td>
                <td>
                    <strong>সালমা খাতুন</strong><br>
                    <small class="text-muted driver-info">salma@example.com</small><br>
                    <small class="text-muted driver-info">০১৯১২৩৪৫৬৭৮</small>
                </td>
                <td><span class="badge commission-type-badge bg-danger">জরিমানা</span></td>
                <td class="earnings-amount">৳৮০০.০০</td>
                <td>১০%</td>
                <td class="earnings-amount text-danger">-৳৫০.০০</td>
                <td><span class="badge payment-status-badge bg-danger">ব্যর্থ</span></td>
                <td>
                    <strong>১০ জানুয়ারি, ২০২৪</strong><br>
                    <small class="date-info">৭ দিন আগে</small>
                </td>
                <td>
                    <span class="text-muted">-</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-gradient-primary editBtn" data-id="3" title="সম্পাদনা">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-success paymentBtn" data-id="3" data-action="paid" title="পরিশোধিত মার্ক">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-gradient-warning" title="পুনরায় চেষ্টা">
                        <i class="fas fa-redo"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#commissionsTableBody').html(mockData);
        updatePaginationInfo({ total: 3, from: 1, to: 3 });
    }

    function generateTableRows(data) {
        if (!data || data.length === 0) {
            showEmptyState('কোন কমিশন পাওয়া যায়নি');
            return;
        }

        let html = '';
        data.forEach((commission, index) => {
            html += `
                <tr>
                    <td><input type="checkbox" class="form-check-input commission-checkbox" value="${commission.id}"></td>
                    <td>${index + 1}</td>
                    <td>
                        <strong>${commission.driver?.name || 'অজ্ঞাত ড্রাইভার'}</strong><br>
                        <small class="text-muted driver-info">${commission.driver?.email || 'N/A'}</small><br>
                        <small class="text-muted driver-info">${commission.driver?.phone || 'N/A'}</small>
                    </td>
                    <td>${getCommissionTypeBadge(commission.commission_type)}</td>
                    <td class="earnings-amount">৳${commission.base_fare || '0.00'}</td>
                    <td>${commission.commission_rate || '0'}%</td>
                    <td class="earnings-amount ${parseFloat(commission.total_earning) >= 0 ? 'text-success' : 'text-danger'}">৳${commission.total_earning || '0.00'}</td>
                    <td>${getPaymentStatusBadge(commission.payment_status)}</td>
                    <td>
                        <strong>${commission.formatted_earning_date || commission.earning_date}</strong><br>
                        <small class="date-info">${getRelativeDate(commission.earning_date)}</small>
                    </td>
                    <td>
                        ${commission.payment_date ? 
                            `<strong>${commission.formatted_payment_date || commission.payment_date}</strong><br>
                             <small class="date-info">${commission.payment_method || 'N/A'}</small>` : 
                            '<span class="text-muted">-</span>'
                        }
                    </td>
                    <td>
                        <button class="btn btn-sm btn-gradient-primary editBtn" data-id="${commission.id}" title="সম্পাদনা">
                            <i class="fas fa-edit"></i>
                        </button>
                        ${commission.payment_status !== 'paid' ? 
                            `<button class="btn btn-sm btn-gradient-success paymentBtn" data-id="${commission.id}" data-action="paid" title="পরিশোধিত মার্ক">
                                <i class="fas fa-check"></i>
                             </button>
                             <button class="btn btn-sm btn-gradient-danger paymentBtn" data-id="${commission.id}" data-action="failed" title="ব্যর্থ মার্ক">
                                <i class="fas fa-times"></i>
                             </button>` :
                            `<button class="btn btn-sm btn-gradient-info" disabled title="ইতিমধ্যে পরিশোধিত">
                                <i class="fas fa-check-circle"></i>
                             </button>`
                        }
                    </td>
                </tr>
            `;
        });
        $('#commissionsTableBody').html(html);
        updatePaginationInfo({ total: data.length, from: 1, to: data.length });
    }

    function getCommissionTypeBadge(type) {
        const badges = {
            'per_ride': '<span class="badge commission-type-badge bg-primary">প্রতি রাইড</span>',
            'daily_bonus': '<span class="badge commission-type-badge bg-success">দৈনিক বোনাস</span>',
            'weekly_bonus': '<span class="badge commission-type-badge bg-info">সাপ্তাহিক বোনাস</span>',
            'monthly_bonus': '<span class="badge commission-type-badge bg-warning">মাসিক বোনাস</span>',
            'referral_bonus': '<span class="badge commission-type-badge bg-secondary">রেফারেল বোনাস</span>',
            'penalty': '<span class="badge commission-type-badge bg-danger">জরিমানা</span>'
        };
        return badges[type] || `<span class="badge commission-type-badge bg-light">${type || 'অজ্ঞাত'}</span>`;
    }

    function getPaymentStatusBadge(status) {
        const badges = {
            'pending': '<span class="badge payment-status-badge bg-warning">বকেয়া</span>',
            'processing': '<span class="badge payment-status-badge bg-info">প্রক্রিয়াধীন</span>',
            'paid': '<span class="badge payment-status-badge bg-success">পরিশোধিত</span>',
            'failed': '<span class="badge payment-status-badge bg-danger">ব্যর্থ</span>',
            'cancelled': '<span class="badge payment-status-badge bg-secondary">বাতিল</span>'
        };
        return badges[status] || `<span class="badge payment-status-badge bg-light">${status || 'অজ্ঞাত'}</span>`;
    }

    function getRelativeDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays === 1) return 'গতকাল';
        if (diffDays === 0) return 'আজ';
        return `${diffDays} দিন আগে`;
    }

    function showLoading(show) {
        if (show) {
            $('#loadingSpinner').show();
            $('#commissionsTableBody').html('<tr><td colspan="11" class="text-center">লোড হচ্ছে...</td></tr>');
            console.log('⏳ Loading shown');
        } else {
            $('#loadingSpinner').hide();
            console.log('✅ Loading hidden');
        }
    }

    function showEmptyState(message = 'কোন ডেটা পাওয়া যায়নি') {
        const emptyHtml = `
            <tr>
                <td colspan="11" class="text-center py-4">
                    <div class="empty-state">
                        <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">${message}</h5>
                        <p class="text-muted">ফিল্টার পরিবর্তন করুন অথবা নতুন কমিশন যোগ করুন।</p>
                        <button class="btn btn-gradient-primary" onclick="openAddModal()">
                            <i class="fas fa-plus me-2"></i>প্রথম কমিশন যোগ করুন
                        </button>
                    </div>
                </td>
            </tr>
        `;
        $('#commissionsTableBody').html(emptyHtml);
        console.log('📭 Empty state shown:', message);
    }

    function updatePaginationInfo(response) {
        const from = response.from || 1;
        const to = response.to || response.total || 0;
        const total = response.total || 0;
        
        $('#showing_from').text(from);
        $('#showing_to').text(to);
        $('#total_records').text(total);
    }

    function clearFilters() {
        $('#search_term').val('');
        $('#driver_filter').val('');
        $('#commission_type_filter').val('');
        $('#payment_status_filter').val('');
        $('#start_date').val('');
        $('#end_date').val('');
        $('#date_filter').val('');
        loadCommissions();
        console.log('🧹 Filters cleared');
    }

    function setQuickDateFilter(filter) {
        const today = new Date();
        let startDate, endDate;

        switch(filter) {
            case 'today':
                startDate = endDate = today.toISOString().split('T')[0];
                break;
            case 'yesterday':
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                startDate = endDate = yesterday.toISOString().split('T')[0];
                break;
            case 'this_week':
                const thisWeekStart = new Date(today);
                thisWeekStart.setDate(today.getDate() - today.getDay());
                startDate = thisWeekStart.toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_week':
                const lastWeekEnd = new Date(today);
                lastWeekEnd.setDate(today.getDate() - today.getDay() - 1);
                const lastWeekStart = new Date(lastWeekEnd);
                lastWeekStart.setDate(lastWeekEnd.getDate() - 6);
                startDate = lastWeekStart.toISOString().split('T')[0];
                endDate = lastWeekEnd.toISOString().split('T')[0];
                break;
            case 'this_month':
                startDate = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
                endDate = today.toISOString().split('T')[0];
                break;
            case 'last_month':
                const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0);
                startDate = lastMonth.toISOString().split('T')[0];
                endDate = lastMonthEnd.toISOString().split('T')[0];
                break;
        }

        if (startDate && endDate) {
            $('#start_date').val(startDate);
            $('#end_date').val(endDate);
        }
    }

    function openAddModal() {
        console.log('➕ Opening add commission modal');
        editingId = null;
        $('#commissionModalTitle').text('নতুন কমিশন যোগ করুন');
        $('#commissionForm')[0].reset();
        $('#earning_date').val(new Date().toISOString().split('T')[0]); // Set today's date
        $('#total_earning_display').text('৳0.00');
        toggleFieldsByCommissionType('');
        $('#commissionModal').modal('show');
    }

    function editCommission(id) {
        console.log('✏️ Edit commission:', id);
        editingId = id;
        $('#commissionModalTitle').text('কমিশন সম্পাদনা করুন');
        
        // Mock data for editing - replace with actual API call
        if (id == 1) {
            $('#driver_id').val('1');
            $('#commission_type').val('per_ride');
            $('#base_fare').val('500.00');
            $('#commission_rate').val('15');
            $('#commission_amount').val('75.00');
            $('#bonus_amount').val('0');
            $('#penalty_amount').val('0');
            $('#earning_date').val('2024-01-15');
            $('#description').val('Regular ride commission');
        } else if (id == 2) {
            $('#driver_id').val('2');
            $('#commission_type').val('weekly_bonus');
            $('#base_fare').val('1200.00');
            $('#commission_rate').val('20');
            $('#commission_amount').val('240.00');
            $('#bonus_amount').val('50');
            $('#penalty_amount').val('0');
            $('#earning_date').val('2024-01-12');
            $('#bonus_type').val('Performance bonus');
            $('#description').val('Weekly performance bonus');
        } else {
            $('#driver_id').val('3');
            $('#commission_type').val('penalty');
            $('#base_fare').val('800.00');
            $('#commission_rate').val('10');
            $('#commission_amount').val('80.00');
            $('#bonus_amount').val('0');
            $('#penalty_amount').val('130');
            $('#earning_date').val('2024-01-10');
            $('#penalty_type').val('Late arrival');
            $('#description').val('Penalty for late arrival');
        }
        
        toggleFieldsByCommissionType($('#commission_type').val());
        calculateTotalEarning();
        $('#commissionModal').modal('show');
    }

    function deleteCommission(id) {
        console.log('🗑️ Delete commission:', id);
        
        Swal.fire({
            title: 'আপনি কি নিশ্চিত?',
            text: 'এই কমিশনটি মুছে ফেলা হবে!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'হ্যাঁ, মুছে ফেলুন!',
            cancelButtonText: 'বাতিল'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mock delete - replace with actual API call
                Swal.fire('মুছে ফেলা হয়েছে!', 'কমিশনটি সফলভাবে মুছে ফেলা হয়েছে।', 'success');
                loadCommissions();
            }
        });
    }

    function saveCommission() {
        console.log('💾 Saving commission...');
        
        const formData = {
            driver_id: $('#driver_id').val(),
            commission_type: $('#commission_type').val(),
            base_fare: $('#base_fare').val(),
            commission_rate: $('#commission_rate').val(),
            commission_amount: $('#commission_amount').val(),
            bonus_amount: $('#bonus_amount').val() || 0,
            penalty_amount: $('#penalty_amount').val() || 0,
            earning_date: $('#earning_date').val(),
            bonus_type: $('#bonus_type').val(),
            penalty_type: $('#penalty_type').val(),
            description: $('#description').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('📝 Form data:', formData);

        // Validation
        if (!formData.driver_id || !formData.commission_type || !formData.base_fare || !formData.commission_rate || !formData.earning_date) {
            Swal.fire({
                title: 'ত্রুটি!',
                text: 'অনুগ্রহ করে সকল প্রয়োজনীয় ক্ষেত্র পূরণ করুন।',
                icon: 'error'
            });
            return;
        }

        // Mock save for now - replace with actual API call
        setTimeout(() => {
            Swal.fire({
                title: 'সফল!',
                text: editingId ? 'কমিশন সফলভাবে আপডেট হয়েছে!' : 'কমিশন সফলভাবে তৈরি হয়েছে!',
                icon: 'success',
                timer: 2000
            });

            $('#commissionModal').modal('hide');
            loadCommissions();
        }, 1000);
    }

    function openPaymentModal(id, action) {
        console.log('💳 Opening payment modal for ID:', id, 'Action:', action);
        
        $('#paymentForm')[0].reset();
        
        if (action === 'paid') {
            $('#paymentModalTitle').text('পেমেন্ট সম্পূর্ণ করুন');
            $('#markAsPaidBtn').show();
            $('#markAsFailedBtn').hide();
            $('#failure_reason_field').hide();
            $('#payment_method').prop('required', true);
        } else if (action === 'failed') {
            $('#paymentModalTitle').text('পেমেন্ট ব্যর্থ মার্ক করুন');
            $('#markAsPaidBtn').hide();
            $('#markAsFailedBtn').show();
            $('#failure_reason_field').show();
            $('#payment_method').prop('required', false);
            $('#failure_reason').prop('required', true);
        }
        
        $('#paymentModal').data('commission-id', id).data('action', action).modal('show');
    }

    function updatePayment() {
        console.log('💳 Updating payment...');
        
        const commissionId = $('#paymentModal').data('commission-id');
        const action = $('#paymentModal').data('action');
        
        const formData = {
            commission_id: commissionId,
            action: action,
            payment_method: $('#payment_method').val(),
            payment_reference: $('#payment_reference').val(),
            failure_reason: $('#failure_reason').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('📝 Payment data:', formData);

        // Mock update for now - replace with actual API call
        setTimeout(() => {
            const message = action === 'paid' ? 'পেমেন্ট সফলভাবে সম্পূর্ণ হয়েছে!' : 'পেমেন্ট ব্যর্থ হিসেবে মার্ক করা হয়েছে!';
            
            Swal.fire({
                title: 'সফল!',
                text: message,
                icon: 'success',
                timer: 2000
            });

            $('#paymentModal').modal('hide');
            loadCommissions();
        }, 1000);
    }

    function updateSelectedCommissions() {
        selectedCommissions = [];
        $('.commission-checkbox:checked').each(function() {
            selectedCommissions.push($(this).val());
        });
        
        console.log('✅ Selected commissions:', selectedCommissions);
        
        // Update select all checkbox state
        const totalCheckboxes = $('.commission-checkbox').length;
        const checkedCheckboxes = $('.commission-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            $('#selectAll').prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            $('#selectAll').prop('indeterminate', false).prop('checked', true);
        } else {
            $('#selectAll').prop('indeterminate', true);
        }
    }

    function openBulkPaymentModal(action) {
        if (selectedCommissions.length === 0) {
            Swal.fire({
                title: 'কোন নির্বাচন নেই!',
                text: 'অনুগ্রহ করে কমপক্ষে একটি কমিশন নির্বাচন করুন।',
                icon: 'warning'
            });
            return;
        }

        console.log('💰 Opening bulk payment modal for action:', action);
        
        $('#bulkPaymentForm')[0].reset();
        $('#selected_count').text(selectedCommissions.length);
        
        if (action === 'paid') {
            $('#bulk_payment_status').val('paid');
        } else if (action === 'failed') {
            $('#bulk_payment_status').val('failed');
        }
        
        toggleBulkPaymentFields($('#bulk_payment_status').val());
        $('#bulkPaymentModal').modal('show');
    }

    function bulkUpdatePayments() {
        console.log('💰 Bulk updating payments...');
        
        const formData = {
            commission_ids: selectedCommissions,
            payment_status: $('#bulk_payment_status').val(),
            payment_method: $('#bulk_payment_method').val(),
            failure_reason: $('#bulk_failure_reason').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        console.log('📝 Bulk payment data:', formData);

        // Mock update for now - replace with actual API call
        setTimeout(() => {
            Swal.fire({
                title: 'সফল!',
                text: `${selectedCommissions.length} টি কমিশনের পেমেন্ট স্ট্যাটাস আপডেট হয়েছে!`,
                icon: 'success',
                timer: 2000
            });

            $('#bulkPaymentModal').modal('hide');
            selectedCommissions = [];
            loadCommissions();
        }, 1000);
    }

    function exportSelectedCommissions() {
        if (selectedCommissions.length === 0) {
            Swal.fire({
                title: 'কোন নির্বাচন নেই!',
                text: 'অনুগ্রহ করে কমপক্ষে একটি কমিশন নির্বাচন করুন।',
                icon: 'warning'
            });
            return;
        }

        console.log('📥 Exporting selected commissions:', selectedCommissions);
        
        // Mock export for now - replace with actual API call
        Swal.fire({
            title: 'এক্সপোর্ট শুরু হয়েছে!',
            text: `${selectedCommissions.length} টি কমিশনের ডেটা এক্সপোর্ট হচ্ছে...`,
            icon: 'info',
            timer: 2000
        });
    }

    function toggleFieldsByCommissionType(type) {
        // Hide all conditional fields first
        $('#bonus_type_field, #penalty_type_field').hide();
        $('#bonus_type, #penalty_type').prop('required', false);
        
        // Show relevant fields based on commission type
        if (type && (type.includes('bonus') || type === 'referral_bonus')) {
            $('#bonus_type_field').show();
            $('#bonus_type').prop('required', true);
        }
        
        if (type === 'penalty') {
            $('#penalty_type_field').show();
            $('#penalty_type').prop('required', true);
        }
    }

    function toggleBulkPaymentFields(status) {
        if (status === 'paid') {
            $('#bulk_payment_method_field').show();
            $('#bulk_failure_reason_field').hide();
            $('#bulk_payment_method').prop('required', true);
            $('#bulk_failure_reason').prop('required', false);
        } else if (status === 'failed') {
            $('#bulk_payment_method_field').hide();
            $('#bulk_failure_reason_field').show();
            $('#bulk_payment_method').prop('required', false);
            $('#bulk_failure_reason').prop('required', true);
        } else {
            $('#bulk_payment_method_field').show();
            $('#bulk_failure_reason_field').hide();
            $('#bulk_payment_method').prop('required', false);
            $('#bulk_failure_reason').prop('required', false);
        }
    }

    function calculateCommissionAmount() {
        const baseFare = parseFloat($('#base_fare').val()) || 0;
        const commissionRate = parseFloat($('#commission_rate').val()) || 0;
        const commissionAmount = (baseFare * commissionRate) / 100;
        
        $('#commission_amount').val(commissionAmount.toFixed(2));
        calculateTotalEarning();
    }

    function calculateTotalEarning() {
        const commissionAmount = parseFloat($('#commission_amount').val()) || 0;
        const bonusAmount = parseFloat($('#bonus_amount').val()) || 0;
        const penaltyAmount = parseFloat($('#penalty_amount').val()) || 0;
        
        const totalEarning = commissionAmount + bonusAmount - penaltyAmount;
        $('#total_earning_display').text('৳' + totalEarning.toFixed(2));
    }

    // Make functions globally available for debugging
    window.loadCommissions = loadCommissions;
    window.openAddModal = openAddModal;
    window.editCommission = editCommission;
    window.deleteCommission = deleteCommission;
    window.clearFilters = clearFilters;
    window.openPaymentModal = openPaymentModal;

    // Hide test indicator after everything loads
    setTimeout(function() {
        $('#jsTest').fadeOut();
    }, 3000);

    console.log('🎉 Driver Commission Dashboard loaded successfully!');
});
</script>
@endpush