@extends('layouts.app')

@section('page-title', 'Borrow Book Transaction')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Borrow Book</h4>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('borrows.store') }}" method="post">
                        @csrf
                        <input type="hidden" name="book_copy_id" id="book_copy_id" value="{{ old('book_copy_id') }}">
                        <div class="row form-group">
                            <label class="col-md-2 align-self-center" for="barcode">Barcode <span class="text-danger">*</span></label>
                            <div class="col-md-7">
                                <input type="text" name="barcode" class="form-control" id="barcode"
                                    value="{{ old('barcode') }}" placeholder="Enter barcode" style="background-color: rgb(223, 222, 222)"/>
                                    <span class="text-danger d-block mt-1">{{ $errors->first('barcode') }}</span>
                            </div>
                            <button type="button" class="col-md-1 offset-1 btn btn-dark" id="searchBook">Search Book</button>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div id="bookInfoCard" class="card border shadow-sm d-none">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Book Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row align-items-start">
                                            <div class="col-md-3 text-center mb-3 mb-md-0">
                                                <img id="bookInfoCover" src="" alt="Book cover"
                                                    class="img-fluid rounded border d-none"
                                                    style="max-height: 220px; object-fit: cover;">
                                            </div>
                                            <div class="col-md-9">
                                                <h4 id="bookInfoTitle" class="mb-3"></h4>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-2"><strong>Barcode:</strong> <span id="bookInfoBarcode"></span></p>
                                                        <p class="mb-2"><strong>ISBN:</strong> <span id="bookInfoIsbn"></span></p>
                                                        <p class="mb-2"><strong>Authors:</strong> <span id="bookInfoAuthors"></span></p>
                                                        <p class="mb-2"><strong>Publisher:</strong> <span id="bookInfoPublisher"></span></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2"><strong>Category:</strong> <span id="bookInfoCategory"></span></p>
                                                        <p class="mb-2"><strong>Publish Year:</strong> <span id="bookInfoPublishYear"></span></p>
                                                        <p class="mb-2"><strong>Pages:</strong> <span id="bookInfoPages"></span></p>
                                                        <p class="mb-2"><strong>Language:</strong> <span id="bookInfoLanguage"></span></p>
                                                        <p class="mb-0"><strong>Shelf:</strong> <span id="bookInfoShelf"></span></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="{{ $errors->any() ? '' : 'd-none' }}" id="form-body">
                            <div class="row form-group">
                                <label class="col-md-2" for="member">Member <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <select name="member_id" id="member_id" class="form-select js-select2" data-placeholder="Select member" data-allow-clear="1">
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id') == $member->id ? 'selected' : '' }}>
                                                {{ $member->name }} ({{ $member->member_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger d-block mt-1">{{ $errors->first('member_id') }}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-2" for="borrow_date">Borrow Date <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="borrow_date" class="form-control" id="borrow_date"
                                        value="{{ old('borrow_date', now()->format('Y-m-d')) }}" />
                                        <span class="text-danger d-block mt-1">{{ $errors->first('borrow_date') }}</span>
                                </div>
                            </div>
                            <div class="row form-group">
                                <label class="col-md-2" for="due_date">Due Date <span class="text-danger">*</span></label>
                                <div class="col-md-6">
                                    <input type="date" name="due_date" class="form-control" id="due_date"
                                        value="{{ old('due_date', now()->format('Y-m-d')) }}" />
                                        <span class="text-danger d-block mt-1">{{ $errors->first('due_date') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="card-action w-100 {{ $errors->any() ? '' : 'd-none' }}">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <a href="{{ route('borrows.index') }}" class="btn btn-danger">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const barcodeInput = $('#barcode');
        const bookInfoCard = $('#bookInfoCard');
        const bookInfoCover = $('#bookInfoCover');
        const cardAction = $('.card-action');
        const searchButton = $('#searchBook');
        const formBody = $('#form-body');
        const oldBarcode = @json(old('barcode'));

        barcodeInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                getBookByBarcode();
            }

        });

        searchButton.on('click', function() {
            getBookByBarcode()
        });

        $(document).ready(function () {
            if (oldBarcode && oldBarcode.trim() !== '') {
                getBookByBarcode();
            }
        });

        function fillBookInfo(book) {
            $('#book_copy_id').val(book.book_copy_id);

            $('#bookInfoTitle').text(book.title || '-');
            $('#bookInfoBarcode').text(book.barcode || '-');
            $('#bookInfoIsbn').text(book.isbn || '-');
            $('#bookInfoAuthors').text(book.authors || '-');
            $('#bookInfoPublisher').text(book.publisher || '-');
            $('#bookInfoCategory').text(book.category || '-');
            $('#bookInfoPublishYear').text(book.publish_year || '-');
            $('#bookInfoPages').text(book.pages || '-');
            $('#bookInfoLanguage').text(book.language || '-');
            $('#bookInfoShelf').text(book.shelf_location || '-');

            if (book.cover_image_url) {
                bookInfoCover.attr('src', '/storage/' + book.cover_image_url).removeClass('d-none');
            } else {
                bookInfoCover.attr('src', '/assets/img/books/no_cover.jpg').removeClass('d-none');
            }

            bookInfoCard.removeClass('d-none');
            cardAction.removeClass('d-none');
            formBody.removeClass('d-none');
        }

        function resetBookInfo() {
            cardAction.addClass('d-none');
            formBody.addClass('d-none');
            bookInfoCard.addClass('d-none');

            $('#book_copy_id').val('');
        }

        function getBookByBarcode() {

            const barcode = barcodeInput.val().trim();

            if (barcode === '') {
                showFlash('warning', 'Please enter a barcode.', 'Warning');
                resetBookInfo();
                return;
            }

            $.ajax({
                url: '{{ route('borrows.getBookByBarcode') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    barcode: barcode
                },
                success: function(response) {
                    if (response.success) {
                        fillBookInfo(response.book);
                    } else {
                        showFlash('error', response.message ?? 'Failed', 'Error');
                        resetBookInfo();
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'An error occurred.';
                    showFlash('error', msg, 'Error');
                    resetBookInfo();
                }
            });
        }
    </script>
@endpush
