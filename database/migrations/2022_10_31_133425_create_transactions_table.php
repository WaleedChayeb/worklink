<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('invoice_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('set null');
            $table->string('stripe_transaction_id')->nullable();
            $table->string('stripe_session_id')->nullable();
            $table->string('paypal_transaction_id')->nullable();
            $table->string('paypal_transaction_token')->nullable();


            $table->string('coinbase_charge_id')->nullable();
            $table->string('coinbase_transaction_token')->nullable();

            $table->string('nowpayments_payment_id')->nullable();
            $table->string('nowpayments_order_id')->nullable();


            $table->string('ccbill_payment_token')->nullable();
            $table->string('ccbill_transaction_id')->nullable();
            $table->string('ccbill_subscription_id')->nullable();

            $table->string('paystack_payment_token')->nullable();

            $table->string('status');
            $table->string('type');
            $table->string('payment_provider');
            $table->string('currency');
            $table->string('paypal_payer_id')->nullable();
            $table->float('amount');
            $table->text('taxes')->nullable();
            $table->timestamps();

            $table->index('stripe_transaction_id');
            $table->index('stripe_session_id');
            $table->index('paypal_payer_id');
            $table->index('paypal_transaction_id');
            $table->index('paypal_transaction_token');
            $table->index('coinbase_charge_id');
            $table->index('coinbase_transaction_token');
            $table->index('nowpayments_payment_id');
            $table->index('nowpayments_order_id');
            $table->index('ccbill_payment_token');
            $table->index('ccbill_transaction_id');
            $table->index('ccbill_subscription_id');
            $table->index('status');
            $table->index('type');
            $table->index('paystack_payment_token');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
