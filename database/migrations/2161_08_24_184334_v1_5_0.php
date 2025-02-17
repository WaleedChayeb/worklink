<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V150 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        DB::table('settings')->insert(
            array(
                'key' => 'ai.open_ai_model',
                'display_name' => 'OpenAI Model',
                'value' => 'gpt-3.5-turbo-instruct',
                'details' => '{
"default" : "gpt-3.5-turbo-instruct",
"options" : {
"gpt-4o": "GPT 4.0-o",
"gpt-4": "GPT 4.0",
"gpt-3.5-turbo-instruct": "GPT 3.5 Turbo Instruct"
},
"description" : "The OpenAI model to be used. You can check more details, including pricing at their docs/models page."
}',
                'type' => 'select_dropdown',
                'order' => 22,
                'group' => 'AI',
            )
        );


        DB::table('settings')->insert(
            array(
                'key' => 'slack.slack_webhook_url',
                'display_name' => 'Slack webhook URL',
                'value' => '',
                'type' => 'text',
                'order' => 10,
                'group' => 'Slack',
            )
        );

        DB::table('settings')->insert(
            array(
                'key' => 'slack.slack_message_template',
                'display_name' => 'Slack message template',
                'value' => '🎉 *New job post available*

A new job posting is available on *[site_name]*. Details on short:

   * Title: _*[job_title]*_
   * Company: _[company_name]_
   * Type: _[job_type]_
   * Location: _[job_location]_

🔗 Check out more info on the listing page, at this link [job_slug] .',
                'details' => '{"description" : "Message template. Available variables: [site_name], [job_title], [company_name], [job_type], [job_location], [job_slug]"}',
                'type' => 'code_editor',
                'order' => 20,
                'group' => 'Slack',
            )
        );


        // Check if the table exists before attempting to drop it
        if (Schema::hasTable('email_subscribers')) {
            Schema::drop('email_subscribers');
        }

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
                'ai.open_ai_model',
                'slack.slack_webhook_url',
                'slack.slack_message_template',
            ])
            ->delete();


    }
}
