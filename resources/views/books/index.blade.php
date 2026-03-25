@extends('layouts.app')

@section('page-title', 'Book')
@section('page-path')
    <ul class="breadcrumbs mb-3">
        <li class="nav-book">
            <a href="#">
                <i class="icon-book-open"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="#">Book</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('books.index') }}">All Books</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">All Books</h4>
                        <a href="{{ route('books.create') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Add Book
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                        {{ $dataTable->table(['class' => 'table table-striped text-nowrap']) }}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{ $dataTable->scripts() }}
@endpush