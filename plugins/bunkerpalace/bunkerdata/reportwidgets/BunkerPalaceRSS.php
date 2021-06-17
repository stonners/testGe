<?php namespace BunkerPalace\BunkerData\ReportWidgets;

use Backend\Classes\ReportWidgetBase;
use ApplicationException;
use Exception;

/**
 * Google Analytics browsers overview widget.
 *
 * @package backend
 * @author Alexey Bobkov, Samuel Georges
 */
class BunkerPalaceRSS extends ReportWidgetBase
{
    /**
     * Renders the widget.
     */
    public function render()
    {
        try {
            $this->loadData();
        }
        catch (Exception $ex) {
            $this->vars['error'] = $ex->getMessage();
        }

        return $this->makePartial('widget');
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'backend::lang.dashboard.widget_title_label',
                'default'           => 'Bunker Palace',
                'type'              => 'string',
                'validationPattern' => '^.+$',
                'validationMessage' => 'backend::lang.dashboard.widget_title_error'
            ]
        ];
    }

    protected function loadData()
    {
        try {
            $projects = json_decode(file_get_contents('http://www.bunkerpalace.com/projects/index.json?limit=6'), true);
        } catch (Exception $e) {
            throw new ApplicationException('Error while getting Bunker Palace projects');
        }

        $this->vars['projects'] = $projects;
    }
}
