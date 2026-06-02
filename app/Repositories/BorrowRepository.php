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

            foreach ($data['book_copy_ids'] as $bookCopyId) {
                BorrowTransaction::create([
                    'book_copy_id' => $bookCopyId,
                    'member_id' => $data['member_id'],
                    'borrow_date' => $data['borrow_date'],
                    'due_date' => $data['due_date'],
                    'staff_id' => auth()->id(),
                ]);

                BookCopy::where('id', $bookCopyId)->update(['status' => 'borrowed']);
            }
        });
    }

    public function returnBook($borrowId)
    {
        $borrowTransaction = BorrowTransaction::findOrFail($borrowId);
        $borrowTransaction->update([
            'return_date' => now(),
        ]);

        BookCopy::where('id', $borrowTransaction->book_copy_id)->update(['status' => 'available']);
    }
}
