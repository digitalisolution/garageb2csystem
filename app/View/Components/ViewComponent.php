<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\View;

abstract class ViewComponent extends Component
{
    /**
     * Determine the component view dynamically based on host.
     *
     * @param string $defaultView
     * @return \Illuminate\Contracts\View\View
     */
    protected function ViewComponent(string $defaultView, array $data = [])
    {
        $host = str_replace('.', '-', request()->getHost());
        $overrideView = "components.override.{$host}.{$defaultView}";

        if (View::exists($overrideView)) {
            return view($overrideView);
        }

        return view("components.{$defaultView}", $data);
    }
}
