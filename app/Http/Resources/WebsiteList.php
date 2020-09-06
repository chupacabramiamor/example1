<?php

namespace App\Http\Resources;

use App\Models\Website;
use App\Services\WebsiteService;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WebsiteList extends ResourceCollection
{
    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($item) {
            return [
                'id' => $item->id,
                'domain' => $item->domain,
                'state' => $item->state == Website::STATE_COMPLETED,
                'scanned_at' => $item->scanned_at,
                'next_scan_at' => $item->scanned_at ? Carbon::parse($item->scanned_at)->addDays(28) : null,
                'pages_count' => $item->lastResults->count() > 0 ? $item->lastResults->groupBy('url')->count() : 0,
                'cookies_count' => $item->lastResults->count(),
                'is_actions_required' => WebsiteService::isActionRequired($item),
                'plan' => [
                    'name' => $item->subscription->plan->name,
                    'status' => $item->subscription->status,
                    'billing_at' => $item->subscription->billing_at,
                    'price' => $item->subscription->plan->price,
                ]
            ];
        });
    }
}
