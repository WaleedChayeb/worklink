<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryRangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_ranges', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('range');
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('salary_ranges')->truncate();
        DB::statement('
        INSERT INTO salary_ranges (`range`, `created_at`, `updated_at`) VALUES
            (">50k", NOW(), NOW()),
            ("50k-100k", NOW(), NOW()),
            ("100k-200k", NOW(), NOW()),
            ("200k+", NOW(), NOW())
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
        Schema::dropIfExists('salary_ranges');
    }
}
