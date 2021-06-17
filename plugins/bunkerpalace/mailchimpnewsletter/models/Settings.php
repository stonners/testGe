<?php namespace BunkerPalace\MailchimpNewsletter\Models;

use Model;
use Cms\Classes\Page;
use BunkerPalace\MailchimpNewsletter\Classes\NewsletterManager;

/**
 * Settings Model
 */
class Settings extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $rules = [
        'mailchimp_api_key' => 'required',
        'test_emails' => 'required',
        'from_name' => 'required',
        'reply_to_email' => 'required|email',
        'page_template' => 'required'
    ];

    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'bunkerpalace_mcnewsletter_settings';

    public $settingsFields = 'fields.yaml';

    public $attachOne = [
        'logo' => ['System\Models\File']
    ];

    public function getPageTemplateOptions($value, $formData)
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getListIdOptions()
    {
        return NewsletterManager::instance()->getlistsOptions();
    }

}
