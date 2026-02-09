<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CreditLogService;
use App\Models\VrmSmsCredit;
use Illuminate\Support\Facades\DB;

class VrmSmsCreditController extends Controller
{
    protected $creditLogService;

     public function __construct(CreditLogService $creditLogService)
    {
        $this->creditLogService = $creditLogService;
    }
public function index(Request $request)
{
    $start_date = $request->input('start_date');
    $end_date = $request->input('end_date');
    $reference = $request->input('reference', 'vrm');

    $show_data = $request->has('start_date') 
                || $request->has('end_date') 
                || $request->has('reference');

    $vrmLogs = DB::table('vrm_credit_logs')->where('credit_type', 'vrm');

    $total_vrm_used = $vrmLogs->clone()
        ->whereNotNull('credit_used')
        ->sum(DB::raw("CAST(credit_used AS SIGNED)"));

    $latest_balance = DB::table('vrm_credit_logs')
        ->where('credit_type', 'vrm')
        ->whereNotNull('credit_available')
        ->orderBy('used_date', 'desc')
        ->value('credit_available') ?? 0;

    $total_vrm_credit = $total_vrm_used + $latest_balance;
    $remaining_vrm = $latest_balance;

    $smsLogs = DB::table('vrm_credit_logs')->where('credit_type', 'sms');
    $total_sms_used = $smsLogs->clone()
        ->sum(DB::raw("CAST(credit_used AS SIGNED)"));
    $remaining_sms = DB::table('vrm_credit_logs')
        ->where('credit_type', 'sms')
        ->whereNotNull('credit_available')
        ->orderBy('used_date', 'desc')
        ->value('credit_available') ?? 0;
    $total_sms_credit = $total_sms_used + $remaining_sms;

    $autodataLogs = DB::table('vrm_credit_logs')->where('credit_type', 'autodata');
    $total_autodata_used = $autodataLogs->clone()
        ->sum(DB::raw("CAST(credit_used AS SIGNED)"));
    $remaining_autodata = DB::table('vrm_credit_logs')
        ->where('credit_type', 'autodata')
        ->whereNotNull('credit_available')
        ->orderBy('used_date', 'desc')
        ->value('credit_available') ?? 0;
    $total_autodata_credit = $total_autodata_used + $remaining_autodata;

    $data = collect();

    if ($show_data) {
        if ($reference === 'vrm') {
            $query = DB::table('vrm_credit_logs')
                ->where('credit_type', 'vrm')
                ->whereNotNull('vrm');

            if ($start_date) {
                $query->whereDate('used_date', '>=', $start_date);
            }
            if ($end_date) {
                $query->whereDate('used_date', '<=', $end_date);
            }

           $data = $query->select(
        'vrm as name',
        DB::raw("DATE(used_date) as date"),
        DB::raw("SUM(CAST(credit_used AS SIGNED)) as quantity")
    )
    ->groupBy('vrm', DB::raw("DATE(used_date)"))
    ->orderBy('date', 'ASC')
    ->get();


        } else {
            $query = DB::table('vrm_credit_logs')
                ->where('credit_type', $reference);

            if ($start_date) {
                $query->whereDate('used_date', '>=', $start_date);
            }
            if ($end_date) {
                $query->whereDate('used_date', '<=', $end_date);
            }

            $data = $query->select(
        'vrm as name',
        DB::raw("DATE(used_date) as date"),
        DB::raw("SUM(CAST(credit_used AS SIGNED)) as quantity")
    )
    ->groupBy('vrm', DB::raw("DATE(used_date)"))
    ->orderBy('date', 'ASC')
    ->get();

        }
    }

    $purchaseHistory = DB::table('vrm_sms_credits')->orderBy('created_at', 'ASC')->get();

    return view('AutoCare.vrmsmscredit.index', compact(
        'data',
        'start_date',
        'end_date',
        'reference',
        'purchaseHistory',
        'show_data',
        'total_vrm_used',
        'total_sms_used',
        'total_autodata_used',
        'total_vrm_credit',
        'total_sms_credit',
        'total_autodata_credit',
        'remaining_vrm',
        'remaining_sms',
        'remaining_autodata'
    ));
}

public function buyCredits(Request $request)
{
    $request->validate([
        'type' => 'required|in:vrm,sms,autodata',
        'quantity' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
    ]);

    $type = $request->type;
    $qty = $request->quantity;
    $pricePerUnit = $request->price;

    $adminUserId = auth()->id() ?? 1;
       
    try {
        $result = $this->creditLogService->addVrmCredit(
            credit_type: $type,
            userId: $adminUserId,
            amount: $qty,
            pricePerUnit: $pricePerUnit,
            reason: "Purchased {$qty} {$type} credits",
            origin: 'admin'
        );
        return back()->with('msg', "✅ {$qty} {$type} credits added. New balance: {$result['new_balance']}.");

    } catch (\Exception $e) {
        \Log::error('Credit purchase failed', [
            'type' => $type,
            'qty' => $qty,
            'user_id' => $adminUserId,
            'error' => $e->getMessage(),
        ]);

        return back()->withErrors(['error' => 'Failed to add credits: ' . $e->getMessage()]);
    }
}

}
