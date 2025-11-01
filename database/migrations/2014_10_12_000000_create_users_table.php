<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('bio')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->text('avatar')->nullable();
            $table->text('cover')->nullable();

            $table->boolean('enable_2fa')->nullable();
            $table->boolean('public_profile')->default(true);

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('identity_verified_at')->nullable();

            $table->string('auth_provider')->nullable();
            $table->string('auth_provider_id')->nullable();

            $table->index('auth_provider');
            $table->index('auth_provider_id');

            // Billing data
            $table->string('billing_address')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('postcode')->nullable();

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
