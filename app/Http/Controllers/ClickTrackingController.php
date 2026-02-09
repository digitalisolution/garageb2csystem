<?php

namespace App\Http\Controllers;
use App\Models\HeaderLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClickTrackingController extends Controller
{
    public function trackPhoneClick(Request $request)
    {
        if ($request->ajax()) {
            $telephone = $request->input('telephone');
            $inputValue = preg_replace("/[^0-9]/", "", $telephone);
            $ipAddress = $request->ip();
            $today = now()->toDateString();

            if (is_numeric($inputValue) && strlen($inputValue) <= 15) {
                DB::table('phone_tracking')->insert([
                    'type'  => $request->input('type', 'phone'),
                    'value' => $telephone,
                    'date'  => $today,
                    'ip'    => $ipAddress,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Invalid number'], 400);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function clickReport()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '24')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['clicks'] = DB::table('phone_tracking')
            ->select('date','value', DB::raw('COUNT(*) as total'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        return view('AutoCare.reports.click-report', $viewData);
    }

}
