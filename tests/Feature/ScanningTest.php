<?php

namespace Tests\Feature;

use App\Models\Scan;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\BaseFeature;
use Tymon\JWTAuth\Facades\JWTAuth;

class ScanningTest extends BaseFeature
{
    public function setUp() : void
    {
        parent::setUp();

        $this->token = JWTAuth::fromUser($this->user);

        $this->headers = [
            'Authorization' => "Bearer {$this->token}"
        ];
    }

    public function testSendPageResult()
    {
        $scan = factory(Scan::class)->create();

        $scan->load('website');

        $data = [
            'url' => "http://{$scan->website->domain}/",
            'scan_key' => $scan->key,
            'cookies' => [[
                'name' => $this->faker->word,
                // 'path' =>
                'expired_at' => Carbon::parse($this->faker->dateTimeInInterval('now', '+ 1 month'))->timestamp,
                'provider' => $scan->website->domain,
                'httpOnly' => $this->faker->boolean,
                'secure' => $this->faker->boolean,
                'session' => $this->faker->boolean,
                'sameSite' => 'None',
                'priority' => 'Medium',
            ]]
        ];

        $response = $this->actingAs($this->user, 'api')
            ->json('POST', route('results.store.regular'), $data);

        $response->assertStatus(201);

        $this->user->load(['results', 'cookies']);

        $this->assertCount(1, $this->user->results);
        $this->assertCount(1, $this->user->cookies);
    }

    public function testStartWebsiteFastScan()
    {
        $data = [
            'origin' => 'http://example.com/'
        ];

        $response = $this->json('POST', '/rest/scan/fast', $data);

        $response->assertStatus(200);

        $response->assertJson([
            'screenshot_url' => true,
            'channel' => true
        ]);

        $this->get($response->json()['screenshot_url'])
            ->assertOk();
    }

}
