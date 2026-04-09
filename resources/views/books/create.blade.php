@extends('layouts.app')

@section('page-title', 'New Book')
@section('page-path')
<ul class="breadcrumbs mb-3">
    <li class="nav-book">
        <a href="#">
            <i class="icon-book-open"></i>
        </a>
    </li>
    <li class="separator">
        <i class="icon-arrow-right"></i>
    </li>
    <li class="nav-item">
        <a href="{{ route('books.index') }}">Book</a>
    </li>
    <li class="separator">
        <i class="icon-arrow-right"></i>
    </li>
    <li class="nav-item">
        <a href="{{ route('books.create') }}">Create New Book</a>
    </li>
</ul>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <h4 class="card-title">Add Books</h4>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('books.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 pe-5 form-group">
                            <label for="title">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" id="title" value="{{ old('title') }}" placeholder="Enter title" />
                            <span class="text-danger">{{ $errors->first('title') }}</span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="isbn">Is Bn <span class="text-danger">*</span></label>
                            <input type="text" name="isbn" class="form-control" id="isbn" value="{{ old('isbn') }}" placeholder="Enter is bn" />
                            <span class="text-danger">{{ $errors->first('isbn') }}</span>
                        </div>

                        <div class="col-md-6 pe-5 form-group">
                            <label for="publishers">Publishers <span class="text-danger">*</span></label>
                            <select name="publisher_id" class="form-select js-select2" id="publishers" data-placeholder="Select publisher" data-allow-clear="1">
                                @foreach ($publishers as $pub)
                                    <option value="{{ $pub->id }}" {{ old('publisher_id') == $pub->id ? 'selected' : '' }}>{{ $pub->name }} </option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{ $errors->first('publisher_id') }}</span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="categories">Category <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select js-select2" id="categories" data-placeholder="Select category" data-allow-clear="1">
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }} </option>
                                @endforeach
                            </select>
                            <span class="text-danger">{{ $errors->first('category_id') }}</span>
                        </div>

                        <div class="col-md-6 pe-5 form-group">
                            <label for="publish_year">Publish Year </label>
                            <input type="number" name="publish_year" class="form-control" id="pages" value="{{ old('publish_year') }}" placeholder="Enter publish year" />
                            <span class="text-danger">{{ $errors->first('publish_year') }}</span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="pages">Page </label>
                            <input type="number" name="pages" class="form-control" id="pages" value="{{ old('pages') }}" placeholder="Enter pages" />
                            <span class="text-danger">{{ $errors->first('pages') }}</span>
                        </div>

                        <div class="col-md-6 pe-5 form-group">
                            <label for="language">Language </label>
                            <input type="text" name="language" class="form-control" id="language" value="{{ old('language') }}" placeholder="Enter book's language" />
                            <span class="text-danger">{{ $errors->first('language') }}</span>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="shelf_location">Shelf Location </label>
                            <input type="text" name="shelf_location" class="form-control" id="shelft_location" value="{{ old('shelf_location') }}" placeholder="Enter shelf location" />
                            <span class="text-danger">{{ $errors->first('shelf_location') }}</span>
                        </div>

                        <div class="col-md-12 form-group">
                            <label for="cover_image_url">Cover Image</label>
                            <br/>
                            <input type="file" name="cover_image_url" class="form-control-file" id="cover_image_url" accept="image/*"/>
                            <div class="mt-3">
                                <img id="image_preview" src="#" alt="Image Preview" style="max-width: 200px; display: none; border-radius: 8px;" />
                            </div>
                            <span class="text-danger">{{ $errors->first('cover_image_url') }}</span>
                        </div>
                        <div class="card-action w-100">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <a href="{{ route('books.index') }}" class="btn btn-danger">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('cover_image_url').addEventListener('change', function(event) {
        const [file] = event.target.files;
        const preview = document.getElementById('image_preview');

        if (file) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.src = '#';
            preview.style.display = 'none';
        }
    });
</script>
@endsection
