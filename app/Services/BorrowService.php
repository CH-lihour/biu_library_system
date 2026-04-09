<?php

namespace App\Services;

use \App\Repositories\BorrowRepository;
class BorrowService
{
    public function __construct(protected BorrowRepository $borrowRepository)
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
}
