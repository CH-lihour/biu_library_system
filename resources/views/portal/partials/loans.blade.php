@if ($loans->isEmpty())
    <p class="portal-empty">{{ $empty }}</p>
@else
    <div class="portal-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Book</th>
                    <th>Barcode</th>
                    <th>Borrowed</th>
                    <th>Due</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($loans as $loan)
                    <tr>
                        <td><strong>{{ $loan->bookCopy?->book?->title ?? '-' }}</strong></td>
                        <td>{{ $loan->bookCopy?->barcode ?? '-' }}</td>
                        <td>{{ format_date($loan->borrow_date) }}</td>
                        <td class="{{ $loan->isOverdue() ? 'text-danger fw-bold' : '' }}">{{ format_date($loan->due_date) }}</td>
                        <td>
                            @if ($loan->return_date)
                                {!! status_label('returned') !!} {{ format_date($loan->return_date) }}
                            @else
                                {!! status_label($loan->isOverdue() ? 'overdue' : 'borrowed') !!}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
