<?php

namespace App\Services\Traits;

trait WithWebsiteOrigin
{
    private function splitOrigin(string $originUrl) : \stdClass
    {
        $pattern = '(https?):\/\/([\w\d\-\.]+\.\w{2,6})\/?';

        if (!preg_match("~{$pattern}~", $originUrl, $matches)) {
            throw new WebsiteException(__('incorrect_website_origin'));
        }

        return (object) [
            'protocol' => $matches[1],
            'domain' => $matches[2],
        ];
    }
}