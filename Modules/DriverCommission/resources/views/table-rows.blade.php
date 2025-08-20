@foreach ($data as $index => $commission)
<tr>
    <td>
        <input type="checkbox" class="commission-checkbox" value="{{ $commission->id }}">
    </td>
    <td>{{ $index + 1 }}</td>
    <td>
        <div>
            <strong>{{ $commission->driver->name ?? 'N/A' }}</strong><br>
            <small class="text-muted">{{ $commission->driver->email ?? 'N/A' }}</small>
        </div>
    </td>
    <td>{!! $commission->commission_type_badge !!}</td>
    <td>
        <div>
            @if($commission->base_fare > 0)
                <strong>{{ $commission->formatted_base_fare }}</strong>
            @else
                <span class="text-muted">N/A</span>
            @endif
        </div>
    </td>
    <td>
        <div>
            <strong class="text-primary">{{ $commission->formatted_commission_amount }}</strong>
            @if($commission->commission_rate)
                <br><small class="text-muted">{{ $commission->commission_rate }}%</small>
            @endif
        </div>
    </td>
    <td>
        @if($commission->bonus_amount > 0)
            <strong class="text-success">{{ $commission->formatted_bonus_amount }}</strong>
            @if($commission->bonus_type)
                <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $commission->bonus_type)) }}</small>
            @endif
        @else
            <span class="text-muted">$0.00</span>
        @endif
    </td>
    <td>
        @if($commission->penalty_amount > 0)
            <strong class="text-danger">-{{ $commission->formatted_penalty_amount }}</strong>
            @if($commission->penalty_type)
                <br><small class="text-muted">{{ ucfirst(str_replace('_', ' ', $commission->penalty_type)) }}</small>
            @endif
        @else
            <span class="text-muted">$0.00</span>
        @endif
    </td>
    <td>
        <div>
            <strong>{{ $commission->formatted_earning_date }}</strong>
            @if($commission->rideAssignment)
                <br><small class="text-info">Ride: {{ Str::limit($commission->rideAssignment->ride_title, 20) }}</small>
            @endif
        </div>
    </td>
    <td>{!! $commission->payment_status_badge !!}</td>
    <td>
        @if ($commission->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $commission->id }}" title="Restore">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $commission->id }}" title="Delete Permanently">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <div class="btn-group" role="group">
                <button class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $commission->id }}" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-gradient-info btn-sm showBtn" data-id="{{ $commission->id }}" title="View Details">
                    <i class="fas fa-eye"></i>
                </button>
                
                @if($commission->isPending())
                    <button class="btn btn-gradient-success btn-sm markPaidBtn" data-id="{{ $commission->id }}" title="Mark as Paid">
                        <i class="fas fa-check-circle"></i>
                    </button>
                    <button class="btn btn-gradient-warning btn-sm markProcessingBtn" data-id="{{ $commission->id }}" title="Mark as Processing">
                        <i class="fas fa-clock"></i>
                    </button>
                    <button class="btn btn-gradient-danger btn-sm markFailedBtn" data-id="{{ $commission->id }}" title="Mark as Failed">
                        <i class="fas fa-times-circle"></i>
                    </button>
                @endif
                
                <button class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $commission->id }}" title="Move to Trash">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </td>
</tr>
@endforeach