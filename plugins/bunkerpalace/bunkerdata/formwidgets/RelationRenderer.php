<?php namespace BunkerPalace\BunkerData\FormWidgets;

use Backend\Classes\FormWidgetBase;

/**
 * RelationRenderer Form Widget
 */
class RelationRenderer extends FormWidgetBase
{
    /**
     * {@inheritDoc}
     */
    protected $defaultAlias = 'bunkerpalace_bunkerdata_relation_renderer';

    /**
     * {@inheritDoc}
     */
    public function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('relation_renderer');
    }

    /**
     * Prepares the form widget view data
     */
    public function prepareVars()
    {
        $this->vars['fieldName'] = $this->formField->fieldName;
    }

    public function getSaveValue($value)
    {
        return \Backend\Classes\FormField::NO_SAVE_DATA;
    }

}
