<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_reports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('job_id')->nullable();
            $table->index('job_id');
            $table->unsignedBigInteger('company_id')->nullable();
            $table->index('company_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->text('details')->nullable();
            $table->string('type');
            $table->index('type');
            $table->string('status');
            $table->index('status');
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
        Schema::dropIfExists('user_reports');
    }
}
