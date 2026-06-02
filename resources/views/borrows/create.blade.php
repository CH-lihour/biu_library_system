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
                        <div id="selectedBookCopyIds"></div>
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
                                            <div class="col-md-12">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th style="width: 20px">#</th>
                                                            <th>Book Title</th>
                                                            <th>Barcode</th>
                                                            <th>ISBN</th>
                                                            <th style="width: 20px; text-align: center;">Remove</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="bookInfo">

                                                    </tbody>
                                                </table>
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
        const bookInfo = $('#bookInfo');
        const selectedBookCopyIds = $('#selectedBookCopyIds');

        // Get book info when pressing Enter key in barcode input
        barcodeInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                getBookByBarcode();
            }

        });

        // Get
        searchButton.on('click', function() {
            getBookByBarcode()
        });

        // If there's an old barcode value (e.g., after validation error), try to fetch the book info
        $(document).ready(function () {
            if (oldBarcode && oldBarcode.trim() !== '') {
                getBookByBarcode();
            }
        });

        // Helper function to normalize barcode for comparison
        function normalizeBarcode(barcode) {
            return (barcode || '').toString().trim().toLowerCase();
        }

        // Helper function to refresh row numbers after adding/removing books
        function refreshRowNumbers() {
            bookInfo.find('tr').each(function(index) {
                $(this).find('.row-number').text(index + 1);
            });
        }

        // Fill book info in the table and show the card
        function fillBookInfo(book) {
            const normalizedBarcode = normalizeBarcode(book.barcode); ;
            const barcodeExists = bookInfo.find('tr').toArray().some(function(row) {
                return normalizeBarcode($(row).data('barcode')) === normalizedBarcode;
            });

            if (barcodeExists) {
                showFlash('warning', 'This barcode is already added.', 'Warning');
                barcodeInput.val('');
                barcodeInput.focus();
                return;
            }

            $(bookInfo).append(`
                <tr data-barcode="${book.barcode || ''}" data-book-copy-id="${book.book_copy_id}">
                    <td class="row-number"></td>
                    <td>${book.title || '-'}</td>
                    <td>${book.barcode || '-'}</td>
                    <td>${book.isbn || '-'}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeBook(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);

            selectedBookCopyIds.append(`
                <input type="hidden" name="book_copy_ids[]" value="${book.book_copy_id}" data-book-copy-id="${book.book_copy_id}">
            `);

            refreshRowNumbers();

            $('#book_copy_ids').val(book.book_copy_id);

            bookInfoCard.removeClass('d-none');
            cardAction.removeClass('d-none');
            formBody.removeClass('d-none');
        }

        // Reset book info and hide the card
        function resetBookInfo() {
            cardAction.addClass('d-none');
            formBody.addClass('d-none');
            bookInfoCard.addClass('d-none');
        }

        // Fetch book info by barcode via AJAX
        function getBookByBarcode() {

            const barcode = barcodeInput.val().trim();

            if (barcode === '') {
                showFlash('warning', 'Please enter a barcode.', 'Warning');
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
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message ?? 'An error occurred.';
                    showFlash('error', msg, 'Error');
                    resetBookInfo();
                }
            });
        }

        // Remove book from the table
        function removeBook(button) {
            const row = $(button).closest('tr').remove();
            const bookCopyId = String(row.data('book-copy-id') ?? '').trim();

            selectedBookCopyIds.find(`input[data-book-copy-id="${bookCopyId}"]`).remove();

            refreshRowNumbers();

            if ($('#bookInfo tr').length === 0) {
                resetBookInfo();
            }
            
            barcodeInput.val('');
            barcodeInput.focus();
        }
    </script>
@endpush
