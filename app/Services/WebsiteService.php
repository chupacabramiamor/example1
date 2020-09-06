<?php

namespace App\Services;

use App\Exceptions\WebsiteException;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Website;
use App\Services\Traits\WithAuthUser;
use App\Services\Traits\WithWebsiteOrigin;
use Illuminate\Database\Eloquent\Collection;

class WebsiteService
{
    use WithAuthUser, WithWebsiteOrigin;

    public function getList() : Collection
    {
        $builder = Website::query();

        if ($this->user) {
            $builder->whereHas('subscription', function($query) {
                $query->where('user_id', $this->user->id);
            });
        }

        return $builder->orderBy('created_at', 'desc')->with('subscription.plan', 'scans.cookies')->get();
    }

    public function isExist(string $originUrl) : bool
    {
        $origin = $this->splitOrigin($originUrl);

        $builder = Website::query();

        if ($this->user) {
            $builder->whereHas('subscription', function($q) {
                $q->where('user_id', $this->user->id);
            });
        }

        return $builder->where('domain', $origin->domain)->count() > 0;
    }

    public function create(string $originUrl, string $description = null) : Website
    {
        $origin = $this->splitOrigin($originUrl);

        $website = Website::create([
            'protocol' => $origin->protocol,
            'domain' => $origin->domain,
            'description' => $description
        ]);

        if ($this->user) {
            $subscription = Subscription::create([
                'user_id' => $this->user->id,
                'plan_id' => Plan::wherePrice(0)->first()->id,
                'status' => Subscription::STATUS_ACTIVE,
            ]);

            $website->subscription_id = $subscription->id;

            $website->save();
        }

        return $website;
    }

    public function make(string $originUrl, string $description = null) : Website
    {
        $origin = $this->splitOrigin($originUrl);

        $builder = Website::query();

        if ($this->user) {
            $builder->whereHas('subscription', function($query) {
                $query->where('user_id', $this->user->id);
            });
        }

        $website = $builder->where('domain', $origin->domain)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$website) {
            $website = $this->create($originUrl, $description);
        }

        return $website->load('subscription', 'lastResults.cookie');
    }

    public static function isActionRequired(Website $website) : bool
    {
        if (!$website->lastResults->count()) {
            return true;
        }

        foreach ($website->lastResults as $result) {
            if ($result->cookie->group_id == Group::IDENT_UNCLASSIFIED) {
                return true;
            }
        }

        return false;
    }
}