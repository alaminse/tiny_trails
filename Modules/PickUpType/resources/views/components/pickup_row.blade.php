@foreach ($data as $index => $pickup)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $pickup->name }}</td>
    <td>{{ number_format($pickup->amount, 2) }} AUD</td>
    <td>{{ $pickup->min_notice_minutes }}</td>
    <td>
        @if ($pickup->requires_instant_notification)
            <span class="badge btn-gradient-success">Yes</span>
        @else
            <span class="badge btn-gradient-danger">No</span>
        @endif
    </td>
    <td>
        @if ($pickup->status === 'active')
            <span class="badge btn-gradient-success">Active</span>
        @else
            <span class="badge bg-warning text-dark">Inactive</span>
        @endif
    </td>
    <td>
        @if ($pickup->trashed())
            <button class="btn btn-gradient-info btn-sm restoreBtn" data-id="{{ $pickup->id }}">
                <i class="fas fa-undo"></i>
            </button>
            <button class="btn btn-gradient-danger btn-sm forceDeleteBtn" data-id="{{ $pickup->id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        @else
            <a href="#" class="btn btn-gradient-primary btn-sm editBtn" data-id="{{ $pickup->id }}">
                <i class="fas fa-edit"></i>
            </a>
            <a href="#" class="btn btn-gradient-danger btn-sm deleteBtn" data-id="{{ $pickup->id }}">
                <i class="fas fa-trash"></i>
            </a>
        @endif
    </td>
</tr>
@endforeach

