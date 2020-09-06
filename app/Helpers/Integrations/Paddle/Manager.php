<?php

namespace App\Helpers\Integrations\Paddle;

use App\Exceptions\IntegrationException;
use App\Helpers\Integrations\AbstractIntegration;
use App\Helpers\Integrations\PaymentSystemInterface;
use App\Models\Plan;
use Illuminate\Support\Collection;

class Manager extends AbstractIntegration implements PaymentSystemInterface
{
    private $vendor_id;
    private $apikey;

    protected $endpoint = 'https://vendors.paddle.com/api/2.0';

    public function __construct($id, string $key)
    {
        $this->vendor_id = $id;
        $this->apikey = $key;
    }

    protected function defaultPayloadData() : array
    {
        return [
            'vendor_id' => $this->vendor_id,
            'vendor_auth_code' => $this->apikey
        ];
    }

    public function fetchPlans() : ?Collection
    {
        $planTypeMapping = [
            'day' => Plan::TYPE_DAILY,
            'week' => Plan::TYPE_WEEKLY,
            'month' => Plan::TYPE_MONTHLY,
            'year' => Plan::TYPE_ANNUALLY,
        ];

        return collect(array_map(function($item) use ($planTypeMapping) {
            return [
                'name' => $item['name'],
                'type' => $planTypeMapping[$item['billing_type']],
                'interval' => $item['billing_period'],
                'price' => $item['recurring_price'][config('services.paddle.currency')],
                'external_id' => $item['id']
            ];
        }, $this->sendRequest('/subscription/plans')));
    }
}