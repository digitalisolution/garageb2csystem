<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use DB;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Modal;
use App\Models\Brand;
use App\Models\HeaderLink;
use App\Models\CustomerDebitLog;
use Auth;
use App\PurchaseInvoice;

class CustomerCreditDebitLog extends Controller
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
            $getFormAutoFillup = CreaditDebitDetail::whereId($id)->first();
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
            $viewData['pageTitle'] = 'Customer Credit Debit Log';
            $customerCreditDebitLog = DB::table('customer_debit_logs');
            $customerCreditDebitLog->leftJoin('customers', 'customers.id', '=', 'customer_debit_logs.customer_id');
            $customerCreditDebitLog->leftJoin('workshops', 'workshops.id', '=', 'customer_debit_logs.workshop_id');
            // $customerCreditDebitLog->leftJoin('purchases','purchases.id','=','customer_debit_logs.purchase_id');  
            // $customerCreditDebitLog->leftJoin('products','products.id','=','purchases.product_id');
            // $customerCreditDebitLog->leftJoin('suppliers','suppliers.id','=','purchases.customer_name');
            // $customerCreditDebitLog->leftJoin('modals','modals.id','=','purchases.model_number');
            // $customerCreditDebitLog->leftJoin('brands','brands.id','=','purchases.company_name');      
            $customerCreditDebitLog->where('customer_debit_logs.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $getFormAutoFillup['id'] = $request->id;
                $customerCreditDebitLog->where('customer_debit_logs.id', '=', $request->id);
            }
            if ($request->has('user_name') && $request->user_name != '') {
                $getFormAutoFillup['user_name'] = $request->user_name;
                $customerCreditDebitLog->where('customer_debit_logs.customer_id', 'like', '%' . $request->user_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $getFormAutoFillup['created_at_from'] = $request->created_at_from;
                $customerCreditDebitLog->where('customer_debit_logs.created_at', '>=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $getFormAutoFillup['created_at_to'] = $request->created_at_to;
                $customerCreditDebitLog->where('customer_debit_logs.created_at', '<', $request->created_at_to);
            }


            $customerCreditDebitLog->select('customer_debit_logs.id as creditDetailId', 'customer_debit_logs.*', 'customers.*');
            $customerCreditDebitLog->orderBy('customer_debit_logs.created_at', 'ASC');
            $customerCreditDebitLog = $customerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($customerCreditDebitLog), true);
            return view('AutoCare.customer-credit-debit-log.search', $viewData)->with($getFormAutoFillup);

        } else {
            $viewData['pageTitle'] = 'Credit Debit Log';
            $customerCreditDebitLog = DB::table('customer_debit_logs');
            $customerCreditDebitLog->leftJoin('customers', 'customers.id', '=', 'customer_debit_logs.customer_id');
            $customerCreditDebitLog->leftJoin('workshops', 'workshops.id', '=', 'customer_debit_logs.workshop_id');
            // $customerCreditDebitLog->leftJoin('suppliers','suppliers.id','=','customer_debit_logs.supplier_id');
            // $customerCreditDebitLog->leftJoin('purchases','purchases.id','=','customer_debit_logs.purchase_id');  
            // $customerCreditDebitLog->leftJoin('products','products.id','=','purchases.product_id');
            // // $customerCreditDebitLog->leftJoin('suppliers','suppliers.id','=','purchases.customer_name');
            // $customerCreditDebitLog->leftJoin('modals','modals.id','=','purchases.model_number');
            // $customerCreditDebitLog->leftJoin('brands','brands.id','=','purchases.company_name');                    
            $customerCreditDebitLog->where('customer_debit_logs.deleted_at', '=', null);

            // $purchase->select('purchases.*','products.product_name as product_name','suppliers.customer_name as customer_name_from_supplier','brands.brand_name as company_name_from_brand','modals.model_name as model_number');

            $customerCreditDebitLog->select('customer_debit_logs.id as creditDetailId', 'customer_debit_logs.*', 'customers.*');
            $customerCreditDebitLog->orderBy('customer_debit_logs.created_at', 'ASC');
            $customerCreditDebitLog = $customerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($customerCreditDebitLog), true);
            return view('AutoCare.customer-credit-debit-log.search', $viewData)->with($getFormAutoFillup);
            ;
        }

    }
    public function trash(Request $request, $id)
    {
        if (($id != null) && (CreditDebitDetail::where('id', $id)->delete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'Purchase was Trashed!');
            $viewData['pageTitle'] = 'Purchase';
            $customerCreditDebitLog = DB::table('credit_debit_details');
            $customerCreditDebitLog->leftJoin('users', 'users.id', '=', 'credit_debit_details.user_id');
            $customerCreditDebitLog->leftJoin('user_details', 'user_details.users_id', '=', 'credit_debit_details.user_id');
            $customerCreditDebitLog->where('credit_debit_details.deleted_at', '=', null);
            $customerCreditDebitLog->select('credit_debit_details.*', 'users.name as user_name', 'users.email as user_email');
            $customerCreditDebitLog->orderBy('created_at', 'ASC');
            $customerCreditDebitLog = $customerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($customerCreditDebitLog), true);
            return view('AutoCare.credit-debit-log.search', $viewData);
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'Purchase';
            $customerCreditDebitLog = DB::table('credit_debit_details');
            $customerCreditDebitLog->leftJoin('users', 'users.id', '=', 'credit_debit_details.user_id');
            $customerCreditDebitLog->leftJoin('user_details', 'user_details.users_id', '=', 'credit_debit_details.user_id');
            $customerCreditDebitLog->where('credit_debit_details.deleted_at', '=', null);
            $customerCreditDebitLog->select('credit_debit_details.*', 'users.name as user_name', 'users.email as user_email');
            $customerCreditDebitLog->orderBy('created_at', 'ASC');
            $customerCreditDebitLog = $customerCreditDebitLog->get();
            $viewData['market'] = json_decode(json_encode($customerCreditDebitLog), true);
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
