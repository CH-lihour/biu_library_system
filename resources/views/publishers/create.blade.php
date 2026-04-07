@extends('layouts.app')

@section('page-title', 'Add Publisher')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Add Publisher</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('publishers.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name') }}" placeholder="Enter name" />
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}"
                                    placeholder="Enter email" />
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" id="phone" value="{{ old('phone') }}"
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
                                <a href="{{ route('publishers.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
