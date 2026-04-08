@extends('layouts.app')

@section('page-title', 'Create New Member')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Add Member</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('members.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="member_code">Member Code <span class="text-danger">*</span></label>
                                <input type="text" name="member_code" class="form-control" id="member_code"
                                    value="{{ $memberCode }}" placeholder="Enter member code" disabled />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="plan">Plan <span class="text-danger">*</span></label>
                                <select name="plan_id" id="plan_id" class="form-select">
                                    <option value="">Select Plan</option>
                                    @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ old('plan_id')==$plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }} ({{ $plan->loan_duration_days }} days)
                                    </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('plan_id') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="join_date">Join Date <span class="text-danger">*</span></label>
                                <input type="date" name="join_date" class="form-control" id="join_date"
                                    value="{{ old('join_date', now()->format('Y-m-d')) }}" />
                                <span class="text-danger">{{ $errors->first('join_date') }}</span>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="expiry_date">Expiry Date <span class="text-danger">*</span></label>
                                <input type="date" name="expiry_date" class="form-control" id="expiry_date"
                                    value="{{ old('expiry_date', now()->addDays(30)->format('Y-m-d')) }}" />
                                <span class="text-danger">{{ $errors->first('expiry_date') }}</span>
                            </div>

                        </div>
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
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}"
                                    placeholder="Enter email address" />
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="phone">Phone Number</label>
                                <input type="number" name="phone" class="form-control" id="phone" value="{{ old('phone') }}"
                                    placeholder="Enter phone number" />
                                <span class="text-danger">{{ $errors->first('phone') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="address">Address</label>
                                <textarea name="address" class="form-control" id="address"
                                    placeholder="Enter address">{{ old('address') }}</textarea>
                                <span class="text-danger">{{ $errors->first('address') }}</span>
                            </div>
                            <div class="card-action w-100">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('members.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
