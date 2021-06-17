<?php namespace BunkerPalace\BunkerData;

use App;
use Config;
use Backend;
use Event;
use System\Classes\PluginBase;
use Yaml;
use System\Models\File as SystemFile;
use BunkerPalace\BunkerData\Classes\ImageCrush;
use BunkerPalace\BunkerData\Classes\EmbeddedPartialsParser;
use Backend\FormWidgets\RichEditor;

/**
 * BunkerData Plugin Information File
 */
class Plugin extends PluginBase
{

    public $require = ['ToughDeveloper.ImageResizer'];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name' => 'BunkerData',
            'description' => 'October CMS BunkerData plugin',
            'author' => 'Bunker Palace SA',
            'icon' => 'icon-leaf'
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
        // Add BunkerData viewPath

        Event::listen('backend.page.beforeDisplay', function($controller, $action, $params) {

            // Add custom assets

            $controller->addCss('/plugins/bunkerpalace/bunkerdata/assets/css/global.css');

            // Add BunkerData default views

            $bunkerdataViewPath = '~/plugins/bunkerpalace/bunkerdata/views';

            $controller->addViewPath(array(
                $controller->getViewPaths()[0],
                $bunkerdataViewPath
            ));

        });

        Event::listen('backend.list.extendColumns', function($list) {

            $bunkerdataPartialsPath = '~/plugins/bunkerpalace/bunkerdata/widgets/partials';

            // Add custom column types

            foreach($list->columns as $k => $v) {

                if (isset($v['type'])) {

                    // Custom buttons

                    if ($v['type'] == 'custom_buttons') {

                        $override = [
                            'sortable' => false,
                            'clickable' => false,
                            'type' => 'partial',
                            'path' => $bunkerdataPartialsPath . '/_custom_buttons.htm',
                            'actions' => $v['actions'],
                            'actions_button_label' => isset($v['actions_button_label']) ? $v['actions_button_label'] : 'Actions',
                            'width' => '10px'
                        ];

                        $list->addColumns([
                            $k => array_merge($v, $override)
                        ]);

                    }

                    // Live switch

                    if ($v['type'] == 'live_switch') {

                        $override = [
                            'clickable' => false,
                            'type' => 'partial',
                            'path' => $bunkerdataPartialsPath . '/_live_switch.htm',
                            'width' => '10px'
                        ];

                        $list->addColumns([
                            $k => array_merge($v, $override)
                        ]);

                    }

                    // Relation reorder handle

                    if ($v['type'] == 'relation_reorder_handle') {

                        $override = [
                            'clickable' => false,
                            'sortable' => false,
                            'type' => 'partial',
                            'path' => $bunkerdataPartialsPath . '/_relation_reorder_handle.htm',
                            'width' => '10px'
                        ];

                        $list->addColumns([
                            $k => array_merge($v, $override)
                        ]);

                    }

                }

            }

        });

        Event::listen('backend.form.extendFields', function($widget) {

            // MorphOne relations fix in forms

            if (!empty($widget->model->morphOne)) {

                foreach($widget->model->morphOne as $k => $v) {

                    if ($widget->context == 'create' || $widget->model->{$k} == null) {
                        $widget->model->{$k} = new $v[0]();
                    }

                }

            }

        });

        // Add embedded partials functionality to richeditor

        RichEditor::extend(function($widget) {
            
            if (!isset($widget->config->embeddedPartials)) {
                return;
            }

            $partials = json_encode($widget->config->embeddedPartials);

            $widget->addJs('/plugins/bunkerpalace/bunkerdata/assets/js/inject-partials.js?' . uniqid(), [
                'data-partials' => base64_encode($partials),
                'data-widget-id' => $widget->getId()
            ]);

            $widget->addJs('/plugins/bunkerpalace/bunkerdata/assets/js/froala.editorPartials.plugin.js');

        });

    }

    public function registerFormWidgets()
    {
        return [
            'BunkerPalace\BunkerData\FormWidgets\RelationRenderer' => 'relation_renderer',
            'BunkerPalace\BunkerData\FormWidgets\FileUploadFocus' => 'fileuploadfocus',
            'BunkerPalace\BunkerData\FormWidgets\TagListUnique' => 'taglistunique',
            'BunkerPalace\BunkerData\FormWidgets\Video' => 'videoembed'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return [
            'BunkerPalace\BunkerData\Components\LocaleSwitcher' => 'localeSwitcher'
        ];
    }

    public function registerReportWidgets()
    {
        return [
            'BunkerPalace\BunkerData\ReportWidgets\BunkerPalaceRSS' => [
                'label'   => 'Bunker Palace RSS',
                'context' => 'dashboard'
            ]
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'resizecrush' => function($file_path, $width = false, $height = false, $options = []) {
                    $image = new ImageCrush($file_path);
                    return $image->resize($width, $height, $options);
                },
                'parseEmbeddedPartials' => function($html) {
                    return EmbeddedPartialsParser::parse($html);
                }
            ]
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return []; // Remove this line to activate
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return []; // Remove this line to activate
    }

    public function registerListColumnTypes()
    {
        return [];
    }


}
