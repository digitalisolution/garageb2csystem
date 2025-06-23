<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\OrderTypes; // Import the OrderType model

class SearchTyres extends Component
{
    public $fittingTypes; // Variable to hold fitting types

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Fetch active fitting types from the database
        $this->fittingTypes = OrderTypes::where('status', 1)->get();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        // Pass the fitting types to the view
        return view('components.search-tyres', [
            'fittingTypes' => $this->fittingTypes,
        ]);
    }
}