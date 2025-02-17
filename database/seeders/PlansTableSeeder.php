<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlansTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('plans')->delete();
        
        \DB::table('plans')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Good',
                'description' => 'Good plan',
                'status' => 'published',
                'order' => 1,
                'price' => 10.0,
                'yearly_price' => 110.0,
                'trial_days' => NULL,
                'default_plan' => 0,
                'display_logo' => 1,
                'highlight_ad' => 0,
                'main_page_pin' => 0,
                'share_on_slack' => 0,
                'share_on_newsletter' => 1,
                'share_on_partner_network' => 0,
                'share_on_social_media' => 1,
                'created_at' => '2023-05-16 12:08:00',
                'updated_at' => '2023-05-16 12:40:02',
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Better',
                'description' => 'Better plan',
                'status' => 'published',
                'order' => 2,
                'price' => 25.0,
                'yearly_price' => 250.0,
                'trial_days' => NULL,
                'default_plan' => 1,
                'display_logo' => 1,
                'highlight_ad' => 0,
                'main_page_pin' => 0,
                'share_on_slack' => 1,
                'share_on_newsletter' => 1,
                'share_on_partner_network' => 1,
                'share_on_social_media' => 1,
                'created_at' => '2023-05-16 12:21:11',
                'updated_at' => '2023-05-16 12:41:11',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Best',
                'description' => 'Best plan',
                'status' => 'published',
                'order' => 3,
                'price' => 50.0,
                'yearly_price' => 450.0,
                'trial_days' => NULL,
                'default_plan' => 0,
                'display_logo' => 1,
                'highlight_ad' => 1,
                'main_page_pin' => 1,
                'share_on_slack' => 1,
                'share_on_newsletter' => 1,
                'share_on_partner_network' => 1,
                'share_on_social_media' => 1,
                'created_at' => '2023-05-16 12:22:19',
                'updated_at' => '2023-05-16 12:22:19',
            ),
            3 => 
            array (
                'id' => 6,
                'name' => 'Free',
                'description' => 'Free plan -',
                'status' => 'published',
                'order' => 4,
                'price' => 0.0,
                'yearly_price' => 0.0,
                'trial_days' => 0,
                'default_plan' => 0,
                'display_logo' => 0,
                'highlight_ad' => 0,
                'main_page_pin' => 0,
                'share_on_slack' => 0,
                'share_on_newsletter' => 0,
                'share_on_partner_network' => 0,
                'share_on_social_media' => 0,
                'created_at' => '2024-03-27 18:07:13',
                'updated_at' => '2024-03-27 18:07:13',
            ),
            4 => 
            array (
                'id' => 7,
                'name' => 'Trial',
            'description' => 'Trial plan - Custom period(s)',
                'status' => 'published',
                'order' => 5,
                'price' => 0.0,
                'yearly_price' => 0.0,
                'trial_days' => 15,
                'default_plan' => 0,
                'display_logo' => 0,
                'highlight_ad' => 0,
                'main_page_pin' => 0,
                'share_on_slack' => 0,
                'share_on_newsletter' => 0,
                'share_on_partner_network' => 0,
                'share_on_social_media' => 0,
                'created_at' => '2024-03-27 18:07:51',
                'updated_at' => '2024-03-27 18:09:34',
            ),
        ));
        
        
    }
}