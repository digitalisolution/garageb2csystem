<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use DB;
use App\Supplier;
use App\Models\Product;
use App\Modal;
use App\Models\Brand;
use App\Models\HeaderLink;
use App\Models\SupplierDebitLog;
use Auth;
use App\PurchaseInvoice;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
    }
    public function save(Request $request, $id = null)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $getFormAutoFillup = array();

        $viewData['pageTitle'] = 'Add Purchase Detail';
        $viewData['option1'] = 'Add Supplier';
        $viewData['optionValue1'] = "AutoCare/supplier/add";
        $viewData['option2'] = 'Add Product Detail';
        $viewData['optionValue2'] = "AutoCare/product/add";


        $viewData['supplier'] = Supplier::orderBy('supplier_name', 'ASC')->pluck('supplier_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');

        $viewData['product'] = Product::pluck('product_name', 'id');
        if (isset($id) && $id != null) {
            $getFormAutoFillup = Purchase::whereId($id)->first();
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

                $savePurchaseInvoice = new PurchaseInvoice;
                $savePurchaseInvoice->supplier_id = $supplier_name;
                $savePurchaseInvoice->purchase_invoice_number = $bill_num;
                $savePurchaseInvoice->purchase_invoice_date = $bill_date;
                $savePurchaseInvoice->purchase_invoice_amount = $request->purchase_invoice_amount;
                $savePurchaseInvoice->purchase_discription = $request->purchase_discription;
                $savePurchaseInvoice->payment_type = $request->payment_type;
                $savePurchaseInvoice->user_id = Auth::user()->id;
                // $savePurchaseInvoice->total_purchase_amount =  $request->total_purchase_amount;
                // $savePurchaseInvoice->purchase_due_amount =  $request->purchase_due_amount;

                if ($savePurchaseInvoice->save()) {
                    $purchaseInvoiceId = $savePurchaseInvoice->id;
                }

                for ($i = 0; $i < count($product_id); $i++) {

                    $savePurchase = new Purchase;
                    $savePurchase->purchase_invoice_id = $purchaseInvoiceId;
                    $savePurchase->supplier_name = $supplier_name;
                    $savePurchase->bill_num = $bill_num;
                    $savePurchase->bill_date = $bill_date;
                    $savePurchase->product_id = $product_id[$i];
                    $savePurchase->company_name = $company_name[$i];
                    $savePurchase->model_number = $model_number[$i];
                    $savePurchase->part_number = $part_number[$i];
                    $savePurchase->discription = $discription[$i];
                    $savePurchase->hsn = $hsn[$i];
                    $savePurchase->unit_price = $unit_price[$i];
                    $savePurchase->unit_price_exit = $unit_price_exit[$i];
                    $savePurchase->quantity = $quantity[$i];
                    $savePurchase->purchase_discription = $request->purchase_discription;
                    $savePurchase->gst = $gst[$i];
                    $savePurchase->discount = isset($discount[$i]) ? $discount[$i] : 0;
                    $savePurchase->total_amount = $total_amount[$i];
                    $savePurchase->user_id = Auth::user()->id;
                    //update product
                    $productDetail = Product::whereId($product_id[$i])->first()->toArray();
                    $productStockIn = $productDetail['stock_in'];
                    $productStockAvailable = $productDetail['stock_available'];
                    $productManame['stock_in'] = $productStockIn + $savePurchase->quantity;
                    $productManame['unit_price_exit'] = $unit_price_exit[$i];

                    // $SupplierDetail=Supplier::whereId($supplier_name)->first()->toArray();
                    // $total_debit=$SupplierDetail['total_debit'];
                    // $toal_balance=$SupplierDetail['toal_balance'];
                    // $UpdateSupplier['total_debit']=$total_debit+$total_amount[$i];
                    // $UpdateSupplier['toal_balance']=$toal_balance-$total_amount[$i];
                    //  Product::where([['id', '=',$product_id[$i]]])->update($productManame);



                    //  supplier_id    purchase_id debit_amount    total_amount    created_at  updated_at  deleted_at
                    // $SupplierDetail=Supplier::whereId($supplier_name)->first()->toArray();
                    // $supplier_id=$SupplierDetail['supplier_id'];
                    // $purchase_id=$SupplierDetail['purchase_id'];
                    // $debit_amount=$SupplierDetail['debit_amount'];
                    // $toal_balance=$SupplierDetail['toal_balance'];

                    // $UpdateSupplier['total_debit']=$total_debit+$total_amount[$i];
                    // $UpdateSupplier['toal_balance']=$toal_balance-$total_amount[$i];
                    //  Product::where([['id', '=',$product_id[$i]]])->update($productManame);


                    // $productManame['gst']=$gst[$i];

                    // ALTER TABLE `suppliers` ADD `total_credit` FLOAT(100,2) NULL AFTER `email_verified_at`, ADD `total_debit` FLOAT(100,2) NULL AFTER `total_credit`, ADD `toal_balance` FLOAT(100,2) NULL AFTER `total_debit`;


                    Product::where([['id', '=', $product_id[$i]]])->update($productManame);

                    if (!$savePurchase->save()) {

                        // $saveSupplierDebitLog->total_amount = $total_amount[$i];
                        $request->session()->flash('message.level', 'Error');
                        $request->session()->flash('message.content', 'Somthing Went Wrong!');
                    } else {
                        $saveSupplierDebitLog = new SupplierDebitLog;
                        $saveSupplierDebitLog->purchase_invoice_id = $purchaseInvoiceId;
                        $saveSupplierDebitLog->supplier_id = $supplier_name;
                        $saveSupplierDebitLog->purchase_id = $savePurchase->id;
                        // $saveSupplierDebitLog->total_amount = $total_amount[$i];
                        $saveSupplierDebitLog->debit_amount = $total_amount[$i];
                        $saveSupplierDebitLog->comments = $request->purchase_discription;
                        // $saveSupplierDebitLog->payment_type =  $request->payment_type;
                        $saveSupplierDebitLog->save();
                    }
                }
                $request->session()->flash('message.level', 'success');
                $request->session()->flash('message.content', ' Saved Successfully!');
            }
            return view('AutoCare.purchase.add', $viewData);
        }
    }
    public function update(Request $request, $id = null)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $getFormAutoFillup = array();
        $viewData['pageTitle'] = 'Update Purchase Detail';
        $viewData['supplier'] = Supplier::orderBy('supplier_name', 'ASC')->pluck('supplier_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
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

            $updateInvoice = PurchaseInvoice::where('id', '=', $request->purchase_invoice_id);

            $savePurchaseInvoice['supplier_id'] = $supplier_name;
            $savePurchaseInvoice['purchase_invoice_number'] = $bill_num;
            $savePurchaseInvoice['payment_type'] = $request->payment_type;
            $savePurchaseInvoice['purchase_invoice_date'] = $bill_date;
            $savePurchaseInvoice['purchase_discription'] = $request->purchase_discription;
            // $savePurchaseInvoice['user_id'] = $user_id[$i];
            // $savePurchaseInvoice['total_purchase_amount'] = $total_purchase_amount[$i];\
            // $savePurchaseInvoice['purchase_due_amount'] = $purchase_due_amount[$i];
            $updateInvoice->update($savePurchaseInvoice);


            for ($i = 0; $i < count($product_id); $i++) {
                $updatePurchaseDetail = Purchase::where('id', '=', $request->id);
                // $savePurchase->id = $requst->id;
                $savePurchase['supplier_name'] = $supplier_name;
                $savePurchase['bill_num'] = $bill_num;
                $savePurchase['bill_date'] = $bill_date;
                // $savePurchase['product_id'] = $product_id[$i];
                // $savePurchase['company_name'] = $company_name[$i];
                // $savePurchase['model_number'] = $model_number[$i];
                $savePurchase['purchase_discription'] = $request->purchase_discription;
                $savePurchase['discription'] = $discription[$i];
                $savePurchase['part_number'] = $part_number[$i];
                $savePurchase['hsn'] = $hsn[$i];
                $savePurchase['unit_price'] = $unit_price[$i];
                $savePurchase['unit_price_exit'] = $unit_price_exit[$i];
                // $savePurchase['quantity'] = $quantity[$i];
                $savePurchase['gst'] = $gst[$i];
                $savePurchase['discount'] = isset($discount[$i]) ? $discount[$i] : 0;
                $savePurchase['total_amount'] = $total_amount[$i];

                // $productDetail=Product::whereId($product_id[$i])->first()->toArray();
                $productManame['unit_price_exit'] = $unit_price_exit[$i];
                // $productManame['gst']=$gst[$i];
                Product::where([['id', '=', $product_id[$i]]])->update($productManame);
                // $productStockIn=$productDetail['stock_in'];
                // $productStockAvailable=$productDetail['stock_available'];
                // $productManame['stock_in']=$productStockIn+$quantity[$i];
                // $productManame['stock_available']=$productStockAvailable+$quantity[$i];
                // Product::where([['id', '=',$product_id[$i]]])->update($productManame);

                if (!$updatePurchaseDetail->update($savePurchase)) {

                    $request->session()->flash('message.level', 'Error');
                    $request->session()->flash('message.content', 'Somthing Went Wrong!');
                } else {

                    $saveSupplierDebitLog = SupplierDebitLog::where('purchase_id', '=', $request->id);
                    // $saveSupplierDebit['purchase_invoice_id'] = $request->purchase_invoice_id;
                    $saveSupplierDebit['supplier_id'] = $supplier_name;
                    $saveSupplierDebit['purchase_id'] = $request->id;
                    $saveSupplierDebit['debit_amount'] = $total_amount[$i];
                    // $saveSupplierDebit['payment_type'] =$request->payment_type;
                    // $saveSupplierDebitLog->payment_type =  $request->payment_type;
                    // $saveSupplierDebit['debit_amount'] = $total_amount[$i];
                    $saveSupplierDebit['comments'] = $request->purchase_discription;
                    $saveSupplierDebitLog->update($saveSupplierDebit);
                }
            }
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', ' Updated Successfully!');
        }
        return redirect('/AutoCare/purchase/add/' . $request->id);
    }
    // this is for search
    public function view(Request $request)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        $getFormAutoFillup = array();
        if ($request->isMethod('post')) {
            $viewData['pageTitle'] = 'Add Party';
            $purchase = DB::table('purchases');
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->where('purchases.deleted_at', '=', null);
            if ($request->has('id') && $request->id != '') {
                $getFormAutoFillup['id'] = $request->id;
                $purchase->where('purchases.id', '=', $request->id);
            }
            if ($request->has('supplier_name') && $request->supplier_name != '') {
                $getFormAutoFillup['supplier_name'] = $request->supplier_name;
                $purchase->where('suppliers.supplier_name', 'like', '%' . $request->supplier_name . '%');
            }
            if ($request->has('created_at_from') && $request->created_at_from != '') {
                $getFormAutoFillup['created_at_from'] = $request->created_at_from;
                $purchase->whereDate('purchases.bill_date', '<=', $request->created_at_from);
            }
            if ($request->has('created_at_to') && $request->created_at_to != '') {
                $getFormAutoFillup['created_at_to'] = $request->created_at_to;
                $purchase->whereDate('purchases.bill_date', '>=', $request->created_at_to);
            }
            if ($request->has('product_name') && $request->product_name != '') {
                $getFormAutoFillup['product_name'] = $request->product_name;
                $purchase->where('products.product_name', 'like', '%' . $request->product_name . '%');
            }
            if ($request->has('company_name') && $request->company_name != '') {
                $getFormAutoFillup['company_name'] = $request->company_name;
                $purchase->where('purchases.brand_name', 'like', '%' . $request->company_name . '%');
            }
            if ($request->has('brand') && $request->brand != '') {
                $purchase->where('purchases.company_name', '=', $request->brand);
            }
            if ($request->has('model_number') && $request->model_number != '') {
                $purchase->where('products.model_number', '=', $request->model_number);
            }

            $purchase->select('purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'ASC');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            // print_r($viewData['purchase']);
            // exit;

            return view('AutoCare.purchase.search', $viewData)->with($getFormAutoFillup);
            ;

        } else {
            $viewData['pageTitle'] = 'View Purchase';
            $purchase = DB::table('purchases');
            $purchase->where('purchases.deleted_at', '=', null);
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->select('purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'asc');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            // $viewData['purchase'] = $purchase;
            //	$purchase= DB::table('purchases');
            //$purchase->orderBy('id','desc');
            //$purchase= $purchase->get();
            //$viewData['purchase']=json_decode(json_encode($purchase), true);
            //Ashu@97047$&(!   ca7zaoly6g7y
            return view('AutoCare.purchase.search', $viewData);
        }

    }
    public function trash(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        if (($id != null) && (Purchase::where('id', $id)->delete())) {
            $request->session()->flash('message.level', 'warning');
            $request->session()->flash('message.content', 'Purchase was Trashed!');
            $viewData['pageTitle'] = 'Purchase';
            $purchase = DB::table('purchases');
            $purchase->where('purchases.deleted_at', '=', null);
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->select('purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'desc');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            return view('AutoCare.purchase.search', $viewData);
        } else {
            session()->flash('status', ['danger', 'Operation was Failed!']);
            $viewData['pageTitle'] = 'Purchase';
            $purchase = DB::table('purchases');
            $purchase->where('purchases.deleted_at', '=', null);
            $purchase->leftJoin('products', 'products.id', '=', 'purchases.product_id');
            $purchase->leftJoin('suppliers', 'suppliers.id', '=', 'purchases.supplier_name');
            $purchase->leftJoin('modals', 'modals.id', '=', 'purchases.model_number');
            $purchase->leftJoin('brands', 'brands.id', '=', 'purchases.company_name');
            $purchase->select('purchases.*', 'products.product_name as product_name', 'suppliers.supplier_name as supplier_name_from_supplier', 'brands.brand_name as company_name_from_brand', 'modals.model_name as model_number');
            $purchase->orderBy('bill_date', 'desc');
            $purchase = $purchase->get();
            $viewData['purchase'] = json_decode(json_encode($purchase), true);
            return view('AutoCare.purchase.search', $viewData);
        }

    }
    public function trashedList()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        $TrashedPurchase = Purchase::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
        return view('AutoCare.purchase.delete', compact('TrashedPurchase', 'TrashedPurchase'));

    }
    public function permanemetDelete(Request $request, $id)
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();

        $purhaseForDelete = DB::table('purchases')->select('purchases.product_id', 'purchases.quantity')
            ->where('purchases.id', '=', $id)
            ->distinct()->get();
        $ProductId = $purhaseForDelete[0]->product_id;
        $quantity = $purhaseForDelete[0]->quantity;
        $productDetail = Product::whereId($ProductId)->first()->toArray();

        if (($productDetail['stock_in'] - $productDetail['stock_out']) > $quantity) {
            $productStockIn = $productDetail['stock_in'];
            $productStockAvailable = $quantity;
            $productManame['stock_in'] = $productStockIn - $quantity;
            $productManame['stock_available'] = $productStockAvailable - $quantity;
            Product::where([['id', '=', $productDetail['id']]])->update($productManame);

            if (($id != null) && (Purchase::where('id', $id)->forceDelete())) {
                SupplierDebitLog::where('purchase_id', '=', $id)->forceDelete();

                $request->session()->flash('message.level', 'warning');
                $request->session()->flash('message.content', "Purchase was deleted Permanently and Can't rollback in Future!");
            } else {
                session()->flash('status', ['danger', 'Operation was Failed!']);
            }


        } else {
            $request->session()->flash('message.level', 'Error');
            $request->session()->flash('message.content', 'Sorry Quantity Used! you an not delete permanently');
        }
        $TrashedPurchase = Purchase::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(50);
        return view('AutoCare.purchase.delete', compact('TrashedPurchase', 'TrashedPurchase'));

    }
}
