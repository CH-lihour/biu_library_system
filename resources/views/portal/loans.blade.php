@extends('layouts.portal')

@section('title', 'My Loans')
@section('content')
    <div class="portal-head">
        <div>
            <h1>My Loans</h1>
            <p>Bring books back to the library desk. Staff confirm the return.</p>
        </div>
    </div>

    <section class="portal-card">
        <h2>On loan now</h2>
        @include('portal.partials.loans', ['loans' => $current, 'empty' => 'You have no books on loan.'])
    </section>

    <section class="portal-card">
        <h2>History</h2>
        @include('portal.partials.loans', ['loans' => $history, 'empty' => 'Books you return will be listed here.'])
        <div class="portal-pages">{{ $history->links('pagination::bootstrap-5') }}</div>
    </section>
@endsection
