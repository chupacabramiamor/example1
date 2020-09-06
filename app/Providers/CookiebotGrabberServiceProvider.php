<?php

namespace App\Providers;

use App\Models\Cookie;
use App\Services\CookieBotGrabber;
use Illuminate\Support\ServiceProvider;

class CookiebotGrabberServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Cookiebot Grabber
        $this->app->bind(CookieBotGrabber::class, function($app) {
            $handler = new CookieBotGrabber(config('cookiebotgrabber.domains'));

            $handler->setCookieExistingChecking(function(string $cookieName) {
                return Cookie::whereName($cookieName)->count() > 0;
            });

            $handler->setCookieDataInserting(function(array $data) {
                Cookie::create([
                    'name' => $data['cookie_name'],
                    'provider' => $data['provider'],
                    'description' => $data['description'],
                    'group_id' => $data['group'],
                ]);
            });

            return $handler;
        });
    }
}
