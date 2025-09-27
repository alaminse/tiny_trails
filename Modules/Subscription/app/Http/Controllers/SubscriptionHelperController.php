<?php

namespace Modules\Subscription\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Subscription\app\Models\Subscription;
use Modules\Subscription\app\Models\PaywayTransaction;
use Carbon\Carbon;

class SubscriptionHelperController extends Controller
{
    /**
     * Get expiring subscriptions
     */
    public function getExpiringSubscriptions(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            $date = Carbon::now()->addDays($days);

            $subscriptions = Subscription::with(['user', 'plan'])
                ->where('status', 'active')
                ->where(function($query) use ($date) {
                    $query->where('trial_ends_at', '<=', $date)
                          ->orWhere('ends_at', '<=', $date);
                })
                ->whereNull('canceled_at')
                ->orderBy('trial_ends_at')
                ->orderBy('ends_at')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscriptions->map(function($subscription) {
                    return [
                        'id' => $subscription->id,
                        'user' => [
                            'name' => $subscription->user->name ?? 'Unknown User',
                            'email' => $subscription->user->email ?? ''
                        ],
                        'plan' => [
                            'name' => $subscription->plan->name ?? 'Plan Deleted'
                        ],
                        'ends_at' => $subscription->trial_ends_at ?? $subscription->ends_at,

                        'days_remaining' => $subscription->onTrial()
                            ? $subscription->trialDaysRemaining()
                            : ($subscription->ends_at ? $subscription->ends_at->diffInDays(now()) : 0),
                        'type' => $subscription->onTrial() ? 'trial' : 'billing'
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get expiring subscriptions:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load expiring subscriptions'
            ], 500);
        }
    }

    /**
     * Get subscriptions with payment issues
     */
    public function getPaymentIssues(): JsonResponse
    {
        try {
            $subscriptions = Subscription::with(['user', 'plan'])
                ->whereIn('payway_status', ['past_due', 'unpaid', 'payment_failed'])
                ->orWhere(function($query) {
                    $query->where('status', 'active')
                          ->where('trial_ends_at', '<', now())
                          ->where('payway_status', '!=', 'active');
                })
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscriptions->map(function($subscription) {
                    return [
                        'id' => $subscription->id,
                        'user' => [
                            'name' => $subscription->user->name ?? 'Unknown User',
                            'email' => $subscription->user->email ?? ''
                        ],
                        'plan' => [
                            'name' => $subscription->plan->name ?? 'Plan Deleted',
                            'price' => $subscription->plan->price ?? 0
                        ],
                        'payway_status' => $subscription->payway_status,
                        'last_payment_attempt' => $this->getLastPaymentAttempt($subscription->id),
                        'issue_type' => $this->determineIssueType($subscription)
                    ];
                })
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get payment issues:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load payment issues'
            ], 500);
        }
    }

    /**
     * Get recent subscription activity
     */
    public function getRecentActivity(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 7);
            $fromDate = Carbon::now()->subDays($days);

            $subscriptions = Subscription::with(['user', 'plan'])
                ->where('created_at', '>=', $fromDate)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $transactions = PaywayTransaction::with(['user', 'subscription.plan'])
                ->where('processed_at', '>=', $fromDate)
                ->where('transaction_type', 'payment')
                ->orderBy('processed_at', 'desc')
                ->limit(10)
                ->get();

            $activity = collect();

            // Add subscription activities
            foreach ($subscriptions as $subscription) {
                $activity->push([
                    'type' => 'subscription_created',
                    'date' => $subscription->created_at,
                    'description' => "New subscription created for {$subscription->user->name}",
                    'user' => $subscription->user->name,
                    'plan' => $subscription->plan->name ?? 'Plan Deleted',
                    'status' => $subscription->status,
                    'icon' => 'fas fa-plus-circle',
                    'color' => 'success'
                ]);
            }

            // Add payment activities
            foreach ($transactions as $transaction) {
                $activity->push([
                    'type' => 'payment_processed',
                    'date' => $transaction->processed_at,
                    'description' => "Payment {$transaction->status} for {$transaction->user->name}",
                    'user' => $transaction->user->name,
                    'amount' => $transaction->formatted_amount,
                    'status' => $transaction->status,
                    'icon' => $transaction->status === 'approved' ? 'fas fa-credit-card' : 'fas fa-exclamation-triangle',
                    'color' => $transaction->status === 'approved' ? 'success' : 'danger'
                ]);
            }

            $sortedActivity = $activity->sortByDesc('date')->take(15)->values();

            return response()->json([
                'success' => true,
                'data' => $sortedActivity
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get recent activity:', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load recent activity'
            ], 500);
        }
    }

    /**
     * Process manual payment for subscription
     */
    public function processPayment(Request $request, $subscriptionId): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01'
        ]);

        try {
            $subscription = Subscription::findOrFail($subscriptionId);

            if (!$subscription->payway_customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No PayWay customer found for this subscription'
                ], 400);
            }

            // Call PayWay to process payment
            $payWayController = app(\Modules\Subscription\app\Http\Controllers\PayWayController::class);

            $paymentResponse = $payWayController->processPayment(
                $subscription->payway_customer_id,
                $request->amount,
                'manual_' . $subscription->id . '_' . time()
            );

            if (!$paymentResponse['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse['message']
                ], 400);
            }

            // Record transaction
            $transaction = new PaywayTransaction();
            $transaction->user_id = $subscription->user_id;
            $transaction->subscription_id = $subscription->id;
            $transaction->payway_transaction_id = $paymentResponse['data']['transactionId'];
            $transaction->payway_customer_id = $subscription->payway_customer_id;
            $transaction->transaction_type = 'payment';
            $transaction->amount = $paymentResponse['data']['paymentAmount'] ?? $request->amount;
            $transaction->currency = 'aud';
            $transaction->status = $paymentResponse['data']['status'];
            $transaction->response_code = $paymentResponse['data']['responseCode'] ?? null;
            $transaction->response_text = $paymentResponse['data']['responseText'] ?? null;
            $transaction->gateway_response = json_encode($paymentResponse['data']);
            $transaction->order_number = $paymentResponse['data']['orderNumber'] ?? null;
            $transaction->processed_at = now();
            $transaction->save();

            // Update subscription if payment successful
            if ($paymentResponse['data']['status'] === 'approved') {
                $subscription->payway_status = 'active';
                $subscription->status = 'active';

                // Extend billing date
                if ($subscription->ends_at) {
                    $subscription->ends_at = $subscription->ends_at->addMonth();
                } else {
                    $subscription->ends_at = now()->addMonth();
                }

                $subscription->save();
            }

            return response()->json([
                'success' => true,
                'message' => $paymentResponse['data']['status'] === 'approved'
                    ? 'Payment processed successfully'
                    : 'Payment failed: ' . ($paymentResponse['data']['responseText'] ?? 'Unknown error'),
                'data' => [
                    'transaction_id' => $transaction->id,
                    'payway_transaction_id' => $paymentResponse['data']['transactionId'],
                    'status' => $paymentResponse['data']['status'],
                    'amount' => $transaction->formatted_amount
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Manual payment processing failed:', [
                'subscription_id' => $subscriptionId,
                'amount' => $request->amount,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export subscriptions
     */
    public function exportSubscriptions(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $range = $request->get('range', 'all');

            $query = Subscription::with(['user', 'plan']);

            // Apply date filters
            switch ($range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
                case 'year':
                    $query->whereYear('created_at', now()->year);
                    break;
                case 'custom':
                    if ($request->has('date_from') && $request->has('date_to')) {
                        $query->whereBetween('created_at', [
                            $request->date_from,
                            $request->date_to . ' 23:59:59'
                        ]);
                    }
                    break;
            }

            $subscriptions = $query->get();

            $data = $subscriptions->map(function($subscription) {
                return [
                    'ID' => $subscription->id,
                    'User Name' => $subscription->user->name ?? 'Unknown',
                    'User Email' => $subscription->user->email ?? '',
                    'Plan Name' => $subscription->plan->name ?? 'Plan Deleted',
                    'Plan Price' => $subscription->plan->price ?? 0,
                    'Status' => $subscription->status,
                    'PayWay Status' => $subscription->payway_status,
                    'PayWay Customer ID' => $subscription->payway_customer_id,
                    'Trial Days' => $subscription->trial_days,
                    'Trial Ends At' => $subscription->trial_ends_at?->format('Y-m-d H:i:s'),
                    'Next Billing' => $subscription->ends_at?->format('Y-m-d H:i:s'),
                    'Card Brand' => $subscription->card_brand,
                    'Card Last Four' => $subscription->card_last_four,
                    'Card Expiration' => $subscription->card_expiration,
                    'Canceled At' => $subscription->canceled_at?->format('Y-m-d H:i:s'),
                    'Cancellation Reason' => $subscription->cancellation_reason,
                    'Created At' => $subscription->created_at->format('Y-m-d H:i:s'),
                    'Updated At' => $subscription->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            $filename = 'subscriptions_' . now()->format('Y-m-d_H-i-s');

            switch ($format) {
                case 'csv':
                    return $this->exportToCsv($data, $filename);
                case 'excel':
                    return $this->exportToExcel($data, $filename);
                case 'pdf':
                    return $this->exportToPdf($data, $filename);
                default:
                    return $this->exportToCsv($data, $filename);
            }

        } catch (\Exception $e) {
            Log::error('Export failed:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Export failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get subscription statistics for dashboard
     */

   public function getSubscriptionStats()
    {
        try {
            $stats = [
                'total' => Subscription::count(),
                'active' => Subscription::where('status', 'active')->count(),
                'inactive' => Subscription::where('status', 'inactive')->count(),
                'on_trial' => Subscription::whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', now())->count(),
                'expired' => Subscription::where('trial_ends_at', '<', now())
                    ->orWhere('ends_at', '<', now())->count(),
                'canceled' => Subscription::whereNotNull('canceled_at')->count(),
            ];

            $revenueStats = [
                'monthly_revenue' => PaywayTransaction::where('transaction_type', 'payment')
                    ->where('status', 'approved')
                    ->whereMonth('processed_at', now()->month)
                    ->whereYear('processed_at', now()->year)
                    ->sum('amount'),
                'yearly_revenue' => PaywayTransaction::where('transaction_type', 'payment')
                    ->where('status', 'approved')
                    ->whereYear('processed_at', now()->year)
                    ->sum('amount'),
                'total_active_value' => Subscription::join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                    ->where('subscriptions.status', 'active')
                    ->selectRaw('CASE WHEN plans.sell_price > 0 THEN plans.sell_price ELSE plans.price END as display_price')
                    ->sum('display_price'),
            ];

            // Debug: Log the data being returned
            Log::info('Subscription Stats:', array_merge($stats, $revenueStats));

            return response()->json([
                'success' => true,
                'data' => array_merge($stats, $revenueStats)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get subscription stats:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }

    /**
     * Private helper methods
     */
    private function getLastPaymentAttempt($subscriptionId)
    {
        return PaywayTransaction::where('subscription_id', $subscriptionId)
            ->where('transaction_type', 'payment')
            ->orderBy('processed_at', 'desc')
            ->first()?->processed_at;
    }

    private function determineIssueType($subscription)
    {
        if ($subscription->payway_status === 'past_due') {
            return 'past_due';
        }

        if ($subscription->payway_status === 'unpaid') {
            return 'unpaid';
        }

        if ($subscription->trial_ends_at && $subscription->trial_ends_at->isPast()) {
            return 'trial_expired';
        }

        return 'payment_failed';
    }

    private function exportToCsv($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add headers
            if ($data->isNotEmpty()) {
                fputcsv($file, array_keys($data->first()));
            }

            // Add data
            foreach ($data as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToExcel($data, $filename)
    {
        // This would require league/csv or maatwebsite/excel package
        // For now, fallback to CSV
        return $this->exportToCsv($data, $filename);
    }

    private function exportToPdf($data, $filename)
    {
        // This would require dompdf or similar package
        // For now, fallback to CSV
        return $this->exportToCsv($data, $filename);
    }
}
