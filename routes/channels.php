<?php

use App\Models\Scan;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('scan.{scan_key}', function ($user, $scan_key) {
    return Scan::whereKey($scan_key)->first() !== null;
});

Broadcast::channel('tst', function() {
    return true;
});