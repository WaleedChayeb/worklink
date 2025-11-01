<?php

use App\Model\JobListing;
use App\Model\JobType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V130 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->integer('type_id')->after('category_id')->nullable();
                $table->index('type_id');
            });
        }

        $jobs = JobListing::get();
        foreach($jobs as $job){
            $type = JobType::where('name', $job->type)->first();
            $id = 1;
            if($type){
                $id = $type->id;
            }
            $job->update([
                'type_id' => $id
            ]);
        }

        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->integer('status')->default(1)->change();
            });
        }

        if (Schema::hasTable('featured_clients')) {
            Schema::table('featured_clients', function (Blueprint $table) {
                $table->text('hyperlink')->after('client_logo')->nullable();
            });
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add the 'type' column back to the 'jobs' table
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('type')->after('category_id')->nullable();
            $table->index('type');
        });

        // Retrieve jobs and set 'type' column based on 'type_id' values
        $jobs = JobListing::all();
        foreach ($jobs as $job) {
            $type = JobType::find($job->type_id);
            $typeName = $type ? $type->name : JobType::first()->name;
            $job->update([
                'type' => $typeName
            ]);
        }

        // Drop the 'type_id' column from the 'jobs' table
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('type_id');
        });

        if (Schema::hasTable('featured_clients')) {
            Schema::table('featured_clients', function (Blueprint $table) {
                $table->dropColumn('hyperlink');
            });
        }


    }
}
