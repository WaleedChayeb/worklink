<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V170 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('settings')
            ->where('key', 'ai.open_ai_model')
            ->update([
                'value' => 'gpt-4o',
            ]);

        // Ordering fixes

        DB::table('settings')
            ->where('key', 'media.max_file_upload_size')
            ->update([
                'order' => '75',
            ]);

        DB::table('settings')
            ->where('key', 'media.use_chunked_uploads')
            ->update([
                'order' => '80',
            ]);

        DB::table('settings')
            ->where('key', 'media.upload_chunk_size')
            ->update([
                'order' => '90',
            ]);


        // Resetting breads per latest changes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\DataTypesTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\DataRowsTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\MenusTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\MenuItemsTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\RolesTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\PermissionsTableSeeder']);
        Artisan::call('db:seed',['--force'=>true,'--class'=>'Database\Seeders\PermissionRoleTableSeeder']);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Artisan::call('optimize:clear');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
