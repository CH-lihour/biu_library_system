@extends('layouts.app')

@section('page-title', 'Update Author')
@section('page-path')
    <ul class="breadcrumbs mb-3">
        <li class="nav-book">
            <a href="{{ route('authors.index') }}">
                <i class="fas fa-user-alt"></i>
            </a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="">Book</a>
        </li>
        <li class="separator">
            <i class="icon-arrow-right"></i>
        </li>
        <li class="nav-item">
            <a href="{{ route('authors.index') }}">Update Authors</a>
        </li>
    </ul>
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Update Author</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('authors.update', $author->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 pe-5 form-group">
                                <label for="firstname">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="firstname" class="form-control" id="firstname"
                                    value="{{ old('firstname', $author->firstname) }}" placeholder="Enter first name" />
                                <span class="text-danger">{{ $errors->first('firstname') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="lastname">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="lastname" class="form-control" id="lastname"
                                    value="{{ old('lastname', $author->lastname) }}" placeholder="Enter last name" />
                                <span class="text-danger">{{ $errors->first('lastname') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 pe-5 form-group">
                                <label for="email">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" id="email" value="{{ old('email', $author->email) }}"
                                    placeholder="Enter email" />
                                <span class="text-danger">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="bio">Bio</label>
                                <textarea name="bio" class="form-control" id="bio"
                                    placeholder="Enter bio">{{ old('bio', $author->bio) }}</textarea>
                                <span class="text-danger">{{ $errors->first('bio') }}</span>
                            </div>
                            <div class="card-action w-100">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('authors.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
