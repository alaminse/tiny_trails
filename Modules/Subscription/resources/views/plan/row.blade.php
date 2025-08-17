@foreach ($plans as $index => $plan)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>
        <div class="d-flex align-items-center">
            <div>
                <strong>{{ $plan->name }}</strong>
                <br>
                <small class="text-muted">{{ $plan->slug }}</small>
            </div>
        </div>
    </td>
    <td>
        <div>
            @if($plan->price != $plan->sell_price)
                <span class="text-muted text-decoration-line-through">{{ $plan->currency }} {{ number_format($plan->price, 2) }}</span><br>
            @endif
            <strong class="text-primary">{{ $plan->currency }} {{ number_format($plan->sell_price, 2) }}</strong>
        </div>
    </td>
    <td>{{ $plan->currency }}</td>
    <td>
        <span class="badge {{ $plan->interval_display }} text-dark">
            {{ $plan->interval_display }}
        </span>
    </td>
    <td>
        @if($plan->pickupType)
            <span class="badge bg-info">{{ $plan->pickupType->name }}</span>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>
    <td>
        @if ($plan->status == 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">Inactive</span>
        @endif
    </td>
    <td>
        @if ($plan->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $plan->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $plan->id }}" title="Delete Permanently">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <div class="btn-group" role="group">
                <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $plan->id }}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-gradient-info btn-sm showBtn" data-id="{{ $plan->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-gradient-warning btn-sm duplicateBtn" data-id="{{ $plan->id }}" title="Duplicate">
                    <i class="fas fa-copy"></i>
                </button>
                <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $plan->id }}" title="Move to Trash">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </td>
</tr>
@endforeach