<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\Portal\CatalogRequest;
use App\Http\Requests\Portal\UpdateProfileRequest;
use App\Models\Member;
use App\Services\PortalService;
use App\Services\ReservationService;

/** Member front-end. The member always comes from the login, never from the request. */
class PortalController extends Controller
{
    public function __construct(protected PortalService $portalService, protected ReservationService $reservationService) {}

    public function home()
    {
        return view('portal.home', ['member' => $this->member()] + $this->portalService->home($this->member()));
    }

    public function catalog(CatalogRequest $request)
    {
        return view('portal.catalog', $this->portalService->catalog($request->validated()) + ['filters' => $request->validated()]);
    }

    public function book(int $book)
    {
        return view('portal.book', ['member' => $this->member()] + $this->portalService->book($this->member(), $book));
    }

    public function loans()
    {
        return view('portal.loans', ['member' => $this->member()] + $this->portalService->loans($this->member()));
    }

    public function reservations()
    {
        return view('portal.reservations', $this->portalService->reservations($this->member()));
    }

    public function reserve(int $book)
    {
        $this->reservationService->reserve($this->member(), $book);

        return to_route('portal.reservations')->with('success', 'Book reserved. We will hold a copy when one is ready.');
    }

    public function cancelReservation(int $reservation)
    {
        $this->reservationService->cancelOwn($this->member(), $reservation);

        return to_route('portal.reservations')->with('success', 'Reservation cancelled.');
    }

    public function profile()
    {
        return view('portal.profile', ['member' => $this->member()]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $this->portalService->updateProfile($request->user(), $request->validated());

        return to_route('portal.profile')->with('success', 'Profile updated.');
    }

    private function member(): Member
    {
        return request()->user()->member;
    }
}
