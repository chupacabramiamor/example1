<?php

namespace App\Providers;

use App\Helpers\Drivers\SendpulseMailDriver;
use Illuminate\Mail\MailServiceProvider as ServiceProvider;
use Illuminate\Mail\TransportManager;

class EmailServiceProvider extends ServiceProvider
{
    protected function registerSwiftTransport()
    {
        parent::registerSwiftTransport();

        $this->app->extend('swift.transport', function (TransportManager $transport) {
            $driver = 'sendpulse';
            $callback = new SendpulseMailDriver();
            $transport->extend($driver, $callback($transport));

            return $transport;
        });
    }
}
