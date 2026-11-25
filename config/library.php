<?php

return [
    // BR-05: how many books one member may have on loan at the same time.
    'max_loans' => (int) env('LIBRARY_MAX_LOANS', 5),

    // How many open reservations one member may have.
    'max_reservations' => (int) env('LIBRARY_MAX_RESERVATIONS', 5),

    // BR-06: days a copy is held once a reservation is ready to collect.
    'hold_days' => (int) env('LIBRARY_HOLD_DAYS', 3),
];
