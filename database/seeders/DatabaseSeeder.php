<?php

use Illuminate\Database\Seeder;

use Database\Seeders\DataTypesTableSeeder;
use Database\Seeders\DataRowsTableSeeder;
use Database\Seeders\MenusTableSeeder;
use Database\Seeders\MenuItemsTableSeeder;
use Database\Seeders\RolesTableSeeder;
use Database\Seeders\PlansTableSeeder;
use Database\Seeders\PermissionsTableSeeder;
use Database\Seeders\PermissionRoleTableSeeder;
use Database\Seeders\UserRolesTableSeeder;
use Database\Seeders\PublicPagesTableSeeder;
use Illuminate\Support\Facades\DB;

//use Database\Seeders\InsertCountries;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(DataTypesTableSeeder::class);
        $this->call(DataRowsTableSeeder::class);
        $this->call(MenusTableSeeder::class);
        $this->call(MenuItemsTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
        $this->call(UserRolesTableSeeder::class);
        $this->call(PublicPagesTableSeeder::class);
        $this->call(PlansTableSeeder::class);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
