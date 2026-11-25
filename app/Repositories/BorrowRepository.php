<?php

namespace App\Repositories;

use App\Exceptions\RuleViolation;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Member;
use App\Models\Reservation;
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
        return Member::where('status', 'active')
            ->where(fn ($query) => $query->whereNull('expiry_date')->orWhere('expiry_date', '>=', today()))
            ->get();
    }

    public function createBorrowTransaction($data)
    {
        DB::transaction(function () use ($data) {
            $member = Member::lockForUpdate()->findOrFail($data['member_id']);
            $copyIds = array_unique($data['book_copy_ids']);

            if ($reason = $member->blockedReason()) {
                throw new RuleViolation($reason);
            }

            $limit = config('library.max_loans');
            if (BorrowTransaction::onLoan()->where('member_id', $member->id)->count() + count($copyIds) > $limit) {
                throw new RuleViolation("A member can have at most {$limit} books on loan.");
            }

            foreach ($copyIds as $bookCopyId) {
                // The book row is locked first, as reservations do, so a copy
                // cannot be lent and held at the same moment.
                $bookId = BookCopy::findOrFail($bookCopyId)->book_id;
                Book::lockForUpdate()->findOrFail($bookId);
                $copy = BookCopy::lockForUpdate()->findOrFail($bookCopyId);

                if ($copy->status !== 'available') {
                    throw new RuleViolation("Copy {$copy->barcode} is not available.");
                }

                if (ReservationRepository::freeCopies($bookId, $member->id) < 1) {
                    throw new RuleViolation("Copy {$copy->barcode} is held for another member.");
                }

                BorrowTransaction::create([
                    'book_copy_id' => $copy->id,
                    'member_id' => $member->id,
                    'borrow_date' => $data['borrow_date'],
                    'due_date' => $data['due_date'],
                    'staff_id' => auth()->user()?->staff?->id,
                    'status' => 'active',
                ]);

                $copy->update(['status' => 'borrowed']);

                Reservation::open()->where('book_id', $bookId)->where('member_id', $member->id)->update(['status' => 'fulfilled']);
            }
        });
    }

    public function returnBook($borrowId)
    {
        DB::transaction(function () use ($borrowId) {
            $borrowTransaction = BorrowTransaction::lockForUpdate()->findOrFail($borrowId);

            // Returning twice would free a copy that may be lent to someone else by now.
            if ($borrowTransaction->return_date !== null) {
                throw new RuleViolation('This book was already returned.');
            }

            $borrowTransaction->update([
                'return_date' => now(),
                'status' => 'returned',
            ]);

            $copy = BookCopy::withTrashed()->findOrFail($borrowTransaction->book_copy_id);
            $copy->update(['status' => 'available']);

            ReservationRepository::holdForNext($copy->book_id);
        });
    }
}
