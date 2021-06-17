<?php namespace BunkerPalace\BunkerData\FormWidgets;

use Backend\Classes\FormWidgetBase;
use Backend\FormWidgets\TagList;

/**
 * RelationRenderer Form Widget
 */
class TagListUnique extends TagList
{
    protected $defaultAlias = 'taglistunique';

    /**
     * @inheritDoc
     */
    public function render()
    {
        $this->prepareVars();
        return $this->makePartial('taglistunique');
    }

    /**
     * @inheritDoc
     */
    public function getSaveValue($value)
    {
        if ($this->mode === static::MODE_RELATION) {
            return $this->hydrateRelationSaveValue($value)[0];
        }

        if (is_array($value) && $this->mode === static::MODE_STRING) {
            return implode($this->getSeparatorCharacter(), $value);
        }

        return is_array($value) && !empty($value) ? $value[0] : NULL;
    }

}
