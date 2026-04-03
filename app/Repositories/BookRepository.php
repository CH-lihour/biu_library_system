<?php

namespace App\Repositories;

use App\Models\Book;
use App\Repositories\Interfaces\BookRepositoryInterface;

class BookRepository implements BookRepositoryInterface
{
    public function findById(int $id)
    {
        return Book::with(['publisher', 'category'])->findOrFail($id);
    }

    public function create(array $data)
    {
        return Book::create($data);
    }

    public function update(int $id, array $data)
    {
        $book = Book::findOrFail($id);
        $book->update($data);
        return $book;
    }

    public function delete(int $id)
    {
        $book = Book::findOrFail($id);

        $book->delete();

        return $book->delete();
    }
}
