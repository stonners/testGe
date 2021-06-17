<?php namespace BunkerPalace\MailchimpNewsletter\Models;

use Model;
use BunkerPalace\MailchimpNewsletter\Classes\NewsletterManager;

/**
 * Newsletter Model
 */
class Newsletter extends Model
{

    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'bunkerpalace_mcnewsletter_newsletters';

    public $rules = [
        'title' => 'required',
        'email_subject' => 'required',
        'mc_list_id' => 'required'
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [
        'thumbnail' => ['System\Models\File']
    ];
    public $attachMany = [];

    protected $newsletterManager;

    public function __construct()
    {
        parent::__construct();
        $this->newsletterManager = NewsletterManager::instance();
    }

    public function getMcSegmentIdOptions()
    {
        return $this->newsletterManager->getSegmentsOptions($this->mc_list_id);
    }

    public function getMcListIdOptions()
    {
        return $this->newsletterManager->getlistsOptions();
    }

    public function getUrlAttribute()
    {
        return $this->newsletterManager->getNewsletterPageUrl($this->id);
    }

    public function getHtmlContentAttribute()
    {
        return file_get_contents($this->url);
    }

    public function beforeSave()
    {
        if (!$this->sent_at) {

            $config = array(
                'recipients' => [
                    'list_id' => $this->mc_list_id,
                    'segment_opts' => [
                        'saved_segment_id' => (int)$this->mc_segment_id
                    ]
                ],
                'settings' => array(
                    'subject_line' => $this->email_subject,
                    'title' => $this->title
                )
            );
            
            $resp = $this->newsletterManager->createOrUpdateCampaign($config, $this->mc_campaign_id);
            $this->mc_campaign_id = $resp['id'];
            $this->mc_web_id = $resp['web_id'];

        }
    }

}
