<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Garage;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\Workshop;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\HeaderLink;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\StatementEmail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
class GaragesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request)
    {
        // dd(session::all());
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();

        $garagesQuery = DB::table('garages');

        if ($request->filled('id')) {
            $garagesQuery->where('id', $request->id);
        }

        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $garagesQuery->where(function ($query) use ($searchTerm) {
                $query->where('garage_name', 'like', $searchTerm);
            });
        }
        if ($request->filled('mobile')) {
            $garagesQuery->where('garage_mobile', 'like', '%' . $request->mobile . '%');
        }
        if ($request->filled('email')) {
            $garagesQuery->where('garage_email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('status')) {
            $garagesQuery->where('garage_status', 'like', '%' . $request->status . '%');
        }

        // Sorting
        $garagesQuery->orderBy('id', 'desc');

        // Paginate
        $garagesResults = $garagesQuery->paginate(10)->appends($request->except('page'));

        // Pass data to view
        $viewData['garages'] = $garagesResults;

        // Set title
        $viewData['pageTitle'] = 'Garage Details';

        // If POST, get form input for repopulating fields
        $formAutoFillup = $request->isMethod('post') ? $request->all() : $request->query();

        return view('AutoCare.garages.garage', $viewData, $formAutoFillup);
    }

    public function getGaragesData(Request $request)
    {
        $garagesQuery = DB::table('garages')
            ->select(
                'garages.id',
                'garages.garage_name',
                'garages.garage_email',
                'garages.garage_mobile',
                'garages.garage_status'
            );

        if ($request->filled('id')) {
            $garagesQuery->where('garages.id', $request->id);
        }

        if ($request->filled('name')) {
            $searchTerm = '%' . $request->name . '%';
            $garagesQuery->where(function ($query) use ($searchTerm) {
                $query->where('garages.garage_name', 'like', $searchTerm);
            });
        }

        if ($request->filled('mobile')) {
            $garagesQuery->where('garages.garage_mobile', 'like', '%' . $request->mobile . '%');
        }

        if ($request->filled('email')) {
            $garagesQuery->where('garages.garage_email', 'like', '%' . $request->email . '%');
        }

        if ($request->filled('status')) {
            $garagesQuery->where('garages.garage_status', $request->status);
        }

        $garagesQuery->orderBy('garages.id', 'desc');

        return DataTables::of($garagesQuery)
            ->addColumn('name', function ($garages) {
                return $garages->garage_name ?? '';
            })

            ->addColumn('status_badge', function ($garages) {
                $checked = $garages->garage_status == 1 ? 'checked' : '';
                return '
        <div class="form-check form-switch">
            <input type="checkbox" class="form-check-input toggle-status" 
                   data-id="' . $garages->id . '" ' . $checked . '>
            <label class="form-check-label">
                ' . ($garages->garage_status == 1 ? 'Active' : 'Inactive') . '
            </label>
        </div>
    ';
            })

            // Actions Column (Crucial Part)
            ->addColumn('actions', function ($garages) {

                $actions = '<div class="btn-group" role="group">
                                <button id="btnGroupDrop' . $garages->id . '" type="button"
                                    class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu btngroup-dropdown"
                                    aria-labelledby="btnGroupDrop' . $garages->id . '">';

                $actions .= '<li>
                                        <a href="' . url('/') . '/AutoCare/garages/details/' . $garages->id . '"
                                            class="dropdown-item btn btn-success btn-sm">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                    </li>';
                $actions .= '<li>
                    <a href="javascript:void(0);" 
                       class="dropdown-item text-danger delete-garage" 
                       data-id="' . $garages->id . '"
                       data-name="' . e($garages->garage_name) . '">
                        <i class="fa fa-trash"></i> Delete
                    </a>
                </li>';
                $actions .= '</ul></div>';
                return $actions;
            })

            ->filterColumn('status_badge', function ($query, $keyword) {
                $keyword = strtolower(trim($keyword));

                if ($keyword === 'inactive') {
                    $query->where('garages.garage_status', 0);
                } elseif ($keyword === 'active') {
                    $query->where('garages.garage_status', 1);
                } else {
                    $query->whereRaw("
            CASE 
              WHEN garages.garage_status = 1 THEN 'active'
              WHEN garages.garage_status = 0 THEN 'inactive'
              ELSE 'unknown'
            END LIKE ?", ["%{$keyword}%"]);
                }
            })
            ->setRowClass(function ($garages) {
                $isVoid = ($garages->is_void ?? false);
                return $isVoid ? 'table-danger' : '';
            })
            ->rawColumns([
                'status_badge',
                'actions'
            ])
            ->make(true);
    }

    public function updateStatus(Request $request, $id)
    {
        $garages = Garage::find($id);

        if (!$garages) {
            return response()->json(['success' => false, 'message' => 'Garage not found.'], 404);
        }

        $newStatus = $request->input('status');
        $garages->garage_status = $newStatus;
        $garages->save();

        if ($newStatus == 0) {
            $this->invalidateAllGarageSessions($garages);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully.',
            'status' => $newStatus
        ]);
    }

    protected function invalidateAllGarageSessions($garages)
    {
        $garages->forceFill([
            'remember_token' => Str::random(60),
        ])->save();

        $sessionIdPrefix = config('session.prefix') . ':';
        $sessionFiles = File::files(storage_path('framework/sessions'));

        foreach ($sessionFiles as $file) {
            $sessionData = file_get_contents($file);

            // Check if the session belongs to the garages
            if (strpos($sessionData, '"login_garage_' . $garages->id . '";i:' . $garages->id) !== false) {
                unlink($file);
            }
        }
    }

    public function create()
    {
        return view('AutoCare.garages.add');
    }

    public function store(Request $request)
    {
        try {
            $validated = $this->validateGarage($request);

            $orderTypes = $request->input('garage_order_types', ['fully_fitted']);
            $validated['garage_order_types'] = implode(',', $orderTypes);
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $basePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/garage_logo/';
            $destinationPath = public_path($basePath);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Logo
            if ($request->hasFile('garage_logo')) {
                $logo = $request->file('garage_logo');
                $extension = $logo->getClientOriginalExtension();
                $prefix = Str::slug($request->garage_name, '-');
                $logoName = $prefix . '-logo.' . $extension;
                $logo->move($destinationPath, $logoName);
                $validated['garage_logo'] = $logoName;
            }

            Garage::create($validated);

            return redirect()->route('AutoCare.garages.show')
                ->with('success', 'Garage created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Throwable $e) {
            Log::error('Garage Create Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $garages = Garage::findOrFail($id);
        $services = $garages->services;
        return view('AutoCare.garages.details', compact('garages', 'services'));
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $this->validateGarage($request);

            $garage = Garage::findOrFail($id);

            $orderTypes = $request->input('garage_order_types', ['fully_fitted']);
            $validated['garage_order_types'] = implode(',', $orderTypes);
            // Prepare path
            $domain = str_replace(['http://', 'https://'], '', request()->getHost());
            $basePath = 'frontend/' . str_replace('.', '-', $domain) . '/img/garage_logo/';
            $destinationPath = public_path($basePath);

            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // Logo
            if ($request->hasFile('garage_logo')) {
                $logo = $request->file('garage_logo');
                $extension = $logo->getClientOriginalExtension();

                $prefix = Str::slug($garage->garage_name, '-');
                $logoName = $prefix . '-logo.' . $extension;
                $logo->move($destinationPath, $logoName);
                $validated['garage_logo'] = $logoName;
            }
            // dd($validated);
            $garage->update($validated);

            return redirect()->route('AutoCare.garages.show')
                ->with('success', 'Garage updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Throwable $e) {
            Log::error('Garage Update Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $garage = Garage::findOrFail($id);
            $garage->delete();

            return response()->json([
                'success' => true,
                'message' => 'Garage deleted successfully.'
            ]);
        } catch (\Throwable $e) {
            \Log::error('Garage Delete Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'garage_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function validateGarage(Request $request): array
    {
        return $request->validate([
            'garage_name' => 'required|string|max:255',
            'garage_company_number' => 'nullable|string|max:50',
            'garage_vat_number' => 'nullable|string|max:50',
            'garage_eori_number' => 'nullable|string|max:20',
            'garage_phone' => 'nullable|string|max:150',
            'garage_mobile' => 'required|string|max:150',
            'garage_email' => 'required|email|max:255',
            'garage_street' => 'nullable|string|max:255',
            'garage_city' => 'nullable|string|max:100',
            'garage_zone' => 'nullable|string|max:100',
            'garage_country' => 'nullable|string|max:100',
            'garage_description' => 'nullable|string|max:500',
            'garage_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'garage_banner' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:4096',
            'garage_favicon' => 'nullable|image|mimes:png,ico|max:512',
            'garage_garage_opening_time' => 'nullable|string|max:500',
            'garage_social_facebook' => 'nullable|string|max:500',
            'garage_social_instagram' => 'nullable|string|max:500',
            'garage_social_twitter' => 'nullable|string|max:500',
            'garage_social_youtube' => 'nullable|string|max:500',
            'garage_google_map_link' => 'nullable|string|max:1000',
            'garage_longitude' => 'nullable|numeric',
            'garage_latitude' => 'nullable|numeric',
            'garage_google_reviews_link' => 'nullable|string|max:1000',
            'garage_google_reviews_stars' => 'nullable|numeric|between:0,5',
            'garage_google_reviews_count' => 'nullable|integer|min:0',
            'garage_website_url' => 'nullable|string|max:255',
            'garage_notes' => 'nullable|string|max:1000',
            'garage_status' => 'nullable|boolean',
            'garage_order_types' => 'required|array|min:1',
            'garage_order_types.*' => 'in:fully_fitted,mobile_fitted,mailorder,delivery,collection',
            'garage_bank_name' => 'nullable|string|max:100',
            'garage_bank_sort_code' => 'nullable|string|max:50',
            'garage_account_number' => 'nullable|string|max:100',
            'garage_revolut_source_id' => 'nullable|string|max:100',
            'garage_revoult_counterparty_id' => 'nullable|string|max:50',
            'commission_type' => 'required|in:Fixed,Percentage',
            'commission_price' => 'required|numeric|min:0',
            'card_processing_fee' => 'required|numeric|min:0',

        ]);
    }

    public function updatePassword(Request $request, $id)
    {
        // Optional: Add admin verification
        if (!Auth::check()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $garage = Garage::findOrFail($id);

        // Update password directly
        $garage->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    public function orders(Request $request, $id)
    {
        $garages = Garage::findOrFail($id);
        // dd('test');
        $workshops = Workshop::where('garage_id', $garages->id)->where('is_void', false)
            ->with('items')->get();

        return view('AutoCare.garages.orders', compact('workshops', 'garages'));
    }

    public function invoices(Request $request, $id)
    {
        $garages = Garage::findOrFail($id);
        $invoices = Invoice::where('garage_id', $garages->id)->where('is_void', false)
            ->with('items')->get();

        $viewData['header_link'] = HeaderLink::where("menu_id", '3')
            ->select("link_title", "link_name")
            ->orderBy('id', 'desc')
            ->get();

        // Calculate invoice counts for each status
        $unpaidCount = $garages->invoices()->where('payment_status', 0)->where('is_void', false)->count();
        $paidCount = $garages->invoices()->where('payment_status', 1)->where('is_void', false)->count();
        $overdueCount = $garages->invoices()->where('payment_status', 2)->where('is_void', false)->count();
        $partiallyPaidCount = $garages->invoices()->where('payment_status', 3)->where('is_void', false)->count();

        return view('AutoCare.garages.invoices', compact('garages', 'invoices', 'unpaidCount', 'paidCount', 'overdueCount', 'partiallyPaidCount'));

    }
    public function statements(Request $request, $id)
    {
        $garages = Garage::findOrFail($id);

        // Fetch invoices within the selected date range
        $query = Invoice::where('garage_id', $id)->where('is_void', false);

        if ($request->filled('from') && $request->filled('to')) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $request->input('from'))->startOfDay();
                $to = Carbon::createFromFormat('d-m-Y', $request->input('to'))->endOfDay();
            } catch (\Exception $e) {
                // Log::error("Invalid dat/e format: " . $e->getMessage());
                return response()->json(['error' => 'Invalid date format. Please use DD-MM-YYYY.'], 400);
            }
        } else {
            $from = Carbon::now()->startOfMonth();
            $to = Carbon::now()->endOfMonth();
        }

        $query->whereBetween('created_at', [$from, $to]);
        $invoices = $query->get();
        // Log::info("Filtered invoices count: " . $invoices->count());

        // Initialize transactions as an empty collection
        $transactions = collect();

        // Populate transactions only if invoices exist
        if (!$invoices->isEmpty()) {
            foreach ($invoices as $invoice) {
                $transactions->push([
                    'date' => $invoice->created_at->format('d-m-Y'),
                    'details' => 'Invoice #' . $invoice->workshop_id,
                    'type' => 'Invoice',
                    'amount' => $invoice->grandTotal,
                    'paid_price' => $invoice->paid_price,
                    'discountPrice' => $invoice->discount_price,
                    'balance_price' => $invoice->balance_price
                ]);
            }
        } else {
            // Log::warning("No invoices found for garage_id: $id in the given date range.");
        }

        $totalInvoiced = $invoices->sum('grandTotal');
        $discountPrice = $invoices->sum('discount_price');
        $totalPaid = $invoices->sum('paid_price') + $discountPrice;
        $balanceDue = $totalInvoiced - $totalPaid;

        if ($request->ajax()) {
            return response()->json([
                'totalInvoiced' => $totalInvoiced,
                'totalPaid' => $totalPaid,
                'balanceDue' => $balanceDue,
                'discountPrice' => $discountPrice,
                'transactionsHtml' => $transactions
            ]);
        }

        return view('AutoCare.garages.statements', compact(
            'garages',
            'totalInvoiced',
            'totalPaid',
            'balanceDue',
            'discountPrice',
            'transactions'
        ));
    }
    public function sendStatementEmail(Request $request)
    {
        // Validate request
        $request->validate([
            'garage_id' => 'required|exists:garages,id',
            'email_to' => 'required|email',
            'email_cc' => 'nullable|email',
            'attach_pdf' => 'boolean',
            'email_body' => 'nullable|string',
        ]);

        // Get customer
        $garages = Garage::findOrFail($request->garage_id);

        // Generate PDF
        $this->generateStatementPdf($garages->id);

        // Build PDF path
        $garageName = getGarage()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');
        $pdfPath = "statements/{$safeGarageName}/STAT-{$garages->id}.pdf";
        $pdfFullPath = storage_path("app/public/{$pdfPath}");

        // Prepare data for email
        $data = [
            'garages' => $garages,
            'totalInvoiced' => $request->totalInvoiced ?? 0,
            'totalPaid' => $request->totalPaid ?? 0,
            'balanceDue' => $request->balanceDue ?? 0,
            'discountPrice' => $request->discountPrice ?? 0,
            'body' => $request->email_body
        ];

        // Send email with optional attachment
        Mail::to($request->email_to)
            ->cc($request->email_cc)
            ->send(new StatementEmail($data, $request->attach_pdf ? $pdfFullPath : null));

        return redirect()->back()->with('success', 'Statement sent successfully!');
    }
    public function previewStatementPdf($id)
    {
        $this->generateStatementPdf($id); // Always regenerate latest version

        $garageName = getGarage()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');
        $pdfPath = "statements/{$safeGarageName}/STAT-{$id}.pdf";

        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Statement PDF not found.');
        }

        return response()->file(storage_path("app/public/{$pdfPath}"), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="statement.pdf"'
        ]);
    }
    public function downloadStatementPdf($id)
    {
        $this->generateStatementPdf($id); // Regenerate fresh statement

        $garageName = getGarage()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');
        $pdfPath = "statements/{$safeGarageName}/STAT-{$id}.pdf";

        if (!Storage::disk('public')->exists($pdfPath)) {
            abort(404, 'Statement PDF not found.');
        }

        return response()->download(storage_path("app/public/{$pdfPath}"), "Statement-{$id}.pdf", [
            'Content-Type' => 'application/pdf',
        ]);
    }
    public function generateStatementPdf($id)
    {
        // Fetch customer and statement data
        $garages = Garage::findOrFail($id);
        $query = Invoice::where('garage_id', $id)->where('is_void', false)->orderBy('created_at', 'asc');

        if (request()->filled('from') && request()->filled('to')) {
            try {
                $from = \Carbon\Carbon::createFromFormat('d-m-Y', request('from'))->startOfDay();
                $to = \Carbon\Carbon::createFromFormat('d-m-Y', request('to'))->endOfDay();
                $query->whereBetween('created_at', [$from, $to]);
            } catch (\Exception $e) {
                abort(400, 'Invalid date format. Use DD-MM-YYYY.');
            }
        }

        $invoices = $query->get();
        // Build transactions list
        $transactions = collect();
        foreach ($invoices as $invoice) {
            $transactions->push([
                'date' => $invoice->created_at->format('d-m-Y'),
                'details' => 'Invoice #' . $invoice->workshop_id,
                'type' => 'Invoice',
                'amount' => $invoice->grandTotal,
                'paid_price' => $invoice->paid_price,
                'discountPrice' => $invoice->discount_price,
                'balance_price' => $invoice->balance_price
            ]);
        }

        $totalInvoiced = $invoices->sum('grandTotal');
        $discountPrice = $invoices->sum('discount_price');
        $totalPaid = $invoices->sum('paid_price') + $discountPrice;
        $balanceDue = $totalInvoiced - $totalPaid;

        // Format discount for each invoice
        foreach ($invoices as &$invoice) {
            if ($invoice->discount_type === 'percentage' && $invoice->discount_value > 0) {
                $invoice->formatted_discount = '(' . $invoice->discount_value . '%)';
            } else {
                $invoice->formatted_discount = '';
            }
        }

        // Generate PDF content
        $pdf = PDF::loadView('AutoCare.garages.statement-pdf', compact(
            'garages',
            'invoices',
            'transactions',
            'totalInvoiced',
            'totalPaid',
            'discountPrice',
            'balanceDue'
        ));

        // Define folder name based on garage
        $garageName = getGarage()->garage_name;
        $safeGarageName = Str::slug($garageName, '_');

        // Define path
        $pdfPath = "statements/{$safeGarageName}/STAT-{$garages->id}.pdf";

        // Save PDF
        Storage::disk('public')->put($pdfPath, $pdf->output());

        return storage_path("app/public/{$pdfPath}");
    }

}
