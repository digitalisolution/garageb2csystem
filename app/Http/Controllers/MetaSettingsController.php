<?php


// app/Http/Controllers/MetaSettingsController.php
namespace App\Http\Controllers;
use Mews\Purifier\Facades\Purifier;

use App\Models\MetaSettings;
use Illuminate\Http\Request;

class MetaSettingsController extends Controller {
    public function index() {
        $metasettings = MetaSettings::orderBy('status', 'asc')->get();
        return view('AutoCare.meta-settings.index', compact('metasettings'));
    }

    public function create() {
        return view('AutoCare.meta-settings.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|boolean',
        ]);
        MetaSettings::create($validated);
        return redirect()->route('AutoCare.meta-settings.index');
    }

    public function edit($setting_id) {
        $metasettings = MetaSettings::where('setting_id', $setting_id)->first();
        return view('AutoCare.meta-settings.edit', compact('metasettings'));
    }

    public function update(Request $request, $setting_id) {
        $metasettings = MetaSettings::find($setting_id);
        if (!$metasettings) {
            return redirect()->route('AutoCare.meta-settings.index')->with('error', 'Template not found');
        }
        $validated = $request->validate([
            'name' => 'required|string',
            'content' => 'required|string',
            'status' => 'required|boolean',
        ]);
        $metasettings->update($validated);
        return redirect()->route('AutoCare.meta-settings.index')->with('success', 'Template updated successfully');
    }

    public function destroy($setting_id) {
        $metasettings = MetaSettings::find($setting_id);
        if (!$metasettings) {
            return redirect()->route('AutoCare.meta-settings.index')->with('error', 'Template not found');
        }
        $metasettings->delete();
        return redirect()->route('AutoCare.meta-settings.index')->with('success', 'Template deleted successfully');
    }
} ?>