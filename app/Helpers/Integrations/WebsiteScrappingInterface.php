<?php

namespace App\Helpers\Integrations;

use Symfony\Component\HttpFoundation\File\File;

interface WebsiteScrappingInterface
{
    public function screenshot(string $origin) : File;
}