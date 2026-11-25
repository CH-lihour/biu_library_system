@extends('layouts.app')

@section('page-title', 'Staff Accounts')
@section('page-subtitle', 'People who can sign in to the admin panel.')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">All Staff Accounts</h4>
                        @if (can_access('users.create'))
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Add Staff
                        </a>
                        @endif
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
