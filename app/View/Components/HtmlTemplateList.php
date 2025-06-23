<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\HTMLTemplate;
class HtmlTemplateList extends Component
{
    public $templates;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->templates = HTMLTemplate::where('status', 1)
            ->orderBy('sort_order', 'asc') // or 'desc' depending on the desired order
            ->get();

    }
    public function render()
    {
        return view('components.html-template-list');
    }
}
