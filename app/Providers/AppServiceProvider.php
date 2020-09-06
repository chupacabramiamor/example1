<?php

namespace App\Providers;

use App\Helpers\Integrations\Browserless;
use App\Helpers\Integrations\Paddle;
use App\Helpers\Integrations\PaymentSystemInterface;
use App\Helpers\Integrations\WebsiteScrappingInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Paddle Integration
        $this->app->bind(PaymentSystemInterface::class, function($app) {
            $config = config('services.paddle');

            return new Paddle\Manager($config['vendor_id'], $config['apikey']);
        });

        // Browserless Integration
        $this->app->bind(WebsiteScrappingInterface::class, function($app) {
            $config = config('services.browserless');

            return new Browserless\Manager($config['token']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();
    }
}
