<?php

namespace App\Repositories;

use App\Exceptions\RuleViolation;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\BorrowTransaction;
use App\Models\Member;
use App\Models\Reservation;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function findForMember(int $id, Member $member)
    {
        return $member->reservations()->findOrFail($id);
    }

    public function reserve(Member $member, int $bookId)
    {
        return DB::transaction(function () use ($member, $bookId) {
            // Locking the member makes a double click or two open tabs queue up.
            $member = Member::lockForUpdate()->findOrFail($member->id);
            $book = Book::findOrFail($bookId);

            if ($reason = $member->blockedReason()) {
                throw new RuleViolation($reason);
            }

            if ($member->reservations()->open()->where('book_id', $book->id)->exists()) {
                throw new RuleViolation('You already reserved this book.');
            }

            $hasLoan = BorrowTransaction::onLoan()
                ->where('member_id', $member->id)
                ->whereHas('bookCopy', fn ($query) => $query->where('book_id', $book->id))
                ->exists();

            if ($hasLoan) {
                throw new RuleViolation('You already have this book on loan.');
            }

            $limit = config('library.max_reservations');
            if ($member->reservations()->open()->count() >= $limit) {
                throw new RuleViolation("You have reached the limit of {$limit} open reservations.");
            }

            return $member->reservations()->create(['book_id' => $book->id, 'status' => 'pending']);
        });
    }

    public function markReady(int $id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = Reservation::findOrFail($id);
            Book::lockForUpdate()->findOrFail($reservation->book_id);
            $reservation->refresh();

            if ($reservation->displayStatus() !== 'pending') {
                throw new RuleViolation('This reservation is not waiting any more.');
            }

            if (self::freeCopies($reservation->book_id) < 1) {
                throw new RuleViolation('There is no free copy of this book to hold.');
            }

            $reservation->update(self::hold());

            return $reservation;
        });
    }

    public function cancel(int $id)
    {
        return DB::transaction(function () use ($id) {
            $reservation = Reservation::lockForUpdate()->findOrFail($id);

            if ($reservation->status !== 'pending') {
                throw new RuleViolation('This reservation is already closed.');
            }

            $wasHeld = $reservation->displayStatus() === 'ready';
            $reservation->update(['status' => 'cancelled']);

            if ($wasHeld) {
                self::holdForNext($reservation->book_id);
            }

            return $reservation;
        });
    }

    public function expireStale(): int
    {
        $stale = Reservation::open()->whereNotNull('notified_at')->where('expires_at', '<=', now())->get();

        foreach ($stale as $reservation) {
            $reservation->update(['status' => 'expired']);
            self::holdForNext($reservation->book_id);
        }

        return $stale->count();
    }

    /** Copies on the shelf that are not held for a member (ignoring that member's own hold, if given). */
    public static function freeCopies(int $bookId, ?int $forMemberId = null): int
    {
        $available = BookCopy::where('book_id', $bookId)->where('status', 'available')->count();
        $held = Reservation::held()->where('book_id', $bookId)
            ->when($forMemberId, fn ($query) => $query->where('member_id', '!=', $forMemberId))
            ->count();

        return $available - $held;
    }

    /** FR-RES-02: when a copy becomes free, hold it for the first member in the queue. */
    public static function holdForNext(int $bookId): void
    {
        DB::transaction(function () use ($bookId) {
            Book::withTrashed()->lockForUpdate()->find($bookId);

            $queue = Reservation::open()->whereNull('notified_at')->where('book_id', $bookId)->orderBy('id')->get();

            foreach ($queue as $next) {
                if (self::freeCopies($bookId) < 1) {
                    return;
                }

                if ($next->member && $next->member->blockedReason() === null) {
                    $next->update(self::hold());
                }
            }
        });
    }

    private static function hold(): array
    {
        return ['notified_at' => now(), 'expires_at' => now()->addDays(config('library.hold_days'))];
    }
}
