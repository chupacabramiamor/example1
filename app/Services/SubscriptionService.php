<?php

namespace App\Services;

use App\Helpers\Integrations\PaymentSystemInterface;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\Traits\WithAuthUser;

class SubscriptionService
{
    use WithAuthUser;

    private $paymentSystem;

    public function __construct(PaymentSystemInterface $paymentSystem)
    {
        $this->paymentSystem = $paymentSystem;
    }

    public function syncPlans() : \stdClass
    {
        $stat = new \stdClass;

        $stat->disabled = 0;
        $stat->added = 0;
        $stat->updated = 0;
        $stat->ignored = 0;

        $remotePlans = $this->paymentSystem->fetchPlans();

        $externalIds = $remotePlans->map(function($item) {
            return $item['external_id'];
        });

        $verifyColumns = [ 'name', 'price' ];

        $toSyncItems = [];

        foreach ($this->paymentSystem->fetchPlans() as $item) {
            $plan = Plan::where('external_id', $item['external_id'])->first();

            if (!$plan) {
                Plan::create($item);
                $stat->added++;

                continue;
            }

            foreach ($verifyColumns as $column) {
                if ($plan->$column != $item[$column]) {
                    $plan->$column = $item[$column];
                }
            }

            if ($plan->isDirty()) {
                $plan->save();
                $stat->updated++;
            } else {
                $stat->ignored++;
            }
        }

        Plan::whereNotIn('external_id', $externalIds)->get()->each(function($plan) use ($stat) {
            if ($plan->disabled_at) {
                $stat->ignored++;
                return;
            }

            $plan->disabled_at = now();
            $plan->save();

            $stat->disabled++;
        });

        return $stat;
    }

    public function determineUser(string $email) : ?User
    {
        return User::whereEmail($email)->first();
    }

    public function determinePlan($external_id) : ?Plan
    {
        return Plan::where('external_id', $external_id)->first();
    }

    public function determineSubscription($external_id) : ?Subscription
    {
        return Subscription::where('external_id', $external_id)->first();
    }
}