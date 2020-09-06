<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class WebsitesTest extends BaseFeature
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testListsOfUserWebsites()
    {
        $user = factory(User::class)->create();

        factory(Website::class, 5)->create()->each(function($website) use ($user) {
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => Plan::inRandomOrder()->first()->id,
                'external_id' => 0,
                'status' => Subscription::STATUS_ACTIVE,
                'update_url' => $this->faker->url,
                'cancel_url' => $this->faker->url
            ]);

            $website->subscription_id = $subscription->id;
            $website->save();
        });

        $response = $this->withHeaders($this->headers)->actingAs($user)->json('GET', route('my-websites.index'));

        $response->assertOk();

        $this->assertCount(5, $response->json());
        $this->assertGreaterThan($response->json()[0], $response->json()[4]);
    }

    public function testCreatesNewWebsite()
    {
        $domain = $this->faker->domainName;

        $data = [
            'origin' => "http://{$domain}/"
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('POST', route('my-websites.store'), $data);

        $response->assertStatus(201)->assertJson([ 'id' => true ]);

        $this->assertEquals($domain, $response->json()['domain']);
    }

    public function testUpdateWebsiteBanner()
    {
        $website = factory(Website::class)->create();

        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => Plan::inRandomOrder()->first()->id,
            'external_id' => 0,
            'status' => Subscription::STATUS_ACTIVE,
            'update_url' => $this->faker->url,
            'cancel_url' => $this->faker->url
        ]);

        $website->subscription_id = $subscription->id;
        $website->save();

        $data = [ 'theme' => 'CodGrayWhite' ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('PUT', route('my-websites.update', $website->id), $data);

        $response->assertOk();

        $this->assertEquals($data, $response->json()['banner']);
    }

    public function testSetWebsiteConfigurationComplete()
    {
        $website = factory(Website::class)->create();

        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => Plan::inRandomOrder()->first()->id,
            'status' => Subscription::STATUS_ACTIVE,
            'update_url' => $this->faker->url,
            'cancel_url' => $this->faker->url
        ]);

        $website->subscription_id = $subscription->id;
        $website->save();

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('PATCH', route('my-websites.complete', $website->id));

        $response->assertOk();
    }

    public function testRemoveWebsite()
    {
        $website = factory(Website::class)->create();

        $subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => Plan::inRandomOrder()->first()->id,
            'status' => Subscription::STATUS_ACTIVE,
            'update_url' => $this->faker->url,
            'cancel_url' => $this->faker->url
        ]);

        $website->subscription_id = $subscription->id;
        $website->save();

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('DELETE', route('my-websites.destroy', $website->id));

        $response->assertOk();
    }

    public function testShouldRejectDuplicateWebsite()
    {
        $domain = $this->faker->domainName;

        $data = [
            'origin' => "http://{$domain}/"
        ];

        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('POST', route('my-websites.store'), $data);

        $response->assertStatus(201)->assertJson([ 'id' => true ]);

        // Повторный запрос
        $response = $this->withHeaders($this->headers)
            ->actingAs($this->user)
            ->json('POST', route('my-websites.store'), $data);

        $response->assertStatus(400);
    }
}
