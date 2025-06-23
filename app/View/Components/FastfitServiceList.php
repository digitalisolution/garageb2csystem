<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\CarService;
class FastfitServiceList extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $services;

    public function __construct()
    {
        // Fetch all services from the database
        // $this->services = CarService::all();
        $this->services = \DB::table('car_services')
            ->where('status', 1)
            ->where('display_status', 1)
            ->whereNotNull('slug')  // Ensure slug is not NULL
            ->where('slug', '!=', '') // Ensure slug is not empty
            ->get();

        // dd($services);

        // return view('view.service', compact('services'));
        // dd($this->services);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.fastfit-service-list');
    }
}
