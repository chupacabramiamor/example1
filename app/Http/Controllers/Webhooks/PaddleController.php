<?php

namespace App\Http\Controllers\Webhooks;

use App\Exceptions\IntegrationException;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaddleWebhookRequest;
use App\Models\Subscription;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaddleController extends Controller
{
    private $subscriptionSvc;

    private $subscriptionTypeMapping = [
        'active' => Subscription::STATUS_ACTIVE,
        'trialing' => Subscription::STATUS_TRAILING,
        'past_due' => Subscription::STATUS_PAST_DUE,
        'paused' => Subscription::STATUS_PAUSED,
        'deleted' => Subscription::STATUS_DELETED,
        'cancelled' => Subscription::STATUS_CANCELLED
    ];

    public function __construct(SubscriptionService $subscriptionSvc)
    {
        $this->subscriptionSvc = $subscriptionSvc;
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(PaddleWebhookRequest $request)
    {
        $methodName = $this->getHandleMethodName($request->input('alert_name'));
        $this->$methodName($request);
    }

    protected function getHandleMethodName(string $slug): string
    {
        $value = 'handle_' . $slug;

        return preg_replace_callback('/_([a-z])/', function($words) {
            return strtoupper($words[1]);
        }, $value);
    }

    private function handleSubscriptionCreated(Request $request)
    {
        $plan = $this->subscriptionSvc->determinePlan($request->input('subscription_plan_id'));

        if (!$plan) {
            throw new IntegrationException('plan_determining_failed');
        }

        $user = $this->subscriptionSvc->determineUser($request->input('email'));

        if (!$user) {
            throw new IntegrationException('user_determining_failed');
        }

        $subscription = Subscription::where('user_id', $user->id)
            ->whereHas('website', function($q) use ($request) { $q->where('id', $request->input('passthrough.website_id')); })
            ->first();

        if (!$subscription) {
            throw new IntegrationException('subscription_determining_failed');
        }

        $subscription->fill([
            'plan_id' => $plan->id,
            'user_id' => $user->id,
            'external_id' => $request->input('subscription_id'),
            'status' => $this->subscriptionTypeMapping[$request->input('status')],
            'update_url' => $request->input('update_url'),
            'cancel_url' => $request->input('cancel_url'),
            'billing_at' => Carbon::parse($request->input('next_bill_date'))
        ]);

        $subscription->save();
    }

    private function handleSubscriptionUpdated(Request $request)
    {
        $subscription = Subscription::where('external_id', $request->input('subscription_id'))->first();

        if (!$subscription) {
            throw new IntegrationException('subscription_not_found');
        }

        $plan = $this->subscriptionSvc->determinePlan($request->input('subscription_plan_id'));

        if (!$plan) {
            throw new IntegrationException('plan_determining_failed');
        }

        $subscription->update([
            'plan_id' => $plan->id,
            'status' => $this->subscriptionTypeMapping[$request->input('status')],
            'billing_at' => Carbon::parse($request->input('next_bill_date'))
        ]);
    }

    private function handleSubscriptionCancelled(Request $request)
    {
        $subscription = Subscription::where('external_id', $request->input('subscription_id'))->first();

        if (!$subscription) {
            throw new IntegrationException('subscription_not_found');
        }

        $subscription->update([
            'cancelled_at' => now(),
            'status' => Subscription::STATUS_CANCELLED,
            'billing_at' => null
        ]);
    }

    private function handleSubscriptionPaymentSucceeded(Request $request)
    {
        $subscription = Subscription::where('external_id', $request->input('subscription_id'))->first();

        if (!$subscription) {
            throw new IntegrationException('subscription_not_found');
        }

        $subscription->update([
            'billing_at' => Carbon::parse($request->input('next_bill_date'))
        ]);
    }

    private function handleSubscriptionPaymentFailed(Request $request)
    {
        $subscription = Subscription::where('external_id', $request->input('subscription_id'))->first();

        if (!$subscription) {
            throw new IntegrationException('subscription_not_found');
        }

        $subscription->update([
            'status' => Subscription::STATUS_PAST_DUE,
            'billing_at' => null
        ]);
    }

}
