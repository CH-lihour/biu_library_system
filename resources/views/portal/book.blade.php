@extends('layouts.portal')

@section('title', $book->title)
@section('content')
    <p><a href="{{ route('portal.catalog') }}">&larr; Back to catalog</a></p>

    <section class="portal-card portal-detail">
        <span class="portal-cover">
            <img src="{{ $book->cover_image_url ? asset('storage/' . $book->cover_image_url) : asset('assets/img/books/no_image.png') }}" alt="">
        </span>
        <div>
            <h1>{{ $book->title }}</h1>
            <p class="portal-muted">{{ $book->authors->pluck('full_name')->join(', ') ?: 'Unknown author' }}</p>

            <dl class="portal-facts">
                @foreach ([
                    'Category' => $book->category?->name,
                    'Publisher' => $book->publisher?->name,
                    'Year' => $book->publish_year,
                    'Pages' => $book->pages,
                    'Language' => $book->language,
                    'ISBN' => $book->isbn,
                    'Shelf' => $book->shelf_location,
                    'Copies' => $book->freeCopies() . ' of ' . $book->copies_count . ' available',
                ] as $label => $value)
                    <div>
                        <dt>{{ $label }}</dt>
                        <dd>{{ $value ?: '-' }}</dd>
                    </div>
                @endforeach
            </dl>

            @if ($reservation)
                <p>{!! status_label($reservation->displayStatus()) !!} You already reserved this book.
                    <a href="{{ route('portal.reservations') }}">See my reservations</a></p>
            @elseif ($member->blockedReason())
                <button type="button" class="btn btn-primary" disabled>Reserve</button>
            @else
                <form action="{{ route('portal.reserve', $book->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Reserve</button>
                </form>
                <p class="portal-muted mt-2">
                    {{ $book->freeCopies() ? 'We set a copy aside and hold it for ' . config('library.hold_days') . ' days. Collect it at the desk.' : 'All copies are out. You join the queue and we hold the next one for you.' }}
                </p>
            @endif
        </div>
    </section>
@endsection
