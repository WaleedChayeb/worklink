<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('plan_id')->references('id')->on('plans')->onDelete('cascade');
            $table->string('paypal_agreement_id')->nullable();
            $table->index('paypal_agreement_id');
            $table->string('stripe_subscription_id')->nullable();
            $table->index('stripe_subscription_id');
            $table->string('paypal_plan_id')->nullable();
            $table->string('ccbill_subscription_id')->nullable();
            $table->string('type');
            $table->index('type');
            $table->string('provider');
            $table->index('provider');
            $table->string('status');
            $table->index('status');
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('canceled_at')->nullable();
            $table->float('amount')->nullable();
            $table->timestamps();
            $table->index('paypal_plan_id');
            $table->index('ccbill_subscription_id');
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
