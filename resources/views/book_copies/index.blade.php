@extends('layouts.app')

@section('page-title', 'Book Copy')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">All Book Copies</h4>
                        <a href="{{ route('book-copies.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Book Copy
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
