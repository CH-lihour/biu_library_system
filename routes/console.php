<?php

use App\Services\PermissionSyncService;
use App\Services\ReservationService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('library:expire-reservations', function (ReservationService $reservations) {
    $this->info($reservations->expireStale().' reservation(s) expired.');
})->purpose('Close reservations that were not collected in time and hold the copy for the next member');

Artisan::command('permissions:sync', function (PermissionSyncService $sync) {
    $result = $sync->sync();

    $this->info('Added: '.(implode(', ', $result['added']) ?: 'nothing'));
    $this->info('Removed: '.(implode(', ', $result['removed']) ?: 'nothing'));
})->purpose('Create permissions for new admin routes and remove those whose routes are gone');

Schedule::command('library:expire-reservations')->hourly();
