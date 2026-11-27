<?php

namespace JamstackVietnam\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public $rules = [
        'group' => 'nullable',
        'name' => 'nullable',
    ];

    public static function rules($id)
    {
        return [
            'general' => [
                'general_logo' => 'nullable',
                'general_business_name' => 'required',
                'general_business_name_en' => 'nullable',
                'general_site_name' => 'nullable',
                'general_site_name_en' => 'nullable',
                'general_description' => 'nullable|string',
                'general_description_en' => 'nullable|string',

                'general_email' => 'nullable',
                'general_tax_code' => 'nullable|string|max:100',
                'tax_code' => 'nullable|string|max:100',
                'general_address' => 'nullable',
                'general_address_en' => 'nullable',
                'showroom_address' => 'nullable|string|max:500',
                'showroom_address_en' => 'nullable|string|max:500',
                'general_phone' => 'nullable',
                'general_zalo' => 'nullable',
                'general_working_hours' => 'nullable',
                'general_working_hours_en' => 'nullable',
                'general_facebook' => 'nullable',
                'general_linkedin' => 'nullable|string|max:500',
                'general_tiktok' => 'nullable',
                'map_iframe' => 'nullable',
                'general_map_iframe' => 'nullable',

                'home_about_eyebrow' => 'nullable|string|max:255',
                'home_about_eyebrow_en' => 'nullable|string|max:255',
                'home_about_lead_highlight' => 'nullable|string|max:500',
                'home_about_lead_highlight_en' => 'nullable|string|max:500',
                'home_about_lead_rest' => 'nullable|string|max:1000',
                'home_about_lead_rest_en' => 'nullable|string|max:1000',
                'home_about_paragraph_1' => 'nullable|string',
                'home_about_paragraph_1_en' => 'nullable|string',
                'home_about_paragraph_2' => 'nullable|string',
                'home_about_paragraph_2_en' => 'nullable|string',
                'home_about_commitment_quote' => 'nullable|string|max:500',
                'home_about_commitment_quote_en' => 'nullable|string|max:500',
                'home_about_commitment_sub' => 'nullable|string|max:500',
                'home_about_commitment_sub_en' => 'nullable|string|max:500',
                'home_about_cta_text' => 'nullable|string|max:255',
                'home_about_cta_text_en' => 'nullable|string|max:255',
                'home_about_cta_link' => 'nullable|string|max:500',
                'home_about_cta_link_en' => 'nullable|string|max:500',
                'home_contact_title' => 'nullable|string|max:500',
                'home_contact_title_en' => 'nullable|string|max:500',
                'home_contact_description' => 'nullable|string',
                'home_contact_description_en' => 'nullable|string',

                'about_story_eyebrow' => 'nullable|string|max:255',
                'about_story_eyebrow_en' => 'nullable|string|max:255',
                'about_story_heading' => 'nullable|string|max:500',
                'about_story_heading_en' => 'nullable|string|max:500',
                'about_story_paragraph_1' => 'nullable|string',
                'about_story_paragraph_1_en' => 'nullable|string',
                'about_story_paragraph_2' => 'nullable|string',
                'about_story_paragraph_2_en' => 'nullable|string',
                'about_story_paragraph_3' => 'nullable|string',
                'about_story_paragraph_3_en' => 'nullable|string',
                'about_story_commitment' => 'nullable|string|max:500',
                'about_story_commitment_en' => 'nullable|string|max:500',
                'about_companion_eyebrow' => 'nullable|string|max:255',
                'about_companion_eyebrow_en' => 'nullable|string|max:255',
                'about_companion_heading' => 'nullable|string|max:500',
                'about_companion_heading_en' => 'nullable|string|max:500',

                'product_detail_brand' => 'nullable|string|max:255',
                'product_detail_brand_en' => 'nullable|string|max:255',
                'product_detail_origin' => 'nullable|string|max:255',
                'product_detail_origin_en' => 'nullable|string|max:255',
                'product_detail_warranty' => 'nullable|string|max:255',
                'product_detail_warranty_en' => 'nullable|string|max:255',
                'product_detail_condition' => 'nullable|string|max:255',
                'product_detail_condition_en' => 'nullable|string|max:255',
                'product_detail_stock_status' => 'nullable|string|max:255',
                'product_detail_stock_status_en' => 'nullable|string|max:255',
                'product_detail_support' => 'nullable|string|max:255',
                'product_detail_support_en' => 'nullable|string|max:255',

                'messagePool' => 'nullable',
                'notification_popup_mode' => 'nullable',
                'notification_popup_name_style' => 'nullable',
                'notification_popup_types' => 'nullable',

                'seo_title_separator' => 'nullable',
                'seo_meta_title' => 'nullable|max:170',
                'seo_meta_description' => 'nullable|max:255',
                'seo_meta_keywords' => 'nullable',
                'seo_meta_robots' => 'nullable',
                'seo_canonical' => 'nullable',
                'seo_image' => 'nullable',
                'seo_schemas' => 'nullable',

                'inject_head' => 'nullable',
                'inject_body_start' => 'nullable',
                'inject_body_end' => 'nullable',
            ],
            'smtp' => [
                'mail_from_address' => 'required|email',
                'mail_from_name' => 'required',
                'mail_username' => 'required',
                'mail_password' => 'required',
                'mail_host' => 'required',
                'mail_port' => 'required',
                'mail_encryption' => 'nullable',
            ],
            'notification' => [
                'notification_production_to' => 'required',
                'notification_staging_to' => 'required',
            ],
            'robots_txt' => [
                'robots_txt'  => 'required',
            ],
            'custom_vars' => [
                'custom_vars'  => 'nullable',
            ],
            'points' => [
                'points_exchange_rate' => 'required|integer|min:1',
            ]
        ][$id] ?? [];
    }

    public static function customVariables($publicOnly = null)
    {
        $settings = cache_response('custom_vars', function () {
            $settings = settings()->group('custom_vars')->all()->first();
            $settings = collect(json_decode($settings, true));

            return $settings;
        }, 'settings');

        if (!is_null($publicOnly)) {
            $settings = $settings->where('is_public', $publicOnly);
        }

        $settings = $settings->pluck('value', 'key')->toArray();

        return $settings;
    }
}
