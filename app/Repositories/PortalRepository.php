<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\Category;
use App\Models\Member;
use App\Repositories\Interfaces\PortalRepositoryInterface;

/** Read side of the member front-end. Personal data is always read through the member. */
class PortalRepository implements PortalRepositoryInterface
{
    public function catalog(?string $search, ?int $categoryId)
    {
        return Book::with(['authors', 'category'])
            ->withCount($this->copyCounts())
            ->when($search, function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';
                $query->where(fn ($query) => $query
                    ->where('title', 'like', $like)
                    ->orWhere('isbn', 'like', $like)
                    ->orWhereHas('authors', fn ($query) => $query->where(fn ($query) => $query->where('firstname', 'like', $like)->orWhere('lastname', 'like', $like))));
            })
            ->when($categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();
    }

    public function categories()
    {
        return Category::orderBy('name')->get(['id', 'name']);
    }

    public function findBook(int $id)
    {
        return Book::with(['authors', 'category', 'publisher'])
            ->withCount($this->copyCounts())
            ->findOrFail($id);
    }

    public function loans(Member $member, bool $current)
    {
        $query = $member->loans()->with(['bookCopy' => fn ($query) => $query->withTrashed(), 'bookCopy.book' => fn ($query) => $query->withTrashed()]);

        return $current
            ? $query->onLoan()->orderBy('due_date')->get()
            : $query->whereNotNull('return_date')->latest('return_date')->paginate(15);
    }

    public function reservations(Member $member, bool $open)
    {
        $query = $member->reservations()->with(['book' => fn ($query) => $query->withTrashed()]);

        return $open
            ? $query->open()->oldest()->get()
            : $query->whereNot('status', 'pending')->latest()->limit(20)->get();
    }

    public function openReservationFor(Member $member, int $bookId)
    {
        return $member->reservations()->open()->where('book_id', $bookId)->first();
    }

    private function copyCounts(): array
    {
        return [
            'copies',
            'copies as available_copies' => fn ($query) => $query->where('status', 'available'),
            'reservations as held_copies' => fn ($query) => $query->held(),
        ];
    }
}
