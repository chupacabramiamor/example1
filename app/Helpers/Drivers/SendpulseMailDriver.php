<?php

namespace App\Helpers\Drivers;

use App\Helpers\Transports\SendpulseTransport;
use Illuminate\Mail\TransportManager;

class SendpulseMailDriver
{
    public function __invoke(TransportManager $manager)
    {
        return function ($app) {
            $config = $app['config']->get('services.sendpulse');

            return new SendpulseTransport($config);
        };
    }
}