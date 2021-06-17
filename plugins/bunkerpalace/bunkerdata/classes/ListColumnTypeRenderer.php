<?php namespace BunkerPalace\BunkerData\Classes;

use BunkerPalace\BunkerData\Widgets\LiveSwitch;

class ListColumnTypeRenderer
{
    use \System\Traits\ViewMaker;

    public function __construct() {
        $this->viewPath = '~/plugins/bunkerpalace/bunkerdata/widgets/partials';
    }

}
