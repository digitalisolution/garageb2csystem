<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\CreditDebitDetail;
use DB;

class CreaditDebitDetailController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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
            return view('AutoCare.purchase.add', $viewData)->with($getFormAutoFillup);
        } else {
            if ($request->isMethod('post')) {
                $supplier_name = $request->input('supplier_name');
                $bill_num = $request->input('bill_num');
                $bill_date = $request->input('bill_date');
                $product_id = $request->input('product_id');
                $company_name = $request->input('company_name');
                $model_number = $request->input('model_number');
                $part_number = $request->input('part_number');
                $discription = $request->input('discription');
                $hsn = $request->input('hsn');
                $unit_price = $request->input('unit_price');
                $quantity = $request->input('quantity');
                $gst = $request->input('gst');
                $discount = $request->input('discount');
                $total_amount = $request->input('total_amount');
                $unit_price_exit = $request->input('unit_price_exit');

                for ($i = 0; $i < count($product_id); $i++) {

                    $saveCreditdebit = new CreditDebitDetail;
                    $saveCreditdebit->supplier_name = $supplier_name;
                    $saveCreditdebit->bill_num = $bill_num;
                    $saveCreditdebit->bill_date = $bill_date;
                    $saveCreditdebit->product_id = $product_id[$i];
                    $saveCreditdebit->company_name = $company_name[$i];
                    $saveCreditdebit->model_number = $model_number[$i];
                    $saveCreditdebit->part_number = $part_number[$i];
                    $saveCreditdebit->discription = $discription[$i];
                    $saveCreditdebit->hsn = $hsn[$i];
                    $saveCreditdebit->unit_price = $unit_price[$i];
                    $saveCreditdebit->unit_price_exit = $unit_price_exit[$i];
                    $saveCreditdebit->quantity = $quantity[$i];
                    $saveCreditdebit->gst = $gst[$i];
                    $saveCreditdebit->discount = $discount[$i];
                    $saveCreditdebit->total_amount = $total_amount[$i];

                    if (!$saveCreditdebit->save()) {
                        $request->session()->flash('message.level', 'Error');
                        $request->session()->flash('message.content', 'Somthing Went Wrong!');
                    }
                }
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', ' Saved Successfully!');
            }
            return view('credit-debit-log.add', $viewData);
        }
    }
    public function update(Request $request, $id = null)
    {
        $getFormAutoFillup = array();
        $viewData['pageTitle'] = 'Credit Debit Log  Detail';

        if ($request->isMethod('post')) {
            $supplier_name = $request->input('supplier_name');
            $bill_num = $request->input('bill_num');
            $bill_date = $request->input('bill_date');
            $product_id = $request->input('product_id');
            $company_name = $request->input('company_name');
            $model_number = $request->input('model_number');
            $part_number = $request->input('part_number');
            $discription = $request->input('discription');
            $hsn = $request->input('hsn');
            $unit_price = $request->input('unit_price');
            $quantity = $request->input('quantity');
            $gst = $request->input('gst');
            $discount = $request->input('discount');
            $total_amount = $request->input('total_amount');
            $unit_price_exit = $request->input('unit_price_exit');

            for ($i = 0; $i < count($product_id); $i++) {

                $updateCreditDebitDetail = CreditDebitDetail::where('id', '=', $request->id);
                ;
                // $saveCreditdebit->id = $requst->id;
                $saveCreditDebit['supplier_name'] = $supplier_name;
                $saveCreditDebit['bill_num'] = $bill_num;
                $saveCreditDebit['bill_date'] = $bill_date;
                $saveCreditDebit['product_id'] = $product_id[$i];
                $saveCreditDebit['company_name'] = $company_name[$i];
                $saveCreditDebit['model_number'] = $model_number[$i];
                $saveCreditDebit['discription'] = $discription[$i];
                $saveCreditDebit['part_number'] = $part_number[$i];
                $saveCreditDebit['hsn'] = $hsn[$i];
                $saveCreditDebit['unit_price'] = $unit_price[$i];
                $saveCreditDebit['unit_price_exit'] = $unit_price_exit[$i];
                // $saveCreditDebit['quantity'] = $quantity[$i];
                $saveCreditDebit['gst'] = $gst[$i];
                $saveCreditDebit['discount'] = $discount[$i];
                $saveCreditDebit['total_amount'] = $total_amount[$i];



                if (!$updateCreditDebitDetail->update($saveCreditDebit)) {
                    $request->session()->flash('message.level', 'Error');
                    $request->session()->flash('message.content', 'Somthing Went Wrong!');
                }
            }
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', ' Updated Successfully!');
        }
        return redirect('/credit-debit/add' . $request->id);
    }
    // this is for search
    public function view(Request $request)
    {
        $getFormAutoFillup = array();
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Credit Debit Log';
            $market = DB::table('credit_debit_details');
            $market->leftJoin('users', 'users.id', '=', 'markets.user_id');
            $market->leftJoin('user_details', 'user_details.users_id', '=', 'markets.user_id');
            $market->where('markets.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $getFormAutoFillup['id'] = $request->id;
                $market->where('markets.id', '=', $request->id);
            }
            if ($request->has('user_name') && $request->user_name != '') {
                $getFormAutoFillup['user_name'] = $request->user_name;
                $market->where('users.name', 'like', '%' . $request->user_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $getFormAutoFillup['created_at_from'] = $request->created_at_from;
                $market->where('credit_debit_details.created_at', '>=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $getFormAutoFillup['created_at_to'] = $request->created_at_to;
                $market->where('credit_debit_details.created_at', '<=', $request->created_at_to);
            }
            if ($request->has('user_id') && $request->user_id != '') {
                $getFormAutoFillup['user_id'] = $request->user_id;
                $market->where('users.id', 'like', '%' . $request->product_name . '%');
            }

            $market->select('markets.*', 'users.name as user_name', 'users.email as user_email');
            $market->orderBy('id', 'desc');
            $market = $market->get();
            $viewData['market'] = json_decode(json_encode($market), true);
            return view('AutoCare.credit-debit-log.search', $viewData)->with($getFormAutoFillup);
            ;

        } else {
            $viewData['pageTitle'] = 'Credit Debit Log';
            $market = DB::table('credit_debit_details');
            $market->leftJoin('users', 'users.id', '=', 'markets.user_id');
            $market->leftJoin('user_details', 'user_details.users_id', '=', 'markets.user_id');
            $market->where('markets.deleted_at', '=', null);
            $market->select('markets.*', 'users.name as user_name', 'users.email as user_email');
            $market->orderBy('id', 'desc');
            $market = $market->get();
            $viewData['market'] = json_decode(json_encode($market), true);
            // $viewData['purchase'] = $market;
            //	$market= DB::table('purchases');
            //$market->orderBy('id','desc');
            //$market= $market->get();
            //$viewData['purchase']=json_decode(json_encode($market), true);
            //Ashu@97047$&(!   ca7zaoly6g7y
            return view('AutoCare.credit-debit-log.search', $viewData);
        }

    }
    public function trash(Request $request, $id)
    {
        if (($id != null) && (CreditDebitDetail::where('id', $id)->delete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'Purchase was Trashed!');
            $viewData['pageTitle'] = 'Purchase';
            $market = DB::table('credit_debit_details');
            $market->leftJoin('users', 'users.id', '=', 'markets.user_id');
            $market->leftJoin('user_details', 'user_details.users_id', '=', 'markets.user_id');
            $market->where('markets.deleted_at', '=', null);
            $market->select('markets.*', 'users.name as user_name', 'users.email as user_email');
            $market->orderBy('id', 'desc');
            $market = $market->get();
            $viewData['market'] = json_decode(json_encode($market), true);
            return view('AutoCare.credit-debit-log.search', $viewData);
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'Purchase';
            $market = DB::table('credit_debit_details');
            $market->leftJoin('users', 'users.id', '=', 'markets.user_id');
            $market->leftJoin('user_details', 'user_details.users_id', '=', 'markets.user_id');
            $market->where('markets.deleted_at', '=', null);
            $market->select('markets.*', 'users.name as user_name', 'users.email as user_email');
            $market->orderBy('id', 'desc');
            $market = $market->get();
            $viewData['market'] = json_decode(json_encode($market), true);
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
