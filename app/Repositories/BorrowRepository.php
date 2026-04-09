<?php

namespace App\Repositories;

use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Member;
use App\Repositories\Interfaces\BorrowRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BorrowRepository implements BorrowRepositoryInterface
{
    public function getBookByBarcode($barcode)
    {
        return BookCopy::with(['book.authors', 'book.publisher', 'book.category'])
            ->where('barcode', $barcode)
            ->where('status', 'available')
            ->first();
    }

    public function getActiveMembers()
    {
        return Member::where('status', 'active')->get();
    }

    public function createBorrowTransaction($data)
    {
        DB::transaction(function () use ($data) {

            BorrowTransaction::create([
                'book_copy_id' => $data['book_copy_id'],
                'member_id' => $data['member_id'],
                'borrow_date' => $data['borrow_date'],
                'due_date' => $data['due_date'],
                'staff_id' => auth()->id(),
            ]);

            BookCopy::where('id', $data['book_copy_id'])->update(['status' => 'borrowed']);

            // foreach ($data['book_copy_id'] as $bookCopyId) {
            //     BorrowTransaction::create([
            //         'borrow_transaction_id' => $borrowTransaction->id,
            //         'book_copy_id' => $bookCopyId,
            //     ]);

            //     BookCopy::where('id', $bookCopyId)->update(['status' => 'borrowed']);
            // }
        });
    }
}
