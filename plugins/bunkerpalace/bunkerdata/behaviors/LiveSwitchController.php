<?php namespace BunkerPalace\BunkerData\Behaviors;

use October\Rain\Extension\ExtensionBase;
use BunkerPalace\BunkerData\Widgets\LiveSwitch;

class LiveSwitchController extends ExtensionBase
{

    protected $controller;

    public function __construct($controller) {

        $this->controller = $controller;

    }

    public function onLiveSwitch() {

        $className = "\\" . post('className');
        $model = new $className;

        $id = post('id');
        $col = post('col');
        $value = (int)!post('live_switch[' . $col . ']');

        $model::where('id', $id)->update([$col => $value]);

        return [
            'value' => $value
        ];

    }

}
