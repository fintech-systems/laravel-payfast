<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use FintechSystems\Payfast\Enums\PaymentMethod;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billable_id');
            $table->string('billable_type');
            $table->integer('plan_id');
            $table->string('name');
                        
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedInteger('cycles')->nullable();
            $table->unsignedInteger('cycles_complete')->nullable();
            $table->unsignedInteger('frequency')->nullable();
            $table->unsignedInteger('payfast_status')->nullable();
            $table->string('payfast_status_reason')->nullable();
            $table->string('payfast_status_text')->nullable();
            $table->string('token')->unique();

            $table->string('payment_method');

            $table->string('status');
            $table->string('merchant_payment_id');
            $table->string('payment_status')->nullable();
            $table->string('subscription_status')->nullable();
            $table->timestamp('next_bill_at')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('paused_from')->nullable();            
            $table->timestamps();

            $table->index(['billable_id', 'billable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscriptions');
    }
}
