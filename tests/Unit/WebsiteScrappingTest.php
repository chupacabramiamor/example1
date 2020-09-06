<?php

namespace Tests\Unit;

use App\Helpers\Integrations\WebsiteScrappingInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\File\File;
use Tests\TestCase;

class WebsiteScrappingTest extends TestCase
{
    private $websiteScrappingSvc;

    public function setUp(): void
    {
        parent::setUp();
        $this->websiteScrappingSvc = app()->make(WebsiteScrappingInterface::class);
    }


    public function testShouldObtainScreenshot()
    {
        $file = $this->websiteScrappingSvc->screenshot('https://example.com');

        $this->assertInstanceOf(File::class, $file);
    }
}
