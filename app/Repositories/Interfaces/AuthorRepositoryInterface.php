<?php

namespace App\Repositories\Interfaces;

interface AuthorRepositoryInterface
{
    public function findById(int $id);
    public function createAuthor(array $data);
    public function updateAuthor(int $id, array $data);
    public function deleteAuthor(int $id);
}
