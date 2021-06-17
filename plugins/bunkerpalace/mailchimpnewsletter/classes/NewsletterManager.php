<?php namespace BunkerPalace\MailchimpNewsletter\Classes;

use App;
use ApplicationException;
use BunkerPalace\MailchimpNewsletter\Models\Settings;
use DrewM\MailChimp\MailChimp;
use Cms\Classes\Page;

class NewsletterManager
{
    use \October\Rain\Support\Traits\Singleton;

    public $api = null;

    protected $settings;

    protected function init()
    {
        $this->settings = Settings::instance();

        if ($this->settings->mailchimp_api_key) {
            $this->api = new MailChimp($this->settings->mailchimp_api_key);
        }
    }

    protected function onApiError()
    {
        throw new ApplicationException($this->api->getLastError());
    }

    protected function getResponseAsOptionsList($endpoint, $itemsKey)
    {
        if (!$this->api) {
            return [];
        }

        $items = $this->api->get($endpoint);
        $options = [];

        foreach ($items[$itemsKey] as $item) {
            $options[$item['id']] = $item['name'];
        }

        return $options;
    }

    public function getListsOptions()
    {
        return $this->getResponseAsOptionsList('lists', 'lists');
    }

    public function getSegmentsOptions($listId)
    {
        if (!$listId) {
            return [
                0 => "Envoyer à toute la liste"
            ];
        }

        return ([
            0 => "Envoyer à toute la liste"
        ] + $this->getResponseAsOptionsList('lists/' . $listId . '/segments', 'segments'));
    }

    public function getNewsletterPageUrl($modelId)
    {
        $pageName = $this->settings->page_template;

        if (!$pageName) {
            throw new ApplicationException("The newsletter page template is not specified in settings");
        }

        return Page::url($pageName, ['id' => $modelId]);
    }

    public function createOrUpdateCampaign($config, $campaignId = null)
    {
        if (!$this->api) {
            throw new ApplicationException("Mailchimp API is not properly configured");
        }

        $defaults = array(
            'type' => 'regular',
            'recipients' => [
                'segment_opts' => [
                    'saved_segment_id' => 0
                ]
            ],
            'settings' => array(
                'from_name' => $this->settings->from_name,
                'reply_to' => $this->settings->reply_to_email,
                'inline_css' => true
            )
        );

        $payload = array_replace_recursive($defaults, $config);

        if ($campaignId != null) {
            $resp = $this->api->patch('campaigns/' . $campaignId, $payload);
        } else {
            $resp = $this->api->post('campaigns', $payload);
        }

        if (!$this->api->success()) {
            $this->onApiError();
        }

        return $resp;
    }

    public function updateCampaignContent($campaignId, $content)
    {
        $resp = $this->api->put('campaigns/' . $campaignId . '/content', array(
            'html' => $content
        ));

        if (!$this->api->success()) {
            $this->onApiError();
        }

        return $resp;
    }

    public function sendTestCampaign($campaignId)
    {
        $testEmails = explode(',', $this->settings->test_emails);

        $resp = $this->api->post('campaigns/' . $campaignId . '/actions/test', array(
            'test_emails' => $testEmails,
            'send_type' => 'html'
        ));

        if (!$this->api->success()) {
            $this->onApiError();
        }

        return $this->settings->test_emails;
    }

    public function sendCampaign($campaignId)
    {
        $resp = $this->api->post('campaigns/' . $campaignId . '/actions/send');

        if (!$this->api->success()) {
            $this->onApiError();
        }

        return $resp;
    }

    public function subscribe($subscription)
    {
        $subscriberHash = md5(strtolower($subscription['email_address']));

        $action = sprintf('lists/%s/members/%s', $this->settings->list_id, $subscriberHash);

        $resp = $this->api->put($action, $subscription);

        return $resp;
    }

    public function getSettings()
    {
        return $this->settings;
    }
}
