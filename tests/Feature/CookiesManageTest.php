<?php

namespace Tests\Feature;

use App\Models\Cookie;
use App\Models\Group;
use App\Models\Scan;
use App\Services\LngService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CookiesManageTest extends BaseFeature
{
    public function setUp() : void
    {
        parent::setUp();

        $this->scan = factory(Scan::class)->create()->load('website');
    }

    /**
     * @return void
     */
    public function testCreateOneResult()
    {
        $domainName = $this->scan->website->domain;

        $data = [
            'url' => "http://{$domainName}/",
            'scan_key' => $this->scan->key,
            'cookies' => [[
                'name' => $this->faker->word,
                // 'path' =>
                'expired_at' => Carbon::parse($this->faker->dateTimeInInterval('now', '+ 1 month'))->timestamp,
                'provider' => $domainName,
                'httpOnly' => $this->faker->boolean,
                'secure' => $this->faker->boolean,
                'session' => $this->faker->boolean,
                'sameSite' => 'None',
                'priority' => 'Medium',
            ]]
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user, 'api')
            ->json('POST', route('results.store.regular'), $data);

        $response->assertStatus(201);

        $this->user->load(['results', 'cookies']);

        $this->assertCount(1, $this->user->results);
        $this->assertCount(1, $this->user->cookies);
    }

    public function testCreateSomeResultsByOneProvider()
    {
        $resultsCount = 3;
        $providerName = $this->faker->domainName;
        $cookieName = $this->faker->word;

        for ($i = 0; $i < $resultsCount; $i++) {
            $data = [
                'url' => "http://{$this->scan->website->domain}/",
                'scan_key' => $this->scan->key,
                'cookies' => [[
                    'name' => $cookieName,
                    // 'path' =>
                    'expired_at' => Carbon::parse($this->faker->dateTimeInInterval('now', '+ 1 month'))->timestamp,
                    'provider' => $providerName,
                    'httpOnly' => $this->faker->boolean,
                    'secure' => $this->faker->boolean,
                    'session' => $this->faker->boolean,
                    'sameSite' => 'None',
                    'priority' => 'Medium',
                ]]
            ];

            $response = $this->withHeaders($this->headers)
                ->actingAs($this->user, 'api')
                ->json('POST', route('results.store.regular'), $data);

            $response->assertStatus(201);
        }

        $this->user->load(['results', 'cookies']);

        $this->assertCount($resultsCount, $this->user->results);
        $this->assertCount(1, $this->user->cookies);
    }

    public function testObtainUserCookieList()
    {
        $resultsCount = 3;
        $domainName = $this->scan->website->domain;

        for ($i = 0; $i < $resultsCount; $i++) {
            $data = [
                'url' => "http://{$domainName}/",
                'scan_key' => $this->scan->key,
                'cookies' => [[
                    'name' => $this->faker->word,
                    // 'path' =>
                    'expired_at' => Carbon::parse($this->faker->dateTimeInInterval('now', '+ 1 month'))->timestamp,
                    'provider' => $domainName,
                    'httpOnly' => $this->faker->boolean,
                    'secure' => $this->faker->boolean,
                    'session' => $this->faker->boolean,
                    'sameSite' => 'None',
                    'priority' => 'Medium',
                ]]
            ];

            $this->withHeaders($this->headers)
                ->actingAs($this->user, 'api')
                ->json('POST', route('results.store.regular'), $data)
                ->assertStatus(201);
        }

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('GET', route('cookies.index'));

        $response->assertOk();

        $this->assertCount($resultsCount, $response->json());

        $response->assertJsonFragment([
            'is_third_party' => false,
            'group_id' => Group::IDENT_UNCLASSIFIED
        ]);
    }

    public function testObtainUserCookieItem()
    {
        $cookie = factory(Cookie::class)->create();

        $this->user->cookies()->attach($cookie->id);

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('GET', route('cookies.show', $cookie->id));

        $response->assertOk();
    }

    public function testUpdateUserCookie()
    {
        $cookie = factory(Cookie::class)->create();

        $this->user->cookies()->attach($cookie->id);

        $data = [
            'group_id' => $this->faker->randomElement(Group::IDENTS),
            'description' => $this->fillLangObject()
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('PUT', route('cookies.update', $cookie->id), $data);

        $response->assertOk();

        $this->assertEquals($data['group_id'], $response->json()['group_id']);

        $response->assertJsonFragment([
            'description' => $data['description']
        ]);
    }

    private function fillLangObject(?int $amount = null) : ?array
    {
        $result = [];
        $amount = $amount ?: $this->faker->numberBetween(1, 10);

        $codes = array_slice($this->faker->shuffle(app(LngService::class)->codeList()), 0, $amount);

        foreach ($codes as $code) {
            $result[$code] = $this->faker->sentence();
        }

        return count($result) > 0 ? $result : null;
    }
}
