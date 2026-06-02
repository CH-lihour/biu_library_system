<x-dropdown-actions
    :uid="$uid ?? \Illuminate\Support\Str::uuid()->toString()"
    :items="[
        [
            'label' => 'Return',
            'route' => route('borrows.return', $row->id),
            'method' => 'POST',
            'danger' => false,
        ]
    ]"
/>
{{-- <x-dropdown-actions
    :uid="$uid ?? \Illuminate\Support\Str::uuid()->toString()"
    :items="[
        [
            'label' => 'Overdue',
            'route' => route('borrows.overdue', $row->id),
            'method' => 'POST',
            'danger' => false,
        ]
    ]"
/> --}}
