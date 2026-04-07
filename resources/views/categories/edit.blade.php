@extends('layouts.app')

@section('page-title', 'Category')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Update Category</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('categories.update', $category->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $category->name) }}"
                                    placeholder="Enter name" />
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="slug">Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" class="form-control" id="slug" value="{{ old('slug', $category->slug) }}"
                                    placeholder="Enter slug" />
                                <span class="text-danger">{{ $errors->first('slug') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description"
                                    placeholder="Enter description">{{ old('description', $category->description) }}</textarea>
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-action w-100">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('categories.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
