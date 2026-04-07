<?php

namespace App\Repositories;

use App\Models\Book;
use App\Models\BookCopy;
use App\Repositories\Interfaces\BookCopyRepositoryInterface;

class BookCopyRepository implements BookCopyRepositoryInterface
{
    public function getAllBook()
    {
        return Book::all();
    }

    public function findById(int $id)
    {
        return BookCopy::findOrFail($id);
    }

    public function createBookCopy(array $data)
    {
        return BookCopy::create($data);
    }

    public function updateBookCopy(int $id, array $data)
    {
        $bookCopy = BookCopy::findOrFail($id);
        $bookCopy->update($data);
        return $bookCopy;
    }

    public function deleteBookCopy(int $id)
    {
        $bookCopy = BookCopy::findOrFail($id);
        return $bookCopy->delete();
    }
}
