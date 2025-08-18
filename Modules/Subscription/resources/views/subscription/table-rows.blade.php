@foreach ($subscriptions as $index => $subscription)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>
        <div>
            <strong>{{ $subscription->user->name ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $subscription->user->email ?? 'N/A' }}</small>
        </div>
    </td>
    <td>
        @if($subscription->plan)
            <div>
                <strong>{{ $subscription->plan->name }}</strong><br>
                <small class="text-muted">{{ $subscription->plan->formatted_sell_price }}/{{ $subscription->plan->interval_display }}</small>
            </div>
        @else
            <span class="text-muted">Plan Deleted</span>
        @endif
    </td>
    <td>
        @if ($subscription->status == 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>
    <td>
        @switch($subscription->stripe_status)
            @case('active')
                <span class="badge bg-success">Active</span>
                @break
            @case('canceled')
                <span class="badge bg-danger">Canceled</span>
                @break
            @case('incomplete')
                <span class="badge bg-warning">Incomplete</span>
                @break
            @case('past_due')
                <span class="badge bg-warning">Past Due</span>
                @break
            @case('trialing')
                <span class="badge bg-info">Trialing</span>
                @break
            @default
                <span class="badge bg-secondary">{{ ucfirst($subscription->stripe_status) }}</span>
        @endswitch
    </td>
    <td>
        @if($subscription->trial_ends_at)
            {{ $subscription->trial_ends_at->format('M d, Y') }}
            @if($subscription->isOnTrial())
                <br><small class="text-success">Active Trial</small>
            @endif
        @else
            <span class="text-muted">No Trial</span>
        @endif
    </td>
    <td>
        @if($subscription->ends_at)
            {{ $subscription->ends_at->format('M d, Y') }}
            @if($subscription->hasExpired())
                <br><small class="text-danger">Expired</small>
            @endif
        @else
            <span class="text-muted">No End Date</span>
        @endif
    </td>
    <td>
        @if($subscription->card_brand && $subscription->card_last_four)
            <div>
                <i class="fab fa-cc-{{ strtolower($subscription->card_brand) }}"></i>
                ••••{{ $subscription->card_last_four }}
            </div>
            @if($subscription->card_expiration)
                <small class="text-muted">{{ $subscription->card_expiration }}</small>
            @endif
        @else
            <span class="text-muted">No Card</span>
        @endif
    </td>
    <td>
        @if ($subscription->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $subscription->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $subscription->id }}" title="Delete Permanently">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <div class="btn-group" role="group">
                <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $subscription->id }}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-gradient-info btn-sm showBtn1" data-id="{{ $subscription->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                @if($subscription->isCanceled())
                    <button class="btn btn-gradient-success btn-sm reactivateBtn" data-id="{{ $subscription->id }}" title="Reactivate">
                        <i class="fas fa-play"></i>
                    </button>
                @else
                    <button class="btn btn-gradient-warning btn-sm cancelBtn" data-id="{{ $subscription->id }}" title="Cancel">
                        <i class="fas fa-pause"></i>
                    </button>
                @endif
                <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $subscription->id }}" title="Move to Trash">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </td>
</tr>
@endforeach