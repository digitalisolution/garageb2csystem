<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseReturn;
use App\HeaderLink;
use DB;

class SalesReturnController extends Controller
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
    public function show(Request $request)
    {
        $viewData['header_link'] =  HeaderLink::where("menu_id",'3')->select("link_title","link_name")->orderBy('id','desc')->get();
        $getFormAutoFillup = array();
        if($request->isMethod('post'))
        {
           // DB::enableQueryLog();
            $viewData['pageTitle'] = 'Spare Return';  
            $purchase= DB::table('return_spare_logs');
            $purchase->leftJoin('workshop_products','workshop_products.workshop_id','=','return_spare_logs.job_id');   
            $purchase->leftJoin('products','products.id','=','workshop_products.product_id');         
            
            $purchase->where('return_spare_logs.deleted_at','=',null);
            if($request->has('id') && $request->id !=''){
                $getFormAutoFillup['id']=$request->id;
                $purchase->where('return_spare_logs.id', '=', $request->id);
            }
            if($request->has('supplier_name') && $request->supplier_name !=''){
                $getFormAutoFillup['supplier_name']=$request->supplier_name;
                $purchase->where('return_spare_logs.supplier_name', 'like', '%'.$request->supplier_name.'%');
            }
            if($request->has('created_at_from') && $request->created_at_from !=''){
                $getFormAutoFillup['created_at_from']=$request->created_at_from;
                $purchase->whereDate('return_spare_logs.created_at', '<=', $request->created_at_from);
            }
            if($request->has('created_at_to') && $request->created_at_to !=''){
                $getFormAutoFillup['created_at_to']=$request->created_at_to;
                $purchase->whereDate('return_spare_logs.created_at', '>=', $request->created_at_to);
            }
            if($request->has('product_name') && $request->product_name !=''){
                $getFormAutoFillup['product_name']=$request->product_name;
                $purchase->where('products.product_name', 'like', '%'.$request->product_name.'%');
            }
            if($request->has('company_name') && $request->company_name !=''){
                $getFormAutoFillup['company_name']=$request->company_name;
                $purchase->where('brands.brand_name', 'like', '%'.$request->company_name.'%');
            }

            $purchase->select('return_spare_logs.*','products.product_name as ProductName');
            $purchase->orderBy('created_at','desc');
            $purchase= $purchase->get();
            $viewData['purchase']=json_decode(json_encode($purchase), true);
             // $laQuery = DB::getQueryLog();
                 // DB::disableQueryLog();
                 // print_r($laQuery);
                 //  print_r($purchase);
                 // exit;
            return view('AutoCare.sale_product.return_sale_log', $viewData)->with($getFormAutoFillup);;

        }else
        {
            $viewData['pageTitle'] = 'View Purchase';      
            $purchase= DB::table('return_spare_logs');      
            $purchase->leftJoin('workshop_products','workshop_products.workshop_id','=','return_spare_logs.job_id');   
            $purchase->leftJoin('products','products.id','=','workshop_products.product_id');  
            $purchase->where('return_spare_logs.deleted_at','=',null);
            $purchase->select('return_spare_logs.*','products.product_name as ProductName');
               $purchase->orderBy('created_at','desc');
             $purchase= $purchase->get();
            $viewData['purchase']=json_decode(json_encode($purchase), true);
            //Ashu@97047$&(!   ca7zaoly6g7y
            return view('AutoCare.sale_product.return_sale_log', $viewData);
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
