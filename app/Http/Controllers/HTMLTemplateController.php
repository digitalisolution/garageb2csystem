<?php


// app/Http/Controllers/HTMLTemplateController.php
namespace App\Http\Controllers;
use Mews\Purifier\Facades\Purifier;

use App\Models\HTMLTemplate;
use Illuminate\Http\Request;

class HTMLTemplateController extends Controller
{
    // Display all HTML templates
    public function index()
    {
        $htmlTemplates = HTMLTemplate::orderBy('sort_order', 'asc')->get();// Fetch all HTML templates
        return view('AutoCare.html-templates.index', compact('htmlTemplates'));
    }

    // Show the form for creating a new template
    public function create()
    {
        return view('AutoCare.html-templates.create');
    }

    // Store a newly created template


    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'template_type' => 'required|string',
            'status' => 'required|boolean', // Assuming status is either 1 or 0
            'sort_order' => 'required|integer|min:1', // Sort order should be an integer greater than or equal to 1
        ]);

        // Store the validated data in the database
        HTMLTemplate::create($validated);

        return redirect()->route('AutoCare.html-templates.index');
    }

    // Show the form to edit the selected template
    public function edit($id)
    {
        $htmlTemplate = HTMLTemplate::find($id); // Manually query the template by ID
        // dd($htmlTemplate); // Debugging the data

        return view('AutoCare.html-templates.edit', compact('htmlTemplate'));
    }
    // Update the template information
    public function update(Request $request, $id)
    {
        // Retrieve the template by ID
        $htmlTemplate = HTMLTemplate::find($id);

        if (!$htmlTemplate) {
            return redirect()->route('AutoCare.html-templates.index')->with('error', 'Template not found');
        }

        // Validate the incoming request
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'template_type' => 'required|string',
            'status' => 'required|boolean', // Validate status as either 1 or 0
            'sort_order' => 'required|integer|min:1', // Validate sort_order to be a positive integer
        ]);


        // Update the template
        $htmlTemplate->update($validated);

        // Redirect to index with success message
        return redirect()->route('AutoCare.html-templates.index')->with('success', 'Template updated successfully');
    }


    // Delete the selected template
    public function destroy($id)
    {
        // Retrieve the template by ID
        $htmlTemplate = HTMLTemplate::find($id);

        if (!$htmlTemplate) {
            return redirect()->route('AutoCare.html-templates.index')->with('error', 'Template not found');
        }

        // Delete the template
        $htmlTemplate->delete();

        // Redirect to index with success message
        return redirect()->route('AutoCare.html-templates.index')->with('success', 'Template deleted successfully');
    }

}


?>