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
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|boolean',
        ]);
        try{
        MetaSettings::create($validated);
        return redirect()->route('AutoCare.meta-settings.index');
        } catch (\Throwable $e) {
            \Log::error("Error storing Meta setting: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
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
            'name' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|boolean',
        ]);
        try{
        $metasettings->update($validated);
        return redirect()->route('AutoCare.meta-settings.index')->with('success', 'Template updated successfully');
        } catch (\Throwable $e) {
            \Log::error("Error updating Meta setting: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }

    public function destroy($setting_id) {
        $metasettings = MetaSettings::find($setting_id);
        if (!$metasettings) {
            return redirect()->route('AutoCare.meta-settings.index')->with('error', 'Template not found');
        }
        try{
        $metasettings->delete();
        return redirect()->route('AutoCare.meta-settings.index')->with('success', 'Template deleted successfully');
        } catch (\Throwable $e) {
            \Log::error("Error deleting Meta setting: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error',  $e->getMessage());
        }
    }
} ?>