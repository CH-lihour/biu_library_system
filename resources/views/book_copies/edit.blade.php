@extends('layouts.app')

@section('page-title', 'Update Book Copy')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Update Book Copy</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('book-copies.update', $bookCopy->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Books <span class="text-danger">*</span></label>
                                <select name="book_id" id="book_id" class="form-select">
                                    <option disabled selected>-- Select Book --</option>
                                    @foreach ($books as $book)
                                        <option value="{{ $book->id }}" {{ old('book_id', $bookCopy->book_id) == $book->id ? 'selected' : '' }}>
                                            {{ $book->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('book_id') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="name">Status <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select">
                                    <option disabled {{ old('status', $bookCopy->status?->value ?? $bookCopy->status) ? '' : 'selected' }}>
                                        -- Select Status --
                                    </option>

                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" {{ old('status', $bookCopy->status?->value ?? $bookCopy->status) === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="text-danger">{{ $errors->first('status') }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="barcode">Barcode <span class="text-danger">*</span></label>
                                <input type="text" name="barcode" class="form-control" id="barcode"
                                    value="{{ old('barcode', $bookCopy->barcode) }}" placeholder="Enter barcode" />
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
