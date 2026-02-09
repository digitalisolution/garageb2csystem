<?php


namespace App\Http\Controllers;

use App\Models\HTMLTemplate;
use App\Models\HeaderLink;
use Illuminate\Http\Request;

class HTMLTemplateController extends Controller
{
    // Display all HTML templates
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '20')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['htmlTemplates'] = HTMLTemplate::orderBy('sort_order', 'asc')->get();
        return view('AutoCare.html-templates.index', $viewData);
    }

    // Show the form for creating a new template
    public function create()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '20')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        return view('AutoCare.html-templates.create', $viewData);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'content' => 'required|string',
                'template_type' => 'required|string|max:100',
                'status' => 'required|boolean|max:1',
                'sort_order' => 'required|integer|min:1',
            ]);

            // Store the validated data in the database
            HTMLTemplate::create($validated);

            return redirect()->route('AutoCare.html-templates.index');
        } catch (\Throwable $e) {
            \Log::error("Error creating Html Template: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    // Show the form to edit the selected template
    public function edit($id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '20')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['htmlTemplate'] = HTMLTemplate::find($id);
        if (!$viewData['htmlTemplate']) {
            return redirect()->route('AutoCare.html-templates.index')->with('error', 'Template not found');
        }
        return view('AutoCare.html-templates.edit', $viewData);
    }
    // Update the template information
    public function update(Request $request, $id)
    {
        try {
            // Retrieve the template by ID
            $htmlTemplate = HTMLTemplate::find($id);

            if (!$htmlTemplate) {
                return redirect()->route('AutoCare.html-templates.index')->with('error', 'Template not found');
            }

            // Validate the incoming request
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'content' => 'required|string',
                'template_type' => 'required|string|max:100',
                'status' => 'required|boolean|max:1',
                'sort_order' => 'required|integer|min:1',
            ]);


            // Update the template
            $htmlTemplate->update($validated);

            // Redirect to index with success message
            return redirect()->route('AutoCare.html-templates.index')->with('success', 'Template updated successfully');
        } catch (\Throwable $e) {
            \Log::error("Error updating Html Template: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    // Delete the selected template
    public function destroy($id)
    {
        try {
            // Retrieve the template by ID
            $htmlTemplate = HTMLTemplate::find($id);

            if (!$htmlTemplate) {
                return redirect()->route('AutoCare.html-templates.index')->with('error', 'Template not found');
            }

            // Delete the template
            $htmlTemplate->delete();

            // Redirect to index with success message
            return redirect()->route('AutoCare.html-templates.index')->with('success', 'Template deleted successfully');
        } catch (\Throwable $e) {
            \Log::error("Error deleting Html Template: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

}


?>