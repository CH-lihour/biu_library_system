@extends('layouts.portal')

@section('title', 'Home')
@section('content')
    <div class="portal-head">
        <div>
            <h1>Welcome, {{ $member->name }}</h1>
            <p>
                Member {{ $member->member_code }}
                @if ($member->plan) &middot; {{ $member->plan->name }} @endif
                @if ($member->expiry_date) &middot; membership until {{ format_date($member->expiry_date) }} @endif
            </p>
        </div>
        <a href="{{ route('portal.catalog') }}" class="btn btn-primary">Search catalog</a>
    </div>

    <div class="portal-tiles">
        @foreach ([
            ['On loan', $loans->count(), ''],
            ['Overdue', $overdue, $overdue ? 'danger' : ''],
            ['Fines so far', format_currency($fines), $fines > 0 ? 'danger' : ''],
            ['Reservations', $reservations->count(), ''],
        ] as [$label, $value, $tone])
            <div class="portal-card portal-tile">
                <span>{{ $label }}</span>
                <strong class="{{ $tone ? 'text-' . $tone : '' }}">{{ $value }}</strong>
            </div>
        @endforeach
    </div>

    <section class="portal-card">
        <h2>Books you have now</h2>
        @include('portal.partials.loans', ['loans' => $loans, 'empty' => 'You have no books on loan.'])
    </section>

    <section class="portal-card">
        <h2>Your reservations</h2>
        @include('portal.partials.reservations', ['reservations' => $reservations, 'empty' => 'You have no open reservations.'])
    </section>
@endsection
