<?php

namespace App\Services;

use App\Models\Cookie;
use App\Models\Group;
use App\Models\Plan;
use App\Models\Result;
use App\Models\Scan;
use App\Models\Subscription;
use App\Models\Website;
use App\Services\Traits\WithAuthUser;
use App\Services\Traits\WithWebsiteOrigin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class CookieService
{
    use WithAuthUser, WithWebsiteOrigin;

    public function createResult(string $url, string $scanKey, string $cookieName, string $provider, int $expired_at, ?string $path = '/', array $flags = []) : Result
    {
        $cookie = $this->makeCookie($cookieName, $provider);

        $scan = Scan::where('key', $scanKey)->first();

        return Result::create([
            'user_id' => $this->user ? $this->user->id : null,
            'url' => $url,
            'cookie_id' => $cookie->id,
            'expired_at' => Carbon::createFromTimestamp($expired_at),
            'path' => $path ?: '/',
            'flags' => $flags,
            'scan_id' => $scan->id
        ]);
    }

    public function getUserCookies() : Collection
    {
        return $this->user->cookies;
    }

    public function updateUserCookie($cookie, $data)
    {
        if (is_int($cookie)) {
            $cookie = Cookie::findOrFail($cookie);
        }

        $cookie->users()->syncWithoutDetaching([
            $this->user->id => [
                'group_id' => $data['group_id'],
                'description' => json_encode($data['description'])
            ]
        ]);

        return $cookie->load('users', 'results.website');
    }

    private function makeWebsite(string $websiteOrigin) : Website
    {
        $origin = $this->splitOrigin($websiteOrigin);

        $website = Website::whereDomain($origin->domain)->whereHas('subscription', function($query) {
            $query->where('user_id', $this->user->id);
        })->first();

        if (!$website) {
            $website = Website::create([
                'protocol' => $origin->protocol,
                'domain' => $origin->domain,
            ]);

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

    private function makeCookie(string $cookieName, string $provider) : Cookie
    {
        $conditions = [
            'name' => $cookieName,
        ];

        $cookie = Cookie::firstOrCreate($conditions, [
            'name' => $cookieName,
            'provider' => $provider
        ]);

        $cookie->users()->sync($this->user->id);

        return $cookie->fresh();
    }
}