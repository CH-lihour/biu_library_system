@extends('layouts.app')

@section('page-title', 'Roles')
@section('page-subtitle', 'Create a role, tick its permissions, then give it to staff accounts.')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">All Roles</h4>
                        @if (can_access('roles.create'))
                        <a href="{{ route('roles.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Add Role
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
