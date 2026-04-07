<?php

namespace App\Repositories\Interfaces;

interface BookCopyRepositoryInterface
{
    public function getAllBook();
    public function findById(int $id);

    public function createBookCopy(array $data);

    public function updateBookCopy(int $id, array $data);

    public function deleteBookCopy(int $id);
}
