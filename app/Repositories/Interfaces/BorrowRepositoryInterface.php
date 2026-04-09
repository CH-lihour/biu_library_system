<?php

namespace App\Repositories\Interfaces;

interface BorrowRepositoryInterface
{
    public function getBookByBarcode($barcode);

    public function getActiveMembers();

    public function createBorrowTransaction($data);
}
