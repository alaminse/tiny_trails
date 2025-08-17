<?php

namespace Modules\Subscription\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\UserRolePermission\app\Http\Resources\UserResource;

class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'plan_id' => $this->plan_id,
            'plan' => $this->whenLoaded('plan', function () {
                return [
                    'id' => $this->plan->id,
                    'name' => $this->plan->name,
                    'price' => $this->plan->price,
                    'formatted_price' => $this->plan->formatted_price,
                    'interval' => $this->plan->interval,
                    'interval_display' => $this->plan->interval_display,
                ];
            }),
            'name' => $this->name,
            'stripe_id' => $this->stripe_id,
            'stripe_status' => $this->stripe_status,
            'stripe_status_badge' => $this->stripe_status_badge,
            'trial_ends_at' => $this->trial_ends_at?->format('Y-m-d H:i:s'),
            'trial_ends_at_formatted' => $this->trial_ends_at?->format('M d, Y'),
            'ends_at' => $this->ends_at?->format('Y-m-d H:i:s'),
            'ends_at_formatted' => $this->ends_at?->format('M d, Y'),
            'canceled_at' => $this->canceled_at?->format('Y-m-d H:i:s'),
            'canceled_at_formatted' => $this->canceled_at?->format('M d, Y'),
            'cancellation_reason' => $this->cancellation_reason,
            'status' => $this->status,
            'status_badge' => $this->status_badge,
            'card_brand' => $this->card_brand,
            'card_last_four' => $this->card_last_four,
            'card_expiration' => $this->card_expiration,
            'card_display' => $this->when(
                $this->card_brand && $this->card_last_four,
                ucfirst($this->card_brand) . ' ending in ' . $this->card_last_four
            ),
            'is_on_trial' => $this->isOnTrial(),
            'has_expired' => $this->hasExpired(),
            'is_canceled' => $this->isCanceled(),
            'days_until_trial_ends' => $this->when(
                $this->isOnTrial(),
                $this->trial_ends_at->diffInDays(now())
            ),
            'days_until_expires' => $this->when(
                $this->ends_at && !$this->hasExpired(),
                $this->ends_at->diffInDays(now())
            ),
        ];
    }

    public function getStats()
    {
        return [
            'total_subscriptions' => $this->model->count(),
            'active_subscriptions' => $this->model->where('status', 'active')->count(),
            'inactive_subscriptions' => $this->model->where('status', 'inactive')->count(),
            'canceled_subscriptions' => $this->model->where('status', 'canceled')->count(),
            'expired_subscriptions' => $this->model->where('status', 'expired')->count(),
            'this_month_subscriptions' => $this->model->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'last_month_subscriptions' => $this->model->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count(),
        ];
    }

    /**
     * Get revenue statistics
     */
    public function getRevenueStats()
    {
        return [
            'total_revenue' => $this->model->where('status', 'active')->sum('amount'),
            'monthly_revenue' => $this->model->where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'yearly_revenue' => $this->model->where('status', 'active')
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'average_subscription_value' => $this->model->where('status', 'active')->avg('amount'),
        ];
    }
}
