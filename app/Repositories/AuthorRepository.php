<?php

namespace App\Repositories;

use App\Models\Author;
use App\Repositories\Interfaces\AuthorRepositoryInterface;

class AuthorRepository implements AuthorRepositoryInterface
{
    public function findById(int $id)
    {
        return Author::findOrFail($id);
    }

    public function createAuthor(array $data)
    {
        return Author::create($data);
    }

    public function updateAuthor(int $id, array $data)
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }

    public function deleteAuthor(int $id)
    {
        $author = Author::findOrFail($id);
        $author->delete();
        return $author;
    }
}
