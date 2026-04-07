@extends('layouts.app')

@section('page-title', 'Categories')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">All Categories</h4>
                        <a href="{{ route('categories.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Category
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
