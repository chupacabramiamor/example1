<?php

namespace App\Http\Resources;

use App\Models\Group;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCookieList extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->map(function($cookie) {
            return [
                'id' => $cookie->id,
                'name' => $cookie->name,
                'domains' => $cookie->results->map(function($result) {
                    return $result->scan->website->domain;
                }),
                'provider' => $cookie->provider,
                'is_third_party' => ($cookie->results[0]->scan->website->domain != $cookie->provider),
                'group_id' => $cookie->pivot->group_id ?: Group::IDENT_UNCLASSIFIED,
                'expired_at' => $cookie->results->min('expired_at')
            ];
        });
    }
}
