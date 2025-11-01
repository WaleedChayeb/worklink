<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V140 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('blog_posts')) {
            Schema::create('blog_posts', function(Blueprint $table)
            {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned()->nullable();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->string('title');
                $table->string('slug')->unique();
                $table->index('slug');
                $table->string('cover')->default('no-cover.png');
                $table->longtext('content');
                $table->string('tags')->nullable();
                $table->integer('status')->default(1);
                $table->index('status');
                $table->timestamps();
            });
        }

        \DB::table('settings')->insert(

            array (
                'key' => 'site.display_blog_page',
                'display_name' => 'Display blog page links',
                'value' => '1',
                'details' => '{
"on" : "On",
"off" : "Off",
"checked" : false,
"description" : "If enabled, the blog page links will be displayed."
}',
                'type' => 'checkbox',
                'order' => 160,
                'group' => 'Site',
            ) );


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blog_posts');
        DB::table('settings')->where('key', 'site.display_blog_page')->delete();
    }
}
