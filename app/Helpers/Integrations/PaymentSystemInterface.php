<?php

namespace App\Helpers\Integrations;

use Illuminate\Support\Collection;

interface PaymentSystemInterface
{
    public function __construct($id, string $key);
    public function fetchPlans() : ?Collection;
}