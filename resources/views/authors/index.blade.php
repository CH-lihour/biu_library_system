@extends('layouts.app')

@section('page-title', 'Author')
@section('page-path')
    <ul class="breadcrumbs mb-3">
        <li class="nav-book">
            <a href="{{ route('authors.index') }}">
                <i class="fas fa-user-alt"></i>
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
            <a href="{{ route('authors.index') }}">Authors</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">Authors</h4>
                        <a href="{{ route('authors.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Add Author
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
