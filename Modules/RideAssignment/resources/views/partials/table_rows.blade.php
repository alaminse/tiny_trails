@forelse($rides as $key => $ride)
    <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $ride->subscription->name ?? 'N/A' }}</td>
        <td>
            <span class="badge bg-primary">{{ ucfirst($ride->service_type) }}</span>
            <small class="text-muted d-block">{{ $ride->total_days }} days</small>
        </td>
        <td>AUD {{ number_format($ride->fare, 2) }}</td>
        <td>AUD {{ number_format($ride->driver_commission, 2) }}</td>
        <td>AUD {{ number_format($ride->platform_commission, 2) }}</td>
        <td>
            @if($ride->status === 'pending')
                <span class="badge bg-warning">Pending</span>
            @elseif($ride->status === 'active')
                <span class="badge bg-success">Active</span>
            @elseif($ride->status === 'completed')
                <span class="badge bg-info">Completed</span>
            @elseif($ride->status === 'cancelled')
                <span class="badge bg-danger">Cancelled</span>
            @endif
        </td>
        <td>
            @can('view-rideassign')
            <a href="{{ route('admin.ride.assign.show', $ride->id) }}"
               class="btn btn-sm btn-gradient-primary" title="View Details">
                <i class="fas fa-eye"></i>
            </a>
            @endcan
            @can('edit-rideassign')
            <a href="{{ route('admin.ride.assign.edit', $ride->id) }}"
               class="btn btn-sm btn-gradient-info" title="Edit">
                <i class="fas fa-edit"></i>
            </a>
            @endcan
            @if($ride->status !== 'completed')
                @can('delete-rideassign')
                <form action="{{ route('admin.ride.assign.destroy', $ride->id) }}" method="POST"
                      style="display:inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-gradient-danger"
                            onclick="return confirm('Are you sure you want to delete this ride assignment?')"
                            title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                @endcan
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="text-center">No Ride Assignments Found</td>
    </tr>
@endforelse
