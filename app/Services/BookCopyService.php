<?php

namespace App\Services;

use App\Repositories\Interfaces\BookCopyRepositoryInterface;

class BookCopyService
{
    public function __construct(protected BookCopyRepositoryInterface $bookCopyRepository)
    {

    }

    public function getAllBook()
    {
        return $this->bookCopyRepository->getAllBook();
    }

    public function findById(int $id)
    {
        return $this->bookCopyRepository->findById($id);
    }

    public function createBookCopy(array $data)
    {
        return $this->bookCopyRepository->createBookCopy($data);
    }

    public function updateBookCopy(int $id, array $data)
    {
        return $this->bookCopyRepository->updateBookCopy($id, $data);
    }

    public function deleteBookCopy(int $id)
    {
        return $this->bookCopyRepository->deleteBookCopy($id);
    }
}
