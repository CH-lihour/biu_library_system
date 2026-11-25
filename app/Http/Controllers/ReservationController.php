<?php

namespace App\Http\Controllers;

use App\DataTables\ReservationDataTable;
use App\Services\ReservationService;

class ReservationController extends Controller
{
    public function __construct(protected ReservationService $reservationService) {}

    public function index(ReservationDataTable $dataTable)
    {
        $this->reservationService->expireStale();

        return $dataTable->render('reservations.index');
    }

    public function ready(int $reservation)
    {
        $this->reservationService->markReady($reservation);

        return to_route('reservations.index')->with('success', 'The book is now held for the member.');
    }

    public function cancel(int $reservation)
    {
        $this->reservationService->cancel($reservation);

        return to_route('reservations.index')->with('success', 'Reservation cancelled.');
    }
}
