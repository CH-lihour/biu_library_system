<?php

namespace App\Repositories\Interfaces;

use App\Models\Member;

interface ReservationRepositoryInterface
{
    public function findForMember(int $id, Member $member);

    public function reserve(Member $member, int $bookId);

    public function markReady(int $id);

    public function cancel(int $id);

    public function expireStale(): int;
}
