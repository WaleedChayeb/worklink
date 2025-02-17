<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('slogan');
            $table->text('description');
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        // Categories
        DB::statement("
            INSERT INTO `categories` (`id`, `name`, `slogan`, `description`, `created_at`, `updated_at`) VALUES
            (1, 'Design','Category Slogan', 'Category Description', '2022-11-15 17:19:36', '2022-11-15 17:19:42'),
            (2, 'Full-Stack Programming', 'Category Slogan', 'Category Description', '2022-11-15 17:19:37', '2022-11-15 17:19:42'),
            (3, 'Front-End Programming', 'Category Slogan', 'Category Description', '2022-11-15 17:19:37', '2022-11-15 17:19:43'),
            (4, 'Back-End Programming', 'Category Slogan', 'Category Description', '2022-11-15 17:19:38', '2022-11-15 17:19:43'),
            (5, 'Customer Support', 'Category Slogan', 'Category Description', '2022-11-15 17:19:39', '2022-11-15 17:19:44'),
            (6, 'DevOps and Sysadmin', 'Category Slogan', 'Category Description', '2022-11-15 17:19:39', '2022-11-15 17:19:44'),
            (7, 'Sales and Marketing', 'Category Slogan', 'Category Description', '2022-11-15 17:19:39', '2022-11-15 17:19:45'),
            (8, 'Management and Finance', 'Category Slogan', 'Category Description', '2022-11-15 17:19:40', '2022-11-15 17:19:45'),
            (9, 'Product', 'Category Slogan', 'Category Description', '2022-11-15 17:19:40', '2022-11-15 17:19:45'),
            (10, 'All Other Remote', 'Category Slogan', 'Category Description', '2022-11-15 17:19:41', '2022-11-15 17:19:46');
        ");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
