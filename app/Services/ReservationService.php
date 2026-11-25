<?php

namespace App\Services;

use App\Models\Member;
use App\Repositories\Interfaces\ReservationRepositoryInterface;

class ReservationService
{
    public function __construct(protected ReservationRepositoryInterface $reservationRepository) {}

    public function reserve(Member $member, int $bookId)
    {
        $this->reservationRepository->expireStale();

        return $this->reservationRepository->reserve($member, $bookId);
    }

    /** Looked up through the member, so nobody can cancel someone else's. */
    public function cancelOwn(Member $member, int $id)
    {
        return $this->reservationRepository->cancel($this->reservationRepository->findForMember($id, $member)->id);
    }

    public function markReady(int $id)
    {
        $this->reservationRepository->expireStale();

        return $this->reservationRepository->markReady($id);
    }

    public function cancel(int $id)
    {
        return $this->reservationRepository->cancel($id);
    }

    public function expireStale(): int
    {
        return $this->reservationRepository->expireStale();
    }
}
