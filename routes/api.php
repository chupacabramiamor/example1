<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/scanning/regular', 'API\Scanner\ResultController')->middleware('auth:api')->name('results.store.regular');
Route::post('/scanning/fast', 'API\Scanner\ResultController')->name('results.store.fast');
Route::get('/test', function() {
    \App\Events\TestEvent::dispatch();
    return 'ok';
});
