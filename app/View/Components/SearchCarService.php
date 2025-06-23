<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\CarserviceProduct;

class SearchCarService extends Component
{
    public $carMakes;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Fetch all car makes from the CarserviceProduct model
        $carserviceProduct = new CarserviceProduct();
        $this->carMakes = $carserviceProduct->getMakes();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.search-car-service');
    }
}
