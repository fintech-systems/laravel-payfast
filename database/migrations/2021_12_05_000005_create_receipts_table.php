<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceiptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('billable_id')->nullable();
            $table->string('billable_type')->nullable();                        
            $table->string('order_id')->unique();            
            $table->string('payfast_payment_id')->unique();            
            $table->string('payfast_token')->nullable()->index();
            $table->string('amount');
            $table->string('fees');            
            $table->timestamp('paid_at');
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
        Schema::dropIfExists('receipts');
    }
}
