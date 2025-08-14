<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\WorkshopTyre;
use App\Models\CustomerDebitLog;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportsController extends Controller
{
    // Show the Reports Dashboard
    public function manage()
    {
        return view('AutoCare.reports.customerReports');
    }

    // Fetch Reports Based on Filters
    public function fetchReports(Request $request)
    {
        $reportType = $request->input('report_type'); // e.g., 'customer', 'invoice', 'tyre', 'payment'
        $timeDuration = $request->input('time_duration'); // e.g., 'today', 'week', 'month', 'year'
        $startDate = $request->input('start_date'); // Custom start date (optional)
        $endDate = $request->input('end_date'); // Custom end date (optional)

        // Calculate date range
        try {
            $dateRange = $this->getDateRange($timeDuration, $startDate, $endDate);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        try {
            switch ($reportType) {
                case 'customer':
                    $data = $this->getCustomerReports($dateRange);
                    break;
                case 'invoice':
                    $data = $this->getInvoiceReports($dateRange);
                    break;
                case 'tyre':
                    $data = $this->getTyreReports($dateRange);
                    break;
                case 'payment':
                    $data = $this->getPaymentReports($dateRange);
                    break;
                default:
                    return response()->json(['success' => false, 'message' => 'Invalid report type']);
            }

            return response()->json(['success' => true, 'data' => $data]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // Helper Function to Get Date Range
    private function getDateRange($timeDuration, $startDate = null, $endDate = null)
    {
        $now = Carbon::now();

        if ($startDate && $endDate) {

            return [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ];
        }

        switch ($timeDuration) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'week':
                return [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()];
            case 'month':
                return [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
            default:
                throw new \Exception('Invalid time duration');
        }
    }


    // Customer Reports
    private function getCustomerReports($dateRange)
    {
        return Customer::whereBetween('customers.created_at', $dateRange)
            ->leftJoin('invoices', function ($join) use ($dateRange) {
                $join->on('customers.id', '=', 'invoices.customer_id')
                    ->whereBetween('invoices.created_at', $dateRange)
                    ->where('invoices.is_void', 0);
            })
            ->select(
                'customers.id as customer_id',
                'customers.customer_name',
                'customers.customer_email',
                DB::raw("DATE(customers.created_at) as created_date"),
                DB::raw('COUNT(invoices.id) as total_invoices'),
                DB::raw('ROUND(COALESCE(SUM(invoices.grandTotal), 0), 2) as total_invoice_amt'),
                DB::raw('ROUND(COALESCE(SUM(invoices.balance_price), 0), 2) as total_due_amt')
            )
            ->groupBy('customers.id', 'customers.customer_name', 'customers.customer_email', 'customers.created_at')
            ->get();
    }

    // Invoice Reports
    private function getInvoiceReports($dateRange)
    {
        return Invoice::select(
            'invoices.workshop_id as id',
            'invoices.name',
            DB::raw("
                    CASE 
                        WHEN invoices.payment_status = 1 THEN 'Paid'
                        WHEN invoices.payment_status = 0 THEN 'Unpaid'
                        WHEN invoices.payment_status = 3 THEN 'Partially Paid'
                        ELSE '-'
                    END as pymt_status
                "),
            DB::raw("
                    CASE 
                        WHEN cdl.payment_type = 1 THEN 'By Cash'
                        WHEN cdl.payment_type = 2 THEN 'By Card'
                        WHEN cdl.payment_type = 3 THEN 'By Check'
                        WHEN cdl.payment_type = 4 THEN 'By Bank'
                        ELSE '-'
                    END as pymt_type
                "),
            'invoices.grandTotal as total_amt',
            'invoices.paid_price as paid',
            'invoices.discount_price as discount',
            'invoices.balance_price as due_amt',
            'invoices.status',
            'invoices.due_out',
        )
            ->leftJoin('workshops as w', 'invoices.workshop_id', '=', 'w.id')
            ->leftJoin('customer_debit_logs as cdl', function ($join) {
                $join->on('cdl.workshop_id', '=', 'w.id')
                    ->where('w.is_converted_to_invoice', 1);
            })
            ->whereBetween('invoices.created_at', $dateRange)
            ->where('invoices.is_void', 0)
            ->groupBy(
                'invoices.workshop_id',
                'invoices.name',
                'invoices.payment_method',
                'invoices.payment_status',
                'invoices.grandTotal',
                'invoices.paid_price',
                'invoices.discount_price',
                'invoices.balance_price',
                'invoices.status',
                'invoices.due_out',
                'cdl.payment_type'
            )
            ->get();
    }



    // Tyre Reports
    private function getTyreReports($dateRange)
    {
        return WorkshopTyre::whereBetween('workshop_tyres.created_at', $dateRange)
            ->where('workshop_tyres.ref_type', 'workshop')
            ->join('workshops', function ($join) {
                $join->on('workshop_tyres.workshop_id', '=', 'workshops.id')
                    ->where('workshops.is_converted_to_invoice', 1)
                    ->where('workshops.is_void', 0);
            })
            ->join('customers', 'workshops.customer_id', '=', 'customers.id')
            ->select(
                'customers.id as customer_id',
                'customers.customer_name',
                'workshop_tyres.workshop_id as invoice_id',
                DB::raw("DATE(workshop_tyres.created_at) as created_date"),
                'workshop_tyres.description',
                DB::raw('ROUND(COALESCE(workshop_tyres.cost_price , 0), 2) as cost_price'),
                DB::raw('ROUND(COALESCE(workshop_tyres.margin_rate , 0), 2) as unit_price'),
                DB::raw('ROUND(COALESCE(workshop_tyres.price , 0), 2) as total_amt'),
                DB::raw('SUM(workshop_tyres.quantity) as total_quantity')
            )
            ->groupBy('customers.id', 'customers.customer_name', 'workshop_tyres.workshop_id', 'workshop_tyres.description', 'workshop_tyres.cost_price', 'workshop_tyres.margin_rate', 'workshop_tyres.price', 'workshop_tyres.created_at') // Group by customer and tyre size
            ->get();

    }

    private function getPaymentReports($dateRange)
    {
        return CustomerDebitLog::whereBetween('customer_debit_logs.payment_date', $dateRange)
            ->join('invoices', 'customer_debit_logs.workshop_id', '=', 'invoices.workshop_id')
            ->leftJoin('customers', 'customer_debit_logs.customer_id', '=', 'customers.id')
            // ->leftJoin('users', 'customer_debit_logs.user_id', '=', 'users.id')
            ->where('invoices.is_void', 0)
            ->select(
                'customer_debit_logs.workshop_id as invoice_id',
                DB::raw("COALESCE(customers.customer_name, '-') as customer_name"),
                DB::raw('DATE(customer_debit_logs.payment_date) as payment_date'),
                DB::raw("
                CASE 
                    WHEN customer_debit_logs.payment_type = 1 THEN 'By Cash'
                    WHEN customer_debit_logs.payment_type = 2 THEN 'By Card'
                    WHEN customer_debit_logs.payment_type = 3 THEN 'By Check'
                    WHEN customer_debit_logs.payment_type = 4 THEN 'By Bank'
                    ELSE 'Unknown'
                END as payment_type
            "),
                'customer_debit_logs.debit_amount',
                DB::raw("COALESCE(customer_debit_logs.comments, '-') as comments"),
                // 'users.name as marked_by'
            )
            ->orderBy('customer_debit_logs.payment_date', 'desc')
            ->get();
    }

}