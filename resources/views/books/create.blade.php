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
                            <div class="col-md-6">
                                <div class="col-md-12 form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        value="{{ old('title') }}" placeholder="Enter title" />
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                </div>

                                <div class="col-md-12 form-group">
                                    <label for="isbn">Is Bn <span class="text-danger">*</span></label>
                                    <input type="text" name="isbn" class="form-control" id="isbn" value="{{ old('isbn') }}"
                                        placeholder="Enter is bn" />
                                    <span class="text-danger">{{ $errors->first('isbn') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="categories">Category <span class="text-danger">*</span></label>
                                    <select name="category_id" class="form-select js-select2" id="categories"
                                        data-placeholder="Select category" data-allow-clear="1">
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('category_id') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="author">Author </label>
                                    <select name="author_ids[]" id="author_id" class="form-select js-select2"
                                        data-placeholder="Select authors" data-allow-clear="1" multiple>
                                        @foreach ($authors as $author)
                                            <option value="{{ $author->id }}" {{ (collect(old('author_ids'))->contains($author->id)) ? 'selected' : '' }}>
                                                {{ $author->firstname }} {{ $author->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('author_ids') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="publishers">Publishers <span class="text-danger">*</span></label>
                                    <select name="publisher_id" class="form-select js-select2" id="publishers"
                                        data-placeholder="Select publisher" data-allow-clear="1">
                                        @foreach ($publishers as $pub)
                                            <option value="{{ $pub->id }}" {{ old('publisher_id') == $pub->id ? 'selected' : '' }}>{{ $pub->name }} </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('publisher_id') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="publish_year">Publish Year </label>
                                    <input type="number" name="publish_year" class="form-control" id="publish_year"
                                        value="{{ old('publish_year') }}" placeholder="Enter publish year" />
                                    <span class="text-danger">{{ $errors->first('publish_year') }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="col-md-12 form-group">
                                    <label for="pages">Page </label>
                                    <input type="number" name="pages" class="form-control" id="pages"
                                        value="{{ old('pages') }}" placeholder="Enter pages" />
                                    <span class="text-danger">{{ $errors->first('pages') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="language">Language </label>
                                    <input type="text" name="language" class="form-control" id="language"
                                        value="{{ old('language') }}" placeholder="Enter book's language" />
                                    <span class="text-danger">{{ $errors->first('language') }}</span>
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="shelf_location">Shelf Location </label>
                                    <input type="text" name="shelf_location" class="form-control" id="shelf_location"
                                        value="{{ old('shelf_location') }}" placeholder="Enter shelf location" />
                                    <span class="text-danger">{{ $errors->first('shelf_location') }}</span>
                                </div>

                                {{-- ===== Cover Image Upload ===== --}}
                                <div class="col-md-12 form-group">
                                    <label class="upload-label" for="cover_image_url">Cover Image</label>

                                    <div class="upload-zone" id="uploadZone">
                                        <input type="file" name="cover_image_url" id="cover_image_url" accept="image/*" />
                                        <div class="upload-icon">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                <polyline points="17 8 12 3 7 8" />
                                                <line x1="12" y1="3" x2="12" y2="15" />
                                            </svg>
                                        </div>
                                        <p class="upload-title">Drag & drop your image here</p>
                                        <p class="upload-sub">or <span>browse files</span></p>
                                        <p class="upload-sub upload-hint">PNG, JPG, WEBP up to 10MB</p>
                                    </div>

                                    {{-- preview block --}}
                                    <div class="preview-wrap" id="previewWrap" style="display:none;">
                                        <div class="preview-frame">
                                            <img id="image_preview" src="#" alt="Image Preview" />
                                            <div class="preview-overlay">
                                                <p class="preview-name" id="previewName"></p>
                                                <button class="preview-remove" id="removeBtn" type="button">
                                                    <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" fill="none" width="13" height="13">
                                                        <line x1="18" y1="6" x2="6" y2="18" />
                                                        <line x1="6" y1="6" x2="18" y2="18" />
                                                    </svg>
                                                    Change image
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @error('cover_image_url')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                {{-- ===== End Cover Image Upload ===== --}}

                            </div>
                        </div>
                        <div class="row">
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
@endsection

{{-- Cover Image Styles --}}
@push('styles')
    <style>
        .upload-label {
            font-size: 13px;
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 8px;
            display: block;
        }

        .upload-zone {
            border: 1.5px dashed #d1d5db;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            background: #f9fafb;
            position: relative;
        }

        .upload-zone:hover,
        .upload-zone.drag-over {
            border-color: #9ca3af;
            background: #fff;
        }

        .upload-zone input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
        }

        .upload-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 12px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
        }

        .upload-title {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            margin: 0 0 4px;
        }

        .upload-sub {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }

        .upload-sub span {
            color: #3b82f6;
            text-decoration: underline;
        }

        .upload-hint {
            margin-top: 6px !important;
            font-size: 12px !important;
        }

        .preview-wrap {
            margin-top: 1rem;
        }

        .preview-frame {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e5e7eb;
            background: #f3f4f6;
        }

        .preview-frame img {
            width: 100%;
            height: 260px;
            object-fit: contain;
            display: block;
        }

        .preview-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 14px;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.65) 0%, transparent 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .preview-name {
            font-size: 12px;
            color: #fff;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 65%;
        }

        .preview-remove {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #fff;
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background .2s;
            white-space: nowrap;
        }

        .preview-remove:hover {
            background: rgba(255, 255, 255, 0.28);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function showPreview(file) {
            if (!file || !file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = e => {
                $('#image_preview').attr('src', e.target.result);
                $('#previewWrap').show();
                $('#uploadZone').hide();
                $('#previewName').text(file.name + ' — ' + (file.size / 1024).toFixed(0) + ' KB');
            };
            reader.readAsDataURL(file);
        }

        $('#cover_image_url').on('change', function () {
            if (this.files[0]) showPreview(this.files[0]);
        });

        $('#uploadZone').on('dragover', function (e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        }).on('dragleave', function () {
            $(this).removeClass('drag-over');
        }).on('drop', function (e) {
            e.preventDefault();
            $(this).removeClass('drag-over');
            const file = e.originalEvent.dataTransfer.files[0];
            if (file) showPreview(file);
        });

        $('#removeBtn').on('click', function () {
            $('#cover_image_url').val('');
            $('#image_preview').attr('src', '#');
            $('#previewWrap').hide();
            $('#uploadZone').show();
        });
    </script>
@endpush
