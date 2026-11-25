<?php

namespace App\Services;

use App\Exceptions\RuleViolation;
use App\Models\Member;
use App\Models\User;
use App\Repositories\Interfaces\PortalRepositoryInterface;
use App\Repositories\Interfaces\ReservationRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class PortalService
{
    public function __construct(
        protected PortalRepositoryInterface $portalRepository,
        protected UserRepositoryInterface $userRepository,
        protected ReservationRepositoryInterface $reservationRepository,
    ) {}

    public function home(Member $member): array
    {
        $loans = $this->portalRepository->loans($member, true);

        return [
            'loans' => $loans,
            'overdue' => $loans->filter->isOverdue()->count(),
            'fines' => $loans->sum(fn ($loan) => $this->fine($loan, $member)),
            'reservations' => $this->portalRepository->reservations($member, true),
        ];
    }

    /** BR-03: days late times the plan's daily fine. */
    public function fine($loan, Member $member): float
    {
        return $loan->isOverdue()
            ? (int) $loan->due_date->diffInDays(today()) * (float) ($member->plan?->fine_per_day ?? 0)
            : 0.0;
    }

    public function catalog(array $filters): array
    {
        return [
            'books' => $this->portalRepository->catalog($filters['search'] ?? null, isset($filters['category']) ? (int) $filters['category'] : null),
            'categories' => $this->portalRepository->categories(),
        ];
    }

    public function book(Member $member, int $id): array
    {
        $this->reservationRepository->expireStale();

        return [
            'book' => $this->portalRepository->findBook($id),
            'reservation' => $this->portalRepository->openReservationFor($member, $id),
        ];
    }

    public function loans(Member $member): array
    {
        return [
            'current' => $this->portalRepository->loans($member, true),
            'history' => $this->portalRepository->loans($member, false),
        ];
    }

    public function reservations(Member $member): array
    {
        $this->reservationRepository->expireStale();

        return [
            'open' => $this->portalRepository->reservations($member, true),
            'closed' => $this->portalRepository->reservations($member, false),
        ];
    }

    public function updateProfile(User $user, array $data): void
    {
        $account = ['name' => $data['name']];

        if (! empty($data['password'])) {
            if (! Hash::check($data['current_password'] ?? '', $user->password)) {
                throw new RuleViolation('Your current password is not correct.');
            }

            $account['password'] = Hash::make($data['password']);
        }

        $this->userRepository->updateProfile($user->id, $account, [
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);
    }
}
