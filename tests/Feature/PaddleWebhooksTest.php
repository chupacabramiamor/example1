<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\BaseFeature;

class PaddleWebhooksTest extends BaseFeature
{
    private $webhookPath = '/webhooks/paddle';

    protected function setUp(): void
    {
        parent::setUp();

        $this->website = factory(Website::class)->create();
        $this->plan = Plan::inRandomOrder()->first();
        $this->subscription = Subscription::create([
            'user_id' => $this->user->id,
            'plan_id' => $this->plan->id,
            'status' => Subscription::STATUS_ACTIVE,
        ]);

        $this->website->subscription_id = $this->subscription->id;
        $this->website->save();
    }

    public function testSendSubscriptionCreatedHook()
    {
        $data = [
            'alert_name' => 'subscription_created',
            'alert_id' => $this->faker->randomNumber(),
            'cancel_url' => 'HARE_SHOULD_BE_CANCEL_URL',
            'checkout_id' => $this->faker->uuid(),
            'email' => $this->user->email,
            'passthrough' => json_encode(['website_id' => $this->website->id ]),
            'subscription_plan_id' => $this->plan->external_id,
            'status' => 'active',
            'subscription_id' => $this->faker->uuid(),
            'next_bill_date' => now()->addMonth(1)->format('Y-m-d')
        ];

        $response = $this->json('POST', $this->webhookPath, $data);

        $response->assertOk();

        $subscription = $this->subscription->fresh();

        $this->assertEquals(json_decode($data['passthrough'])->website_id, $subscription->website->id);
        $this->assertEquals($data['subscription_plan_id'], $subscription->plan->external_id);
        $this->assertEquals($data['subscription_id'], $subscription->external_id);
        $this->assertEquals($data['next_bill_date'], $subscription->billing_at);
    }

    public function testSendSubscriptionUpdatedHook()
    {
        $this->subscription->external_id = $this->faker->uuid();
        $this->subscription->save();

        $data = [
            'alert_name' => 'subscription_updated',
            'alert_id' => $this->faker->randomNumber(),
            'email' => $this->user->email,
            'status' => 'active',
            'subscription_plan_id' => $this->plan->external_id,
            'subscription_id' => $this->subscription->external_id,
            'next_bill_date' => now()->addMonth(1)->format('Y-m-d')
        ];

        $response = $this->json('POST', $this->webhookPath, $data);

        $response->assertOk();

        $subscription = $this->subscription->fresh();

        $this->assertEquals($data['subscription_plan_id'], $subscription->plan->external_id);
        $this->assertEquals($data['next_bill_date'], $subscription->billing_at);
    }

    public function testSendSubscriptionCancelledHook()
    {
        $this->subscription->external_id = $this->faker->uuid();
        $this->subscription->save();

        $data = [
            'alert_name' => 'subscription_cancelled',
            'alert_id' => $this->faker->randomNumber(),
            'subscription_id' => $this->subscription->external_id,
        ];

        $response = $this->json('POST', $this->webhookPath, $data);

        $response->assertOk();

        $subscription = $this->subscription->fresh();

        $this->assertEquals($subscription->billing_at, null);
        $this->assertEquals($subscription->status, Subscription::STATUS_CANCELLED);
    }

    public function testSendSubscriptionPaymentSucceededHook()
    {
        $this->subscription->external_id = $this->faker->uuid();
        $this->subscription->save();

        $data = [
            'alert_name' => 'subscription_payment_succeeded',
            'alert_id' => $this->faker->randomNumber(),
            'subscription_id' => $this->subscription->external_id,
            'next_bill_date' => now()->addMonth(1)->format('Y-m-d')
        ];

        $response = $this->json('POST', $this->webhookPath, $data);

        $response->assertOk();

        $subscription = $this->subscription->fresh();

        $this->assertEquals($data['next_bill_date'], $subscription->billing_at);
    }

    public function testSendSubscriptionPaymentFailedHook()
    {
        $this->subscription->external_id = $this->faker->uuid();
        $this->subscription->save();

        $data = [
            'alert_name' => 'subscription_payment_failed',
            'alert_id' => $this->faker->randomNumber(),
            'subscription_id' => $this->subscription->external_id,
        ];

        $response = $this->json('POST', $this->webhookPath, $data);

        $response->assertOk();

        $subscription = $this->subscription->fresh();

        $this->assertEquals($subscription->billing_at, null);
        $this->assertEquals($subscription->status, Subscription::STATUS_PAST_DUE);
    }
}
