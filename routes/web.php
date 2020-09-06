<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('{reactRoutes}', function () {
    return view('app');
})->where('reactRoutes', '^((?!rest|!api).)*$');

Route::get('/auth', 'Auth\LoginController@getIndex')->name('login');
Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');

Route::group([ 'prefix' => 'rest' ], function() {

    Route::get('/languages', 'LanguageController');
    Route::get('/plans', 'PlanController');

    Route::post('/scan/fast', 'ScanController@fast');

    Route::group([ 'prefix' => 'auth' ], function() {
        Route::get('/ping', 'Auth\PingController');
        Route::post('/login', 'Auth\LoginController@login');
        Route::get('/logout', 'Auth\LoginController@logout');
        Route::post('/register', 'Auth\RegisterController@register');
        Route::post('/recovery', 'Auth\ForgotPasswordController@sendResetLinkEmail');
        Route::post('/scan/regular', 'ScanController@regular');
    });

    Route::group([ 'middleware' => 'auth' ], function() {
        // User Websites
        Route::resource('my-websites', 'MyWebsiteController')->except([ 'create', 'edit' ]);
        Route::patch('/my-websites/{website}/complete', 'MyWebsiteController@patchComplete')->name('my-websites.complete');

        // Results & Cookies
        Route::get('/groups', 'GroupController');
        Route::resource('cookies', 'CookieController')->except([ 'create', 'edit', 'store' ]);

        // Profile
        Route::get('/me', 'UserProfileController@show')->name('me.info');
        Route::patch('/me', 'UserProfileController@update')->name('me.update');
    });
});
