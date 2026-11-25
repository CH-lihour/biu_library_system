@extends('layouts.portal')

@section('title', 'Reservations')
@section('content')
    <div class="portal-head">
        <div>
            <h1>Reservations</h1>
            <p>When a book is ready, collect it at the desk before the hold ends.</p>
        </div>
        <a href="{{ route('portal.catalog') }}" class="btn btn-primary">Find a book</a>
    </div>

    <section class="portal-card">
        <h2>Open</h2>
        @include('portal.partials.reservations', ['reservations' => $open, 'empty' => 'You have no open reservations.'])
    </section>

    <section class="portal-card">
        <h2>Closed</h2>
        @include('portal.partials.reservations', ['reservations' => $closed, 'empty' => 'Closed reservations will be listed here.'])
    </section>
@endsection
