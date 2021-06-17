<?php namespace BunkerPalace\MailchimpNewsletter\Components;

use Cms\Classes\ComponentBase;
use Validator;
use ValidationException;
use BunkerPalace\MailchimpNewsletter\Classes\NewsletterManager;

class Subscribe extends ComponentBase
{

    public $settings;

    protected $newsletterManager;

    public function componentDetails()
    {
        return [
            'name'  => 'Newsletter Subscribe Component',
            'description' => 'Allow users to subscribe to your newsletter'
        ];
    }

    public function init()
    {
        $this->newsletterManager = NewsletterManager::instance();
        $this->settings = $this->newsletterManager->getSettings();
    }

    public function onSubscribe()
    {

        $rules = [
            'email' => 'required|email'
        ];

        $validator = Validator::make(input(), $rules);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $this->page['error'] = null;

        $subscription = [
            'email_address' => input('email'),
            'status' => 'subscribed'
        ];

        $mergeFields = input('merge', []);

        if (!empty($mergeFields)) {
            $subscription['merge_fields'] = $mergeFields;
        }

        $interests = input('interests', []);

        foreach($interests as $interestId) {
            $subscription['interests'][$interestId] = true;
        }

        $resp = $this->newsletterManager->subscribe($subscription);

        if (!$resp || $resp['status'] != 'subscribed') {
            $this->page['error'] = true;
        }
    }

}
