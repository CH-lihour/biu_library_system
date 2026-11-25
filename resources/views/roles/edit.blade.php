@extends('layouts.app')

@section('page-title', 'Update Role')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Update Role</h4>
                    </div>
                </div>
                <div class="card-body">
                    @include('roles.form')
                </div>
            </div>
        </div>
    </div>
@endsection
