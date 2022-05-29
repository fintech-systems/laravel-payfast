<?php

namespace Tests\Feature;

use FintechSystems\Payfast\Order;
use Illuminate\Support\Facades\Event;
use FintechSystems\Payfast\Subscription;
use Illuminate\Foundation\Testing\WithoutEvents;
use Illuminate\Foundation\Testing\RefreshDatabase;
use FintechSystems\Payfast\Events\PaymentSucceeded;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use FintechSystems\Payfast\Events\SubscriptionCreated;
use FintechSystems\Payfast\Events\SubscriptionUpdated;
use FintechSystems\Payfast\Events\SubscriptionCancelled;
use FintechSystems\Payfast\Events\SubscriptionPaymentSucceeded;

class WebhooksTest extends FeatureTestCase
{        
    // Had to comment this test until I can do proper API testing
    // public function test_it_can_handle_a_subscription_payment_event()
    // {
    //     Event::fake();

    //     $user = $this->createUser();

    //     $order = Order::create([
    //         'billable_id' => $user->getKey(),
    //         'billable_type' => $user->getMorphClass(),
    //         'ip_address' => '127.0.0.1',
    //     ]);

    //     $this->postJson('payfast/webhook', [
    //         'm_payment_id' => $order->id,
    //         'pf_payment_id' => '68551425',
    //         'payment_status' => 'COMPLETE',            
    //         'item_name' => 'Subscription Payment Test',
    //         'token' => 'test_subscription',
    //         'custom_int1' => $user->getKey(),
    //         'custom_str1' => $user->getMorphClass(),
    //         'custom_int2' => 1234, // Plan ID
    //         'custom_str2' => 'Monthly Subscription', // Name of the subscription instance item
    //         'billing_date' => now()->addDay()->format('Y-m-d'),
    //         'amount_gross' => '5.03',
    //         'amount_fee' => '-2.50',
    //         'amount_net' => '2.53',            
    //     ])->assertOk();

    //     $this->assertDatabaseHas('customers', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),
    //     ]);

    //     $this->assertDatabaseHas('receipts', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),            
    //         'paid_at' => now(),            
    //     ]);
        
    // }

    // Had to comment test until I can do proper API testing
    // public function test_it_can_handle_a_subscription_created_event()
    // {
    //     $this->withoutExceptionHandling();

    //     Event::fake();

    //     $user = $this->createUser();

    //     $order = Order::create([
    //         'billable_id' => $user->getKey(),
    //         'billable_type' => $user->getMorphClass(),
    //         'ip_address' => '127.0.0.1',
    //     ]);

    //     $this->postJson('payfast/webhook', [
    //         'm_payment_id' => $order->id,
    //         'pf_payment_id' => '68551425',
    //         'payment_status' => 'COMPLETE',
    //         'item_name' => 'Subscription Created Event',
    //         'amount_gross' => '6.00',
    //         'amount_fee' => '-2.54',
    //         'amount_net' => '3.46',                                                
    //         'custom_int1' => $user->getKey(), // User ID
    //         'custom_str1' => $user->getMorphClass(), // User Model
    //         'custom_int2' => 1234, // Plan ID
    //         'custom_str2' => 'Monthly Subscription', // Name of the subscription instance item
    //         'email_address' => $user->email,                                    
    //         'merchant_id' => '13741656',
    //         'token' => 'test_subscription',
    //         'billing_date' => '2021-12-04',
    //         'signature' => '966ffe36f01023fe8ac7aab6fdfe1b07'
    //     ])->assertOk();

    //     $this->assertDatabaseHas('customers', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),
    //     ]);

    //     $this->assertDatabaseHas('subscriptions', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),
    //         'name' => 'Monthly Subscription',
    //         'plan_id' => 1234,
    //         'token' => 'test_subscription',
    //         'status' => Subscription::STATUS_ACTIVE,            
    //         'trial_ends_at' => null,
    //     ]);
        
    // }

    // // Had to comment this test until I can do proper API testing
    // public function test_it_can_handle_a_subscription_created_event_if_billable_already_exists()
    // {
    //     $this->withoutExceptionHandling();

    //     Event::fake();
   
    //     $user = $this->createUser();
    //     $user->customer()->create([
    //         'trial_ends_at' => now('UTC')->addDays(5),
    //     ]);

    //     $order = Order::create([
    //         'billable_id' => $user->getKey(),
    //         'billable_type' => $user->getMorphClass(),
    //         'ip_address' => '127.0.0.1',
    //     ]);

    //     $this->postJson('payfast/webhook', [
    //         'm_payment_id' => $order->id,
    //         'pf_payment_id' => '68551425',
    //         'payment_status' => 'COMPLETE',
    //         'item_name' => 'Subscription Created Event',
    //         'amount_gross' => '6.00',
    //         'amount_fee' => '-2.54',
    //         'amount_net' => '3.46',            
    //         'custom_int1' => $user->getKey(), // user id
    //         'custom_str1' => $user->getMorphClass(),            
    //         'custom_int2' => 1234,
    //         'custom_str2' => 'main',            
    //         'custom_int4' => 1,
    //         'email' => $user->payfastEmail(),                                    
    //         'merchant_id' => '13741656',
    //         'token' => 'test_subscription',
    //         'billing_date' => '2021-12-04',
    //         'signature' => '966ffe36f01023fe8ac7aab6fdfe1b07'            
    //     ])->assertOk();

    //     $this->assertDatabaseHas('customers', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),
    //     ]);

    //     $this->assertDatabaseHas('subscriptions', [
    //         'billable_id' => $user->id,
    //         'billable_type' => $user->getMorphClass(),
    //         'name' => 'main',
    //         'token' => 'test_subscription',
    //         'plan_id' => 1234,
    //         'payment_status' => Subscription::PAYMENT_STATUS_COMPLETE,            
    //         'trial_ends_at' => null,
    //     ]);
        
    // }

    public function test_it_can_handle_a_subscription_cancelled_event()
    {
        $this->withoutExceptionHandling();

        Event::fake();

        $billable = $this->createBillable('eugene');

        $subscription = $billable->subscriptions()->create([
            'name' => 'main',
            'token' => 244,
            'plan_id' => 2323,
            'status' => Subscription::STATUS_ACTIVE,
            'next_bill_at' => '2021-12-04',
            'merchant_payment_id' => 'random',
        ]);

        $this->postJson('payfast/webhook', [            
            'payment_status' => Subscription::PAYMENT_STATUS_CANCELLED,
            'token' => '244',
        ])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'billable_id' => $billable->id,
            'billable_type' => $billable->getMorphClass(),
            'name' => 'main',
            'token' => '244',
            'plan_id' => 2323,
            'payment_status' => Subscription::PAYMENT_STATUS_CANCELLED,
        ]);        
    }
}
