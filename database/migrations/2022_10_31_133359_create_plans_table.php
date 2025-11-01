<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Offline payments
         */
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Plans general meta
            $table->string('name');
            $table->text('description');
            $table->string('status')->default('pending');
            $table->integer('order')->nullable();
            $table->float('price')->nullable();
            $table->float('yearly_price')->nullable();
            $table->integer('trial_days')->nullable();
            $table->boolean('default_plan')->nullable();

            // Plan business meta
            $table->boolean('display_logo')->nullable();
            $table->boolean('highlight_ad')->nullable();
            $table->boolean('main_page_pin')->nullable();

            $table->boolean('share_on_slack')->nullable();
            $table->boolean('share_on_newsletter')->nullable();
            $table->boolean('share_on_partner_network')->nullable();
            $table->boolean('share_on_social_media')->nullable();


            // indexes
            $table->index('status');
            $table->index('display_logo');
            $table->index('highlight_ad');
            $table->index('main_page_pin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
