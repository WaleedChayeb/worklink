<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V160 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('content');
            $table->boolean('is_published')->default(1);
            $table->boolean('is_dismissible')->default(1);
            $table->boolean('is_sticky');
            $table->boolean('is_global');
            $table->string('size');
            $table->dateTime('expiring_at')->nullable();
            $table->timestamps();
        });


        DB::table('settings')->insert(array(
            array(
                'key' => 'payments.invoices_enabled',
                'display_name' => 'Enables invoices generation',
                'value' => 1,
                'details' => '{
                        "true" : "On",
                        "false" : "Off",
                        "checked" : false,
                        "description": "If enabled, will generate invoices for each payment in the platform."
                        }',
                'type' => 'checkbox',
                'order' => 20,
                'group' => 'Payments',
            )
        ));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_announcements');
        DB::table('settings')
            ->whereIn('key', [
                'payments.invoices_enabled',
            ])
            ->delete();
    }
};
