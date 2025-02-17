<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->index('slug');
            $table->string('title');
            $table->longText('content');
            $table->unsignedInteger('page_order')->default(0);
            $table->boolean('shown_in_footer')->nullable();
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
        //
    }
}
