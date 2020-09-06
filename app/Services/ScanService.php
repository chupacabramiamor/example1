<?php

namespace App\Services;

use App\Helpers\Integrations\WebsiteScrappingInterface;
use App\Models\Plan;
use App\Models\Result;
use App\Models\Scan;
use App\Models\Subscription;
use App\Models\Website;
use App\Services\Traits\WithAuthUser;
use App\Services\Traits\WithWebsiteOrigin;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\UuidFactory;
use Symfony\Component\HttpFoundation\File\File;

class ScanService
{
    use WithWebsiteOrigin, WithAuthUser;

    private $origin;
    private $websiteScrapper;

    public function __construct(WebsiteScrappingInterface $websiteScrapper)
    {
        $this->websiteScrapper = $websiteScrapper;
    }

    public function setOrigin(string $origin)
    {
        $this->origin = $origin;
    }

    /**
     * Запуск сканирования
     * @param  int|null $pagesCount Лимит страниц, которые должны быть просканированы
     * @return void
     */
    public function run(?int $pagesCount = null) : Scan
    {
        $scan = $this->makeScan();
        // $this->sendScanTask($scan->id, $pagesCount);

        return $scan;
    }

    public function captureScreenshot() : File
    {
        return $this->websiteScrapper->screenshot($this->origin);
    }

    public function calcPagesByScanKey(string $scanKey) : int
    {
        return Result::whereHas('scan', function($query) use($scanKey) {
            $query->where('key', $scanKey);
        })->groupBy('url')->count();
    }

    public function getCookiesByScanKey(string $scanKey)
    {
        $scan = Scan::where('key', $scanKey)->first();

        return $scan->cookies;
    }

    private function makeScan(?string $uuid = null) : Scan
    {
        $scan = Scan::make();

        $scan->website_id = $this->makeWebsite($this->origin)->id;
        $scan->save();

        return $scan->fresh();
    }

    private function makeWebsite(string $websiteOrigin) : Website
    {
        $origin = $this->splitOrigin($websiteOrigin);

        $website = $this->user ? Website::whereDomain($origin->domain)->whereHas('subscription', function($query) {
            $query->where('user_id', $this->user->id);
        })->first() : null;

        if (!$website) {
            $website = Website::create([
                'protocol' => $origin->protocol,
                'domain' => $origin->domain,
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
        }

        return $website;
    }
}