@extends('layouts.portal')

@section('title', 'Catalog')
@section('content')
    <div class="portal-head">
        <div>
            <h1>Catalog</h1>
            <p>{{ format_number($books->total()) }} {{ \Illuminate\Support\Str::plural('title', $books->total()) }} found</p>
        </div>
    </div>

    <form method="GET" action="{{ route('portal.catalog') }}" class="portal-search" role="search">
        <label class="visually-hidden" for="search">Search</label>
        <input type="search" id="search" name="search" class="form-control" maxlength="100" value="{{ $filters['search'] ?? '' }}"
            placeholder="Search by title, author or ISBN">
        <label class="visually-hidden" for="category">Category</label>
        <select name="category" id="category" class="form-select">
            <option value="">All categories</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected(($filters['category'] ?? null) == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    @if ($books->isEmpty())
        <div class="portal-card"><p class="portal-empty">No books match your search. Try another word or category.</p></div>
    @else
        <div class="portal-grid">
            @foreach ($books as $book)
                <a href="{{ route('portal.book', $book->id) }}" class="portal-card portal-book">
                    <span class="portal-cover">
                        <img src="{{ $book->cover_image_url ? asset('storage/' . $book->cover_image_url) : asset('assets/img/books/no_image.png') }}" alt="" loading="lazy">
                    </span>
                    <strong>{{ $book->title }}</strong>
                    <span class="portal-muted">{{ $book->authors->pluck('full_name')->join(', ') ?: 'Unknown author' }}</span>
                    <span @class(['badge', 'bg-success' => $book->freeCopies(), 'bg-warning' => ! $book->freeCopies()])>
                        {{ $book->freeCopies() ? $book->freeCopies() . ' available' : 'None available' }}
                    </span>
                </a>
            @endforeach
        </div>
        <div class="portal-pages">{{ $books->links('pagination::bootstrap-5') }}</div>
    @endif
@endsection
