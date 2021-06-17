<?php namespace BunkerPalace\BunkerData\Behaviors;

use October\Rain\Extension\ExtensionBase;

class RelationReorderController extends ExtensionBase
{

    protected $controller;

    public function __construct($controller) {

        $this->controller = $controller;
        $this->controller->addJs('/plugins/bunkerpalace/bunkerdata/assets/js/relation-reorder.js');

    }

    public function update_onRelationReorder($modelId = null) {

        $reorderedIds = post('reorderedIds');
        $relationName = post('relationName');

        $model = $this->controller->formFindModelObject($modelId);
        $relatedModelClass = get_class($model->{$relationName}()->getRelated());

        $moved = [];
        $position = 0;

        if (is_array($reorderedIds) && count($reorderedIds)) {

            foreach($reorderedIds as $id) {

                if (in_array($id, $moved) || !$record = $relatedModelClass::find($id)) {
                    continue;
                }

                $record->sort_order = $position;
                $record->save();
                $moved[] = $id;
                $position++;

            }

        }

        $this->controller->initForm($model);
        $this->controller->initRelation($model, $relationName);

        return $this->controller->relationRefresh($relationName);

    }

}
