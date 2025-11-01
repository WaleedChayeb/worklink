<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('min_range')->nullable();
            $table->integer('max_range')->nullable();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('applicants_ranges')->truncate();
        DB::statement('
        INSERT INTO applicants_ranges (`name`, `min_range`, `max_range`, `created_at`, `updated_at`) VALUES
            ("1-10", 1, 10, NOW(), NOW()),
            ("25-50", 25, 50, NOW(), NOW()),
            ("50-100", 50, 100, NOW(), NOW()),
            ("100+", null, 100, NOW(), NOW())
        ');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('applicants_ranges');
    }
}
