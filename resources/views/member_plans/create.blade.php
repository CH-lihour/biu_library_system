@extends('layouts.app')

@section('page-title', 'Create New Member Plan')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Add Member Plan</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('member-plans.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}"
                                    placeholder="Enter member name" />
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="loan_duration_days">Loan Duration (Days) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="loan_duration_days" class="form-control" id="loan_duration_days"
                                    value="{{ old('loan_duration_days') }}" placeholder="Enter loan duration in days"
                                    min="1" step="1" />
                                <span class="text-danger">{{ $errors->first('loan_duration_days') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="fine_per_day">Fine Per Day ($)<span class="text-danger">*</span></label>
                                <input type="number" name="fine_per_day" class="form-control" id="fine_per_day"
                                    value="{{ old('fine_per_day') }}" placeholder="Enter fine amount per day" step="0.01" />
                                <span class="text-danger">{{ $errors->first('fine_per_day') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="discount_fee">Discount Fee ($)<span class="text-danger">*</span></label>
                                <input type="number" name="discount_fee" class="form-control" id="discount_fee"
                                    value="{{ old('discount_fee') }}" placeholder="Enter discount fee amount" step="0.01" />
                                <span class="text-danger">{{ $errors->first('discount_fee') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description"
                                    placeholder="Enter description">{{ old('description') }}</textarea>
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-action w-100">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('member-plans.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
