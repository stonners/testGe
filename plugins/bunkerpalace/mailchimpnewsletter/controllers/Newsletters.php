<?php namespace BunkerPalace\MailchimpNewsletter\Controllers;

use BackendMenu;
use Backend\Classes\Controller;
use BunkerPalace\MailchimpNewsletter\Models\Newsletter;
use BunkerPalace\MailchimpNewsletter\Classes\NewsletterManager;
use Flash;
use Redirect;
use Carbon\Carbon;

/**
 * Newsletters Back-end Controller
 */
class Newsletters extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = '$/bunkerpalace/testge/controllers/events/config_relation_add.yaml';

    protected $newsletterManager;

    public function __construct()
    {
        parent::__construct();

        $this->newsletterManager = NewsletterManager::instance();

        BackendMenu::setContext('BunkerPalace.MailchimpNewsletter', 'bunkerpalace_mcnewsletter', 'newsletters');
    }

    protected function getNewsletterModel()
    {
        $id = input('newsletter_id');
        return Newsletter::find($id);
    }

    public function onSendTest()
    {
        $newsletter = $this->getNewsletterModel();
        $testEmails = $this->newsletterManager->sendTestCampaign($newsletter->mc_campaign_id);
        Flash::success('Un test a été envoyé à ' . $testEmails);
    }

    public function onSend()
    {
        $newsletter = $this->getNewsletterModel();
        $resp = $this->newsletterManager->sendCampaign($newsletter->mc_campaign_id);

        $newsletter->sent_at = Carbon::now();
        $newsletter->save();

        Flash::success('La newsletter a été envoyée');
        return Redirect::refresh();
    }

    public function formAfterSave($model)
    {
        if (!$model->sent_at) {
            $this->newsletterManager->updateCampaignContent($model->mc_campaign_id, $model->html_content);
        }
    }

}
