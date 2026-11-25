@if ($reservation->status === 'pending' && can_access('reservations.ready', 'reservations.cancel'))
    @if ($reservation->displayStatus() === 'pending' && can_access('reservations.ready'))
        <form action="{{ route('reservations.ready', $reservation->id) }}" method="POST" style="display:inline-block;">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" title="Hold a copy for this member">Hold copy</button>
        </form>
    @endif
    @if (can_access('reservations.cancel'))
        <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" style="display:inline-block;"
            onsubmit="return confirm('Cancel this reservation?')">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
        </form>
    @endif
@else
    <span class="text-muted">-</span>
@endif
