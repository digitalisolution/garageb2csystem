<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\OrderTypes;
use App\Models\TyresProduct;

class SearchTyres extends Component
{
    public $fittingTypes;
    public $vehicleTypes;
    public $widths;

    public function __construct($vehicleType = null)
    {
        // dd($vehicleType);
        $vehicleType = $vehicleType ?? 'car';

        $this->fittingTypes = OrderTypes::where('status', 1)
            ->when($vehicleType, function ($q) use ($vehicleType) {
                $q->where($vehicleType, 1);
            })
            ->get();

        $this->vehicleTypes = TyresProduct::select('vehicle_type')
            ->whereNotNull('vehicle_type')
            ->where('vehicle_type', '!=', '')
            ->where('tyre_quantity', '>', 0)
            ->where('tyre_fullyfitted_price', '>', 0)
            ->where('status', 1)
            ->distinct()
            ->orderBy('vehicle_type')
            ->pluck('vehicle_type');

        $this->widths = TyresProduct::select('tyre_width')
            ->where('tyre_width', '>', 0)
            ->where('tyre_quantity', '>', 0)
            ->where('tyre_fullyfitted_price', '>', 0)
            ->where('status', 1)
            ->distinct()
            ->orderBy('tyre_width')
            ->pluck('tyre_width');
    }

    public function render()
    {
        return view('components.search-tyres', [
            'fittingTypes' => $this->fittingTypes,
            'vehicleTypes' => $this->vehicleTypes,
            'widths' => $this->widths,
        ]);
    }
}
