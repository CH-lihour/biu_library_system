<?php

namespace App\Services;

use App\Repositories\Interfaces\AuthorRepositoryInterface;

class AuthorService
{
    public function __construct(protected AuthorRepositoryInterface $authorRepository)
    {

    }

    public function getAuthor(int $id)
    {
        return $this->authorRepository->findById($id);
    }

    public function createAuthor(array $data)
    {
        return $this->authorRepository->createAuthor($data);
    }

    public function updateAuthor(int $id, array $data)
    {
        return $this->authorRepository->updateAuthor($id, $data);
    }

    public function deleteAuthor(int $id)
    {
        return $this->authorRepository->deleteAuthor($id);
    }
}
