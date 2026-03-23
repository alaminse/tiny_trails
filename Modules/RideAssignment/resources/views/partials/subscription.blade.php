@forelse($subscriptions as $key => $subscription)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $subscription->user->first_name ?? 'N/A' }}</td>
        <td>{{ $subscription->kid->first_name ?? 'N/A' }}</td>
        <td>{{ $subscription->plan->name ?? 'N/A' }}</td>
        <td>{{ $subscription->pickupLocation->address ?? 'N/A' }}</td>
        <td>{{ $subscription->dropoffLocation->address ?? 'N/A' }}</td>
        <td>
            @if($subscription->status === 'active')
                <span class="btn-sm btn btn-gradient-info">Active</span>
            @else
                <span class="btn-sm btn btn-gradient-danger">{{ ucfirst($subscription->status) }}</span>
            @endif
        </td>
        <td>
    @can('create-rideassign')
            <a href="{{ route('admin.ride.assign.create', ['subscription' => $subscription->id]) }}"
               class="btn btn-sm btn-gradient-success">Assign Ride</a>
                 @endcan
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No Unassigned Subscriptions Found</td>
    </tr>
@endforelse
