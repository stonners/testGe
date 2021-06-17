<?php namespace BunkerPalace\MailchimpNewsletter\Components;

use Cms\Classes\ComponentBase;
use BunkerPalace\MailchimpNewsletter\Models\Newsletter as NewsletterModel;
use BunkerPalace\MailchimpNewsletter\Models\Settings as NewsletterSettings;

class Newsletter extends ComponentBase
{

    public $data;
    public $settings;

    public function componentDetails()
    {
        return [
            'name'  => 'Newsletter Component',
            'description' => 'Display a fully rendered newsletter'
        ];
    }

    public function onRun()
    {
        $this->settings = NewsletterSettings::instance();
        $this->data = NewsletterModel::find($this->property('id'));
    }

}
