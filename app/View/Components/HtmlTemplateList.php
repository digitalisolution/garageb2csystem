<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\HTMLTemplate;

class HtmlTemplateList extends ViewComponent
{
    public $templates;
    public $templateName;

    public function __construct($templateName = null)
    {
        $this->templateName = $templateName;

        $query = HTMLTemplate::where('status', 1);

        if ($templateName) {
            $query->where('title', $templateName);
        }

        $this->templates = $query->orderBy('sort_order', 'asc')->get();
    }

    public function render()
    {
        return $this->ViewComponent('html-template-list');
    }
}
