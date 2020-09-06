<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ResultItem extends JsonResource
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
            'name' => $this->name,
            'domain' => $this->domain,
            'provider' => $this->provider,
            'type' => $this->type,
            'group' => $this->cookie->group,
            'description' => $this->description
        ];
    }
}
