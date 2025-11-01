<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('job_types')->truncate();
        DB::statement('
        INSERT INTO job_types (`name`, `created_at`, `updated_at`) VALUES
            ("Full-time", NOW(), NOW()),
            ("Part-time", NOW(), NOW()),
            ("Contractor", NOW(), NOW()),
            ("Temporary", NOW(), NOW()),
            ("Internship", NOW(), NOW()),
            ("Per diem", NOW(), NOW()),
            ("Voluntary", NOW(), NOW())
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
        Schema::dropIfExists('job_types');
    }
}
