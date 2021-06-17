<?php namespace BunkerPalace\testGe;

use Backend;
use Event;
use System\Classes\PluginBase;

use BunkerPalace\MailchimpNewsletter\Models\Newsletter;
use BunkerPalace\MailchimpNewsletter\Controllers\Newsletters;

/**
 * Ocl Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'testGe',
            'description' => 'No description provided yet...',
            'author'      => 'BunkerPalace',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        Event::listen('backend.menu.extendItems', function($manager) {
            $manager->removeMainMenuItem('October.Backend', 'media');
            $manager->removeMainMenuItem('October.Cms', 'cms');
        });
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'BunkerPalace\testGe\Components\Homepage' => 'homepage',
           
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
            'bunkerpalace.testge.access_settings' => [
                'label' => 'Manage the settings',
                'tab' => 'testGe',
                'order' => 0,
            ]
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
            'Events' => [
                'label'       => 'Évènements',
                'url'         => Backend::url('bunkerpalace/testGe/events'),
                'icon'        => 'icon-calendar',
                'permissions' => ['bunkerpalace.testge.manage_content'],
                'order'       => 200,
                'sideMenu' => [
                    'events' => [
                        'label'       => 'Évènements',
                        'url'         => Backend::url('bunkerpalace/testGe/events'),
                        'icon'        => 'icon-calendar',
                        'permissions' => ['bunkerpalace.testge.manage_content'],
                        'order'       => 1,
                    ],
                    
                ]
            ],
        ];
    }

    /**
     * Registers back-end settings for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Réglages du site',
                'description' => 'Éditer les réglages du site',
                'category'    => 'Général',
                'icon'        => 'icon-cog',
                'class'       => 'BunkerPalace\testGe\Models\Settings',
                'order'       => -1,
                'permissions' => ['bunkerpalace.testge.access_settings'],
            ],
            'test' => [
                'label'       => 'tests du site',
                'description' => 'Éditer les tests du site',
                'category'    => 'Général',
                'icon'        => 'icon-cog',
                'class'       => 'BunkerPalace\testGe\Models\Event',
                'order'       => -1,
                'permissions' => ['bunkerpalace.testge.access_event'],
            ]
        ];
    }
}
