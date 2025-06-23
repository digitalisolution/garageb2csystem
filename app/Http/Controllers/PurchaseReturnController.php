<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseReturn;
use App\Models\HeaderLink;
use DB;

class PurchaseReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id = null)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $getFormAutoFillup = array();
        if ($request->isMethod('post')) {
            // DB::enableQueryLog();
            $viewData['pageTitle'] = 'Purchase Return';
            $purchase = DB::table('purchase_returns');
            $purchase->leftJoin('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id');
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->where('purchases.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $getFormAutoFillup['id'] = $request->id;
                $purchase->where('purchase_returns.id', '=', $request->id);
            }
            if ($request->has('supplier_name') && $request->supplier_name != '') {
                $getFormAutoFillup['supplier_name'] = $request->supplier_name;
                $purchase->where('suppliers.supplier_name', 'like', '%' . $request->supplier_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $getFormAutoFillup['created_at_from'] = $request->created_at_from;
                $purchase->whereDate('purchase_returns.created_at', '<=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $getFormAutoFillup['created_at_to'] = $request->created_at_to;
                $purchase->whereDate('purchase_returns.created_at', '>=', $request->created_at_to);
            }
            if ($request->has('product_name') && $request->product_name != '') {
                $getFormAutoFillup['product_name'] = $request->product_name;
                $purchase->where('products.product_name', 'like', '%' . $request->product_name . '%');
            }
            if ($request->has('company_name') && $request->company_name != '') {
                $getFormAutoFillup['company_name'] = $request->company_name;
                $purchase->where('brands.brand_name', 'like', '%' . $request->company_name . '%');
            }

            $purchase->select('purchase_returns.id as returnedId', 'purchase_returns.created_at as returned_date', 'purchase_returns.quantity as ReturnedQuantity', 'purchase_returns.comments as ReturnComments', 'purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'desc');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            // $laQuery = DB::getQueryLog();
            // DB::disableQueryLog();
            // print_r($laQuery);
            //  print_r($purchase);
            // exit;
            return view('AutoCare.purchase.purchase_return_log', $viewData)->with($getFormAutoFillup);
            ;

        } else {
            $viewData['pageTitle'] = 'View Purchase';
            $purchase = DB::table('purchase_returns');
            $purchase->leftJoin('purchases', 'purchases.id', '=', 'purchase_returns.purchase_id');
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->where('purchases.deleted_at', '=', null);
            $purchase->select('purchase_returns.created_at as returned_date', 'purchase_returns.id as returnedId', 'purchase_returns.quantity as ReturnedQuantity', 'purchase_returns.comments as ReturnComments', 'purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'desc');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            //Ashu@97047$&(!   ca7zaoly6g7y
            return view('AutoCare.purchase.purchase_return_log', $viewData);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
