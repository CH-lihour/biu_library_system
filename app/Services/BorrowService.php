<?php

namespace App\Services;

use App\Repositories\Interfaces\BorrowRepositoryInterface;
class BorrowService
{
    public function __construct(protected BorrowRepositoryInterface $borrowRepository)
    {

    }

    public function getBookByBarcode($barcode)
    {
        return $this->borrowRepository->getBookByBarcode($barcode);
    }

    public function getActiveMembers()
    {
        return $this->borrowRepository->getActiveMembers();
    }

    public function createBorrowTransaction($data)
    {
        $this->borrowRepository->createBorrowTransaction($data);
    }

    public function returnBook($borrowId)
    {
        $this->borrowRepository->returnBook($borrowId);
    }
}
