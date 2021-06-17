<?php namespace BunkerPalace\MailchimpNewsletter;

use Backend;
use System\Classes\PluginBase;

/**
 * Newsletter Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['BunkerPalace.BunkerData'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'Mailchimp Newsletter',
            'description' => 'Manage newsletters backed by mailchimp',
            'author' => 'Bunker Palace SA',
            'icon' => 'icon-envelope'
        ];
    }

    /**
     * Register method, called when the plugin is first registered.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {

    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'BunkerPalace\MailchimpNewsletter\Components\Newsletter' => 'newsletter',
            'BunkerPalace\MailchimpNewsletter\Components\Subscribe' => 'newsletterSubscribe'
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'bunkerpalace.mailchimpnewsletter.manage_newsletter' => [
                'tab' => 'Newsletter',
                'label' => 'Gérer la newsletter'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'bunkerpalace_mcnewsletter' => [
                'label' => 'Newsletter',
                'url' => Backend::url('bunkerpalace/mailchimpnewsletter/newsletters'),
                'icon' => 'icon-envelope',
                'permissions' => ['bunkerpalace.mailchimpnewsletter.manage_newsletter'],
                'order' => 600,
                'sideMenu' => [
                    'newsletters' => [
                        'label' => 'Newsletters',
                        'url' => Backend::url('bunkerpalace/mailchimpnewsletter/newsletters'),
                        'icon' => 'icon-envelope',
                        'permissions' => ['bunkerpalace.mailchimpnewsletter.manage_newsletter'],
                        'order' => 100,
                    ]
                ]
            ],
        ];
    }

    public function registerSettings()
    {
        return [
            'bunkerpalace_newsletter_settings' => [
                'label' => 'Paramètres de la newsletter',
                'description' => 'Editer les paramètres de la newsletter',
                'category' => 'Newsletter',
                'icon' => 'icon-envelope',
                'class' => 'BunkerPalace\MailchimpNewsletter\Models\Settings',
                'order' => 100
            ]
        ];
    }
}
