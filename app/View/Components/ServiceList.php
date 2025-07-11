<?php

namespace App\View\Components;

use App\Models\CarService;
class ServiceList extends ViewComponent
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $services;

    public function __construct()
    {
        $this->services = CarService::where('status', 1)->where('display_status', 1)->get();

    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return $this->ViewComponent('service-list');
    }
}
