@extends('layouts.app')

@section('page-title', 'Add Book Copy')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Add Book Copy</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('book-copies.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Books <span class="text-danger">*</span></label>
                                <select name="book_id" id="book_id" class="form-select js-select2" data-placeholder="Select book" data-allow-clear="1">
                                    @foreach ($books as $book)
                                        <option value="{{ $book->id }}" {{ old('book_id') == $book->id ? 'selected' : '' }}>{{ $book->title }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('book_id') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                <input type="text" name="barcode" class="form-control" id="barcode"
                                    value="{{ old('barcode') }}" placeholder="Enter barcode" />
                                <span class="text-danger">{{ $errors->first('barcode') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="card-action w-100">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('book-copies.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
