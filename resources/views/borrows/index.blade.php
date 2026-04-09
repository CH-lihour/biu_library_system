@extends('layouts.app')

@section('page-title', 'Borrow Transactions')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">All Borrow Transactions</h4>
                        <a href="{{ route('borrows.create') }}" class="btn btn-primary btn-round">
                            <i class="fa fa-plus"></i>
                            Borrow Book
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
