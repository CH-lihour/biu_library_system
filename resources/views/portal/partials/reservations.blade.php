@if ($reservations->isEmpty())
    <p class="portal-empty">{{ $empty }}</p>
@else
    <div class="portal-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Reserved</th>
                    <th>Status</th>
                    <th>Collect before</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($reservations as $reservation)
                    <tr>
                        <td><strong>{{ $reservation->book?->title ?? '-' }}</strong></td>
                        <td>{{ format_date($reservation->created_at) }}</td>
                        <td>{!! status_label($reservation->displayStatus()) !!}</td>
                        <td>{{ $reservation->displayStatus() === 'ready' ? format_date_time($reservation->expires_at, 'd-M-Y H:i') : '-' }}</td>
                        <td class="text-end">
                            @if ($reservation->status === 'pending')
                                <form action="{{ route('portal.reservations.cancel', $reservation->id) }}" method="POST"
                                    onsubmit="return confirm('Cancel this reservation?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
