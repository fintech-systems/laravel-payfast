<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Event;
use FintechSystems\Payfast\Events\PaymentSucceeded;
use FintechSystems\Payfast\Events\SubscriptionCancelled;
use FintechSystems\Payfast\Events\SubscriptionCreated;
use FintechSystems\Payfast\Events\SubscriptionPaymentSucceeded;
use FintechSystems\Payfast\Events\SubscriptionUpdated;
use FintechSystems\Payfast\Subscription;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutEvents;

class WebhooksTest extends FeatureTestCase
{
    
    public function test_gracefully_handle_webhook_without_alert_name()
    {
        $this->postJson('payfast/webhook', [
            'event_time' => now()->addDay()->format('Y-m-d H:i:s'),
        ])->assertOk();
    }

    public function test_it_can_handle_a_subscription_created_event()
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $user = $this->createUser();

        $this->postJson('payfast/webhook', [
            'custom_str1' => 'subscription_created',            
            'm_payment_id' => null,
            'pf_payment_id' => '68551425',
            'payment_status' => 'COMPLETE',
            'item_name' => 'Setup Subscription Agreement',            
            'item_description' => 'Monthly Plan', // Doesn't return for R0 transactions
            'amount_gross' => '0.00',
            'amount_fee' => '0.00',
            'amount_net' => '0.00',                                                
            'custom_str2' => $user->getMorphClass(),
            'custom_str3' => 'Monthly Subscription', // Name of subscription            
            'custom_str4' => null,
            'custom_str5' => null,
            'custom_int1' => null,
            'custom_int2' => $user->getKey(), // user id
            'custom_int3' => 1234, // ID of the plan
            'custom_int4' => 1, // Quantity - unused                        
            'custom_int5' => null,
            'name_first' => null,
            'name_last' => null,
            'email_address' => $user->email,                                    
            'merchant_id' => '13741656',
            'token' => 'bar',
            'billing_date' => '2021-12-04',
            'signature' => '966ffe36f01023fe8ac7aab6fdfe1b07'
        ])->assertOk();

        $this->assertDatabaseHas('customers', [
            'billable_id' => $user->id,
            'billable_type' => $user->getMorphClass(),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'billable_id' => $user->id,
            'billable_type' => $user->getMorphClass(),
            'name' => 'Monthly Subscription',
            'payfast_plan' => 1234,
            'payfast_id' => 'bar',            
            'payfast_status' => Subscription::STATUS_ACTIVE,
            'quantity' => 1,
            'trial_ends_at' => null,
        ]);
        
    }

    public function test_it_can_handle_a_subscription_created_event_if_billable_already_exists()
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $user = $this->createUser();
        $user->customer()->create([
            'trial_ends_at' => now('UTC')->addDays(5),
        ]);

        $this->postJson('payfast/webhook', [
            'custom_str1' => 'subscription_created',
            'custom_int2' => $user->getKey(), // user id
            'custom_str2' => $user->getMorphClass(),            
            'custom_int3' => 1234,
            'custom_str3' => 'main',            
            'custom_int4' => 1,
            'email' => $user->payfastEmail(),                        
            'status' => Subscription::STATUS_ACTIVE,
            'token' => 'bar',
            'payment_status' => 'COMPLETE',            
        ])->assertOk();

        $this->assertDatabaseHas('customers', [
            'billable_id' => $user->id,
            'billable_type' => $user->getMorphClass(),
        ]);

        $this->assertDatabaseHas('subscriptions', [
            'billable_id' => $user->id,
            'billable_type' => $user->getMorphClass(),
            'name' => 'main',
            'payfast_id' => 'bar',
            'payfast_plan' => 1234,
            'payfast_status' => Subscription::STATUS_ACTIVE,
            'quantity' => 1,
            'trial_ends_at' => null,
        ]);
        
    }

    public function test_it_can_handle_a_subscription_cancelled_event()
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $billable = $this->createBillable('taylor');

        $subscription = $billable->subscriptions()->create([
            'name' => 'main',
            'payfast_id' => 244,
            'payfast_plan' => 2323,
            'payfast_status' => Subscription::STATUS_ACTIVE,
            'quantity' => 1,
        ]);

        $this->postJson('payfast/webhook', [
            'custom_str1' => 'subscription_created',            
            'payment_status' => Subscription::STATUS_DELETED,            
            'token' => '244',
        ])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'billable_id' => $billable->id,
            'billable_type' => $billable->getMorphClass(),
            'name' => 'main',
            'payfast_id' => '244',
            'payfast_plan' => 2323,
            'payfast_status' => Subscription::STATUS_DELETED,            
        ]);
        
    }
}
