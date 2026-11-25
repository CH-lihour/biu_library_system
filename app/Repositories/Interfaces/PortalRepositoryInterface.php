<?php

namespace App\Repositories\Interfaces;

use App\Models\Member;

interface PortalRepositoryInterface
{
    public function catalog(?string $search, ?int $categoryId);

    public function categories();

    public function findBook(int $id);

    public function loans(Member $member, bool $current);

    public function reservations(Member $member, bool $open);

    public function openReservationFor(Member $member, int $bookId);
}
