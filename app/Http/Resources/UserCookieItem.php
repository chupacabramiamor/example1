<?php

namespace App\Http\Resources;

use App\Models\Group;
use App\Services\LngService;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCookieItem extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'domains' => $this->resource->results->map(function($result) {
                return $result->website->domain;
            }),
            'provider' => $this->resource->provider,
            'is_third_party' => $this->resource->results->count() > 0 ? ($this->resource->results[0]->website->domain != $this->resource->provider) : false,
            'group_id' => $this->resource->users->count() ? $this->resource->users[0]->pivot->group_id ?: Group::IDENT_UNCLASSIFIED : Group::IDENT_UNCLASSIFIED,
            'description' => LngService::merge(
                $this->resource->description,
                json_decode($this->resource->users[0]->pivot->description, true)
            )
        ];
    }
}
