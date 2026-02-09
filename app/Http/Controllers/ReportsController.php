<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Garage;
use App\Models\WorkshopTyre;
use App\Models\CustomerDebitLog;
use App\Models\Invoice;
use App\Models\HeaderLink;
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
        $viewData['header_link'] = HeaderLink::where("menu_id", '16')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['garages'] = Garage::orderBy('garage_name')->get();
        return view('AutoCare.reports.customerReports', $viewData);
    }

    // Fetch Reports Based on Filters
    public function fetchReports(Request $request)
    {
        $reportType = $request->input('report_type');
        $timeDuration = $request->input('time_duration');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $garageId = $request->input('garage_id');

        // Calculate date range
        try {
            $dateRange = $this->getDateRange($timeDuration, $startDate, $endDate);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }

        try {
            switch ($reportType) {
                case 'garage':
                    $data = $this->getGarageReports($dateRange, $garageId);
                    break;
                case 'customer':
                    $data = $this->getCustomerReports($dateRange, $garageId);
                    break;
                case 'invoice':
                    $data = $this->getInvoiceReports($dateRange, $garageId);
                    break;
                case 'tyre':
                    $data = $this->getTyreReports($dateRange, $garageId);
                    break;
                case 'apc':
                    $data = $this->getApcReports($dateRange, $garageId);
                    break;
                case 'payment':
                    $data = $this->getPaymentReports($dateRange, $garageId);
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
    private function getCustomerReports($dateRange, $garageId = null)
    {
        return Customer::whereBetween('customers.created_at', $dateRange)
            ->leftJoin('invoices', function ($join) use ($dateRange, $garageId) {
                $join->on('customers.id', '=', 'invoices.customer_id')
                    ->whereBetween('invoices.created_at', $dateRange)
                    ->where('invoices.is_void', 0);

                if ($garageId) {
                    $join->where('invoices.garage_id', $garageId);
                }
            })

            ->select(
                //'customers.id as customer_id',
                //'customers.customer_name',
                //'customers.customer_email',
                DB::raw("COALESCE(customers.id) as `customer id`"),
                DB::raw("COALESCE(customers.customer_name) as `customer name`"),
                DB::raw("COALESCE(customers.customer_email) as `customer email`"),
                DB::raw("DATE(customers.created_at) as `created date`"),
                DB::raw('COUNT(invoices.id) as `total invoices`'),
                DB::raw('ROUND(COALESCE(SUM(invoices.grandTotal), 0), 2) as total_invoice_amt'),
                DB::raw('ROUND(COALESCE(SUM(invoices.balance_price), 0), 2) as total_due_amt')
            )
            ->groupBy('customers.id', 'customers.customer_name', 'customers.customer_email', 'customers.created_at')
            ->get();
    }

    private function getGarageReports($dateRange, $garageId = null)
    {
        $query = Garage::leftJoin('invoices', function ($join) use ($dateRange) {
            $join->on('garages.id', '=', 'invoices.garage_id')
                ->whereBetween('invoices.created_at', $dateRange)
                ->where('invoices.is_void', 0);
        })
            ->select(
                //'garages.id as garage_id',
                //'garages.garage_name',
                //'garages.garage_email',
                DB::raw("COALESCE(garages.id) as `garage id`"),
                DB::raw("COALESCE(garages.garage_name) as `garage name`"),
                DB::raw("COALESCE(garages.garage_email) as `garage email`"),
                DB::raw("DATE(garages.created_at) as `created date`"),
                DB::raw('COUNT(invoices.id) as `total invoices`'),
                DB::raw('ROUND(COALESCE(SUM(invoices.grandTotal), 0), 2) as `total invoice amt`'),
                DB::raw('ROUND(COALESCE(SUM(invoices.balance_price), 0), 2) as `total due amt`')
            )
            ->groupBy('garages.id', 'garages.garage_name', 'garages.garage_email', 'garages.created_at');

        if ($garageId) {
            $query->where('garages.id', $garageId); 
        }

        return $query->get();
    }

    // Invoice Reports
    private function getInvoiceReports($dateRange, $garageId = null)
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
            ->leftJoin('customer_debit_logs as cdl', function ($join) use ($dateRange) {
                $join->on('cdl.workshop_id', '=', 'w.id')
                    ->whereBetween('cdl.payment_date', $dateRange);
            })
            ->whereBetween('invoices.created_at', $dateRange)
            ->where('invoices.is_void', 0)
            ->when($garageId, function ($q) use ($garageId) {
                $q->where('invoices.garage_id', $garageId);
            })
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
    private function getTyreReports($dateRange, $garageId = null)
    {
        return WorkshopTyre::whereBetween('workshop_tyres.created_at', $dateRange)
            ->where('workshop_tyres.ref_type', 'workshop')
            ->when($garageId, function ($q) use ($garageId) {
                $q->where('workshops.garage_id', $garageId);
            })
            ->join('workshops', function ($join) {
                $join->on('workshop_tyres.workshop_id', '=', 'workshops.id')
                    ->where('workshops.is_converted_to_invoice', 1)
                    ->where('workshops.is_void', 0);
            })
            ->join('customers', 'workshops.customer_id', '=', 'customers.id')
            ->select(
                // 'customers.id as cst_id',
                'customers.customer_name as Cst.Name',
                'workshop_tyres.workshop_id as inv_id',
                'workshop_tyres.product_ean as ean',
                'workshop_tyres.supplier as supplier',
                DB::raw("COALESCE(workshop_tyres.fitting_type) as `order type`"),
                DB::raw("DATE(workshop_tyres.created_at) as `created date`"),
                'workshop_tyres.description',
                DB::raw('ROUND(COALESCE(workshop_tyres.cost_price , 0), 2) as cost_price'),
                DB::raw('ROUND(COALESCE(workshop_tyres.margin_rate , 0), 2) as unit_price'),
                DB::raw('ROUND(COALESCE(workshop_tyres.price , 0), 2) as total_amt'),
                DB::raw('SUM(workshop_tyres.quantity) as total_qty')
            )
            ->groupBy('customers.id', 'customers.customer_name', 'workshop_tyres.workshop_id', 'workshop_tyres.product_ean', 'workshop_tyres.supplier', 'workshop_tyres.description', 'workshop_tyres.fitting_type', 'workshop_tyres.cost_price', 'workshop_tyres.margin_rate', 'workshop_tyres.price', 'workshop_tyres.created_at') // Group by customer and tyre size
            ->get();

    }

    private function getApcReports($dateRange, $garageId = null)
    {
        return WorkshopTyre::whereBetween('workshop_tyres.created_at', $dateRange)
            ->where('workshop_tyres.ref_type', 'workshop')
            ->where('workshop_tyres.fitting_type', 'mailorder')
            ->when($garageId, function ($q) use ($garageId) {
                $q->where('workshops.garage_id', $garageId);
            })
            ->join('workshops', function ($join) {
                $join->on('workshop_tyres.workshop_id', '=', 'workshops.id')
                    ->where('workshops.is_void', 0);
            })
            ->join('customers', 'workshops.customer_id', '=', 'customers.id')
            ->select(
                DB::raw("CONCAT(workshop_tyres.workshop_id) as `order no.`"),
                DB::raw("DATE(workshop_tyres.created_at) as `created date`"),
                DB::raw("COALESCE(workshops.name, '') as `first name`"),
                DB::raw("COALESCE(workshops.last_name, '') as `last name`"),
                'workshops.mobile as phone',
                'workshops.email as email',
                DB::raw("COALESCE(workshops.address) as address"),
                DB::raw("COALESCE(workshops.city) as `Town / City`"),
                DB::raw("COALESCE(workshops.county) as county"),
                DB::raw("COALESCE(workshops.zone) as postcode"),
                DB::raw("COALESCE(workshops.country) as country"),
                'workshop_tyres.product_ean as ean',
                'workshop_tyres.product_sku as sku',
                'workshop_tyres.description',
                DB::raw("COALESCE(workshop_tyres.tyre_weight, '10') as weight"),
                DB::raw("COALESCE(workshop_tyres.fitting_type) as `order type`"),
                'workshop_tyres.shipping_postcode as postcode',
                DB::raw('SUM(workshop_tyres.quantity) as quantity'),
                DB::raw("COALESCE(workshops.notes, '') as notes"),
            )

            ->groupBy(
                'customers.id',
                'workshops.workshop_origin',
                'workshops.vehicle_reg_number',
                'workshops.payment_method',
                'workshops.notes',
                'workshops.name',
                'workshops.last_name',
                'workshops.mobile',
                'workshops.email',
                'workshops.address',
                'workshops.city',
                'workshops.zone',
                'workshops.county',
                'workshops.country',
                'workshop_tyres.workshop_id',
                'workshop_tyres.product_ean',
                'workshop_tyres.product_sku',
                'workshop_tyres.supplier',
                'workshop_tyres.description',
                'workshop_tyres.fitting_type',
                'workshop_tyres.cost_price',
                'workshop_tyres.margin_rate',
                'workshop_tyres.price',
                'workshop_tyres.created_at'
            )
            ->get();
    }

    private function getPaymentReports($dateRange, $garageId = null)
    {
        return CustomerDebitLog::whereBetween('customer_debit_logs.payment_date', $dateRange)
            ->join('invoices', 'customer_debit_logs.workshop_id', '=', 'invoices.workshop_id')
            ->leftJoin('customers', 'customer_debit_logs.customer_id', '=', 'customers.id')
            // ->leftJoin('users', 'customer_debit_logs.user_id', '=', 'users.id')
            ->where('invoices.is_void', 0)
            ->when($garageId, function ($q) use ($garageId) {
                $q->where('invoices.garage_id', $garageId);
            })
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