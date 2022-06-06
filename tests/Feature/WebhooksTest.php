<?php

namespace Tests\Feature;

use FintechSystems\Payfast\Order;
use FintechSystems\Payfast\Subscription;
use Illuminate\Support\Facades\Event;

class WebhooksTest extends FeatureTestCase
{
   
    public function test_it_can_handle_a_subscription_cancelled_event()
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $billable = $this->createBillable('taylor');

        $subscription = $billable->subscriptions()->create([
            'name' => 'main',
            'payfast_token' => 244,
            'plan_id' => 2323,
            'payfast_status' => Subscription::STATUS_ACTIVE,
            'next_bill_at' => \Carbon\Carbon::now()->format('Y-m-d'),
        ]);
        
        $this->postJson('payfast/webhook', [
            'payment_status' => Subscription::STATUS_DELETED,
            'token' => '244',
        ])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'billable_id' => $billable->id,
            'billable_type' => $billable->getMorphClass(),
            'name' => 'main',
            'payfast_token' => '244',
            'plan_id' => '2323',
            'payfast_status' => Subscription::STATUS_DELETED,
        ]);
    }
}
