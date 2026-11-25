@extends('layouts.app')

@section('page-title', 'Add Staff Account')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">New Staff Account</h4>
                    </div>
                </div>
                <div class="card-body">
                    @include('users.form')
                </div>
            </div>
        </div>
    </div>
@endsection
