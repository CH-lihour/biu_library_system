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
        $book = Book::create($data);
        if (isset($data['author_ids'])) {
            $book->authors()->sync($data['author_ids']);
        }

        return $book;
    }

    public function update(int $id, array $data)
    {
        $book = Book::findOrFail($id);

        if (isset($data['author_ids'])) {
            $book->authors()->sync($data['author_ids']);
        }
        
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
