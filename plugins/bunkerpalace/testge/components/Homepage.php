<?php namespace BunkerPalace\TestGe\Components;

use Cms\Classes\ComponentBase;
use BunkerPalace\TestGe\Models\Event;

class Homepage extends ComponentBase
{
    public $events;
    public function componentDetails()
    {
        return [
            'name'        => 'Homepage Component',
            'description' => 'No description provided yet...'
        ];
    }
    public function onRun(){
        $this->getEvent();
    }

    public function getEvent()
    {
        $self=$this;
        $query = Event::all();
        $self->events = $query;
       
        return $query;
    }

    public function defineProperties()
    {
        return [];
    }
}
