<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Purchase;
use DB;
use App\Models\Supplier;
use App\Models\Product;
use App\Modal;
use App\Models\Brand;
use App\Models\HeaderLink;
use App\SupplierDebitLog;
use Auth;
use App\PurchaseInvoice;

class SupplierCreditDebitLog extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
    }
    public function save(Request $request, $id = null)
    {
        $getFormAutoFillup = array();

        $viewData['pageTitle'] = 'Credit Debit Detail';
        if (isset($id) && $id != null) {
            $getFormAutoFillup = CreditDebitDetail::whereId($id)->first();
            if ($getFormAutoFillup) {
                $getFormAutoFillup = $getFormAutoFillup->toArray();
            } else {
                $request->session()->flash('message.level', 'Error');
                $request->session()->flash('message.content', 'Somthing Went Wrong!');
            }
            return view('AutoCare.credit-debit-log.add', $viewData)->with($getFormAutoFillup);
        } else {
            if ($request->isMethod('post')) {
                $item = $request->input('item');
                $item_discription = $request->input('item_discription');
                $amount = $request->input('amount');
                $is_credit = $request->input('is_credit');
                $created_at = $request->input('created_at');
                $user_id = Auth::user()->id;


                for ($i = 0; $i < count($item); $i++) {
                    $saveCreditdebit = new CreditDebitDetail;
                    $saveCreditdebit->item = $item[$i];
                    $saveCreditdebit->item_discription = $item_discription[$i];
                    $saveCreditdebit->amount = $amount[$i];
                    $saveCreditdebit->is_credit = $is_credit;
                    $saveCreditdebit->user_id = $user_id;
                    $saveCreditdebit->created_at = $created_at;

                    if (!$saveCreditdebit->save()) {
                        $request->session()->flash('message.level', 'Error');
                        $request->session()->flash('message.content', 'Somthing Went Wrong!');
                    }
                }
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', ' Saved Successfully!');
            }
            return view('AutoCare.credit-debit-log.add', $viewData);
        }
    }
    public function update(Request $request, $id = null)
    {
        $getFormAutoFillup = array();
        $viewData['pageTitle'] = 'Credit Debit Log  Detail';
        if ($request->isMethod('post')) {
            $item = $request->input('item');
            $item_discription = $request->input('item_discription');
            $amount = $request->input('amount');
            $is_credit = $request->input('is_credit');
            $user_id = Auth::user()->id;

            for ($i = 0; $i < count($item); $i++) {

                $updateCreditDebitDetail = CreditDebitDetail::where('id', '=', $request->id);
                ;
                $saveCreditdebit['item'] = $item[$i];
                $saveCreditdebit['item_discription'] = $item_discription[$i];
                $saveCreditdebit['amount'] = $amount[$i];
                $saveCreditdebit['is_credit'] = $is_credit;
                // $saveCreditdebit['user_id'] = $user_id; 
                $saveCreditdebit['created_at'] = $request->input('created_at');

                if (!$updateCreditDebitDetail->update($saveCreditdebit)) {
                    $request->session()->flash('message.level', 'Error');
                    $request->session()->flash('message.content', 'Somthing Went Wrong!');
                }
            }
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', ' Updated Successfully!');
        }
        return redirect('/credit-debit/add/' . $request->id);
    }
    // this is for search
    public function view(Request $request)
    {
        $getFormAutoFillup = array();
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Purchase Credit Debit Log';
            $supplerCreditDebitLog = DB::table('supplier_debit_logs');
            $supplerCreditDebitLog->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_debit_logs.supplier_id');
            $supplerCreditDebitLog->leftJoin('purchases', 'purchases.id', '=', 'supplier_debit_logs.purchase_id');
            $supplerCreditDebitLog->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            // $supplerCreditDebitLog->leftJoin('suppliers','suppliers.id','=','purchases.supplier_name');
            $supplerCreditDebitLog->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $supplerCreditDebitLog->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $supplerCreditDebitLog->where('supplier_debit_logs.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $getFormAutoFillup['id'] = $request->id;
                $supplerCreditDebitLog->where('supplier_debit_logs.id', '=', $request->id);
            }
            if ($request->has('user_name') && $request->user_name != '') {
                $getFormAutoFillup['user_name'] = $request->user_name;
                $supplerCreditDebitLog->where('suppliers.supplier_name', 'like', '%' . $request->user_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $getFormAutoFillup['created_at_from'] = $request->created_at_from;
                $supplerCreditDebitLog->where('supplier_debit_logs.created_at', '>=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $getFormAutoFillup['created_at_to'] = $request->created_at_to;
                $supplerCreditDebitLog->where('supplier_debit_logs.created_at', '<', $request->created_at_to);
            }


            $supplerCreditDebitLog->select('supplier_debit_logs.*', 'suppliers.supplier_name as SupplierName', 'suppliers.email as supplierEmail', 'suppliers.mob_num as phoneNumber', 'purchases.bill_date as bill_date', 'brands.brand_name as company_name_from_brand', 'modals.model_name as modelNumber', 'products.product_name as productName', 'purchases.quantity as purchaseQuantity');
            $supplerCreditDebitLog->orderBy('created_at', 'ASC');
            $supplerCreditDebitLog = $supplerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($supplerCreditDebitLog), true);
            return view('AutoCare.purchase-credit-debit-log.search', $viewData)->with($getFormAutoFillup);

        } else {
            $viewData['pageTitle'] = 'Credit Debit Log';
            $supplerCreditDebitLog = DB::table('supplier_debit_logs');
            $supplerCreditDebitLog->leftJoin('suppliers', 'suppliers.id', '=', 'supplier_debit_logs.supplier_id');
            $supplerCreditDebitLog->leftJoin('purchases', 'purchases.id', '=', 'supplier_debit_logs.purchase_id');
            $supplerCreditDebitLog->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            // $supplerCreditDebitLog->leftJoin('suppliers','suppliers.id','=','purchases.supplier_name');
            $supplerCreditDebitLog->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $supplerCreditDebitLog->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $supplerCreditDebitLog->where('supplier_debit_logs.deleted_at', '=', null);

            // $purchase->select('purchases.*','products.product_name as product_name','suppliers.supplier_name as supplier_name_from_supplier','brands.brand_name as company_name_from_brand','modals.model_name as model_number');

            $supplerCreditDebitLog->select('supplier_debit_logs.*', 'suppliers.supplier_name as SupplierName', 'suppliers.email as supplierEmail', 'suppliers.mob_num as phoneNumber', 'purchases.bill_date as bill_date', 'brands.brand_name as company_name_from_brand', 'modals.model_name as modelNumber', 'products.product_name as productName', 'purchases.quantity as purchaseQuantity');
            $supplerCreditDebitLog->orderBy('supplier_debit_logs.created_at', 'ASC');
            $supplerCreditDebitLog = $supplerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($supplerCreditDebitLog), true);
            return view('AutoCare.purchase-credit-debit-log.search', $viewData)->with($getFormAutoFillup);
            ;
        }

    }
    public function trash(Request $request, $id)
    {
        if (($id != null) && (CreditDebitDetail::where('id', $id)->delete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'Purchase was Trashed!');
            $viewData['pageTitle'] = 'Purchase';
            $supplerCreditDebitLog = DB::table('credit_debit_details');
            $supplerCreditDebitLog->leftJoin('users', 'users.id', '=', 'credit_debit_details.user_id');
            $supplerCreditDebitLog->leftJoin('user_details', 'user_details.users_id', '=', 'credit_debit_details.user_id');
            $supplerCreditDebitLog->where('credit_debit_details.deleted_at', '=', null);
            $supplerCreditDebitLog->select('credit_debit_details.*', 'users.name as user_name', 'users.email as user_email');
            $supplerCreditDebitLog->orderBy('created_at', 'ASC');
            $supplerCreditDebitLog = $supplerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($supplerCreditDebitLog), true);
            return view('AutoCare.credit-debit-log.search', $viewData);
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'Purchase';
            $supplerCreditDebitLog = DB::table('credit_debit_details');
            $supplerCreditDebitLog->leftJoin('users', 'users.id', '=', 'credit_debit_details.user_id');
            $supplerCreditDebitLog->leftJoin('user_details', 'user_details.users_id', '=', 'credit_debit_details.user_id');
            $supplerCreditDebitLog->where('credit_debit_details.deleted_at', '=', null);
            $supplerCreditDebitLog->select('credit_debit_details.*', 'users.name as user_name', 'users.email as user_email');
            $supplerCreditDebitLog->orderBy('created_at', 'ASC');
            $supplerCreditDebitLog = $supplerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($supplerCreditDebitLog), true);
            return view('AutoCare.credit-debit-log.search', $viewData);
        }
    }
    public function trashedList()
    {

        $trashed = CreditDebitDetail::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.credit-debit-log.delete', compact('trashed', 'trashed'));

    }
    public function permanemetDelete(Request $request, $id)
    {
        if (($id != null) && (CreditDebitDetail::where('id', $id)->forceDelete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', "Purchase was deleted Permanently and Can't rollback in Future!");
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
        }

        $trashed = CreditDebitDetail::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(50);
        return view('AutoCare.credit-debit-log.delete', compact('trashed', 'trashed'));
    }
}
