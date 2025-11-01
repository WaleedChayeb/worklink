<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V190 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('settings')->insert(array(
            array(
                'key' => 'site.disable_featured_categories_on_homepage',
                'display_name' => 'Disable featured categories on homepage',
                'value' => 0,
                'details' => '{
                        "true" : "On",
                        "false" : "Off",
                        "checked" : false,
                        "description": "If enabled, listings on the frontpage will all be shown grouped together, 12 per page."
                        }',
                'type' => 'checkbox',
                'order' => 155,
                'group' => 'Site',
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

        DB::table('settings')
            ->whereIn('key', [
                'site.disable_featured_categories_on_homepage',
            ])
            ->delete();


    }
};
