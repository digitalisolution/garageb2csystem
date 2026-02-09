<?php

namespace App\Http\Controllers;

use App\SaleProduct;
use Illuminate\Http\Request;
use App\Workshop;
use App\Product;
use App\Service; 
use DB; 
use App\WorkshopProduct;
use App\WorkshopService;
use App\Modal;
use App\Brand;
use App\ServiceType;
use App\HeaderLink;
use Mail;
use App\Mail\SendMailToCustomer;
use App\Jobs\SendEMailJob;
use App\Customer;
use App\CustomerDebitLog;
use App\VehicleDetail;
use Auth;

class SaleProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $viewData['pageTitle'] = 'Add Product Sale'; 
        $viewData['product'] = Product::pluck('product_name', 'id');
        $viewData['service'] = Service::pluck('service_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        $viewData['ServiceType'] = ServiceType::pluck('service_type_name', 'id');
        $viewData['registered_vehicle_select'] = VehicleDetail::pluck('vehicle_reg_number', 'vehicle_reg_number');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['header_link'] =  HeaderLink::where("menu_id",'3')->select("link_title","link_name")->orderBy('id','desc')->get();
        $viewData['registered_vehicle_select'] = VehicleDetail::pluck('vehicle_reg_number', 'vehicle_reg_number');
        return view('AutoCare.sale_product.add', $viewData);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        
        $viewData['pageTitle'] = 'Add Workshop'; 
        $viewData['product'] = Product::pluck('product_name', 'id');
        $viewData['service'] = Service::pluck('service_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        $viewData['ServiceType'] = ServiceType::pluck('service_type_name', 'id');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['header_link'] =  HeaderLink::where("menu_id",'3')->select("link_title","link_name")->orderBy('id','desc')->get();
        

        $PartyManage =  request()->except(['_token','registered_vehicle','service_id','product_id','status','service_quantity','product_quantity','service_type','service_price','product_price','workshop_product_brand','workshop_product_model','workshop_service_brand','workshop_service_model','service_type_id']);
        $PartyManage['is_workshop']=0;
         $PartyManage['is_complete']=1;
        
         if(!isset($request->customer_id)) 
           {
                $CustomerSave= new Customer();
                $CustomerSave->customer_name    = $request->name;
                $CustomerSave->customer_contact_number    = $request->mobile;
                $CustomerSave->customer_alt_number   = $request->landline;     
                $CustomerSave->customer_email    = $request->email;
                $CustomerSave->customer_address=$request->address;
                $CustomerSave->customer_gstin    = $request->gst_no;
                $CustomerSave->created_by=Auth::user()->id;
                $CustomerSave->save();
                $PartyManage['customer_id']= $CustomerSave->id;
           }   
        $PartyManage['status']="Complete(Spare Purchase)";
        $PartyManage = new Workshop($PartyManage);
        if($PartyManage->save()){

            for($i=0; $i < count($request->product_id); $i++){

                    $WorkshopProduct= new WorkshopProduct();
                    $WorkshopProduct->workshop_id  = $PartyManage->id;
                    $WorkshopProduct->product_id  = $request->product_id[$i];
                    $WorkshopProduct->product_quantity  = $request->product_quantity[$i];
                    $WorkshopProduct->product_price = $request->product_price[$i];
                    $WorkshopProduct->workshop_product_brand    = $request->workshop_product_brand[$i];
                    $WorkshopProduct->workshop_product_model    = $request->workshop_product_model[$i];    
                    $WorkshopProduct->save();

                $productDetail=Product::whereId($request->product_id[$i])->first()->toArray();
                $productStockOut=$productDetail['stock_out'];
                if($productStockOut==null)
                {
                    $productStockOut=0;
                }
                $productStockAvailable=$productDetail['stock_available'];
                $productManame['stock_out']=$productStockOut+$request->product_quantity[$i];
                $productManame['stock_available']=$productStockAvailable-$request->product_quantity[$i];
                Product::where([['id', '=',$request->product_id[$i]]])->update($productManame);

           } 

            $CustomerDebitLog= new CustomerDebitLog();
            $CustomerDebitLog->sale_id    = $PartyManage->id;
           
             if(isset($CustomerSave->id))
            {
                 $CustomerDebitLog->customer_id    = $CustomerSave->id;
            }
            else
            {
                 $CustomerDebitLog->customer_id    = $request->customer_id;
            }
            $CustomerDebitLog->credit   = $request->paid_price;            
            $CustomerDebitLog->comments    = $request->notes;
            $CustomerDebitLog->is_debit    = 0;
            $CustomerDebitLog->save();
            $request->session()->flash('message.level', 'success');
            $request->session()->flash('message.content', ' Saved Successfully!');
        }
         $viewData['workshopId'] = $PartyManage->id;
        return view('AutoCare.sale_product.add', $viewData);
                
            
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
     * @param  \App\SaleProduct  $saleProduct
     * @return \Illuminate\Http\Response
     */
    public function show(SaleProduct $saleProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SaleProduct  $saleProduct
     * @return \Illuminate\Http\Response
     */
    public function edit(SaleProduct $saleProduct, $id = null)
    {

         $viewData['product'] = Product::pluck('product_name', 'id');
        $viewData['service'] = Service::pluck('service_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        $viewData['ServiceType'] = ServiceType::pluck('service_type_name', 'id');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['header_link'] =  HeaderLink::where("menu_id",'3')->select("link_title","link_name")->orderBy('id','desc')->get();


          $getFormAutoFillup = Workshop::whereId($id)->first()->toArray();
              $viewData['workshopProductData']= WorkshopProduct::where('workshop_id','=',$id)->get();
              $viewData['workshopServiceData']= WorkshopService::where('workshop_id','=',$id)->get();
            return view('AutoCare.sale_product.add', $viewData)->with($getFormAutoFillup);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SaleProduct  $saleProduct
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SaleProduct $saleProduct)
    {
        $viewData['pageTitle'] = 'Add Workshop'; 
        $viewData['product'] = Product::pluck('product_name', 'id');
        $viewData['service'] = Service::pluck('service_name', 'id');
        $viewData['model_select'] = Modal::pluck('model_name', 'id');
        $viewData['brand_select'] = Brand::pluck('brand_name', 'id');
        $viewData['ServiceType'] = ServiceType::pluck('service_type_name', 'id');
        $viewData['registered_vehicle_select'] = VehicleDetail::pluck('vehicle_reg_number', 'vehicle_reg_number');
        $viewData['customerNameSelect'] = Customer::pluck('customer_name', 'id');
        $viewData['header_link'] =  HeaderLink::where("menu_id",'3')->select("link_title","link_name")->orderBy('id','desc')->get();
        // This if condition for fill detail for update otherwise for save and update 
      
            if ($request->isMethod('post')){
                $getFormAutoFillup = array();               
                if(isset($request->id) && $request->id != null) 
                {
                    if ($request->isMethod('post')){
                        $PartyManage=    request()->except(['_token','service_id','product_id','service_quantity','product_quantity','service_type','service_price','product_price','workshop_product_brand','workshop_product_model','workshop_service_brand','workshop_service_model','service_type_id']);
                         // $PartyManage['is_workshop']=0;
         // $PartyManage['is_complete']=1;
                         
                        if(Workshop::where([['id', '=', $request->id]])->update($PartyManage)){
                              $workshopProductData= WorkshopProduct::where('workshop_id','=',$request->id)->get();
                              foreach ($workshopProductData as $key => $value) {
                                    $stock_out=$value['product_quantity'];
                                    $product_id=$value['product_id'];
                                    $stock_available=$value['stock_available'];
                                     
                                    $productDetail=Product::whereId($product_id)->first()->toArray();
                                    $productStockOut=$productDetail['stock_out'];
                                    $productStockAvailable=$productDetail['stock_available'];
                                    $productManame['stock_out']=$productStockOut-$stock_out;
                                    $productManame['stock_available']=$productStockAvailable+ $stock_available;
                                    Product::where([['id', '=',$product_id]])->update($productManame);
                              }

                            WorkshopProduct::where('workshop_id',$request->id)->forceDelete();
                        for($i=0; $i < count($request->product_id); $i++){

                                $WorkshopProduct= new WorkshopProduct();
                                $WorkshopProduct->workshop_id  = $request->id;
                                $WorkshopProduct->product_id  = $request->product_id[$i];
                                $WorkshopProduct->product_quantity  = $request->product_quantity[$i];
                                $WorkshopProduct->product_price = $request->product_price[$i];
                                $WorkshopProduct->workshop_product_brand    = $request->workshop_product_brand[$i];
                                $WorkshopProduct->workshop_product_model    = $request->workshop_product_model[$i];    
                                $WorkshopProduct->save();

                            $productDetail=Product::whereId($request->product_id[$i])->first()->toArray();
                            $productStockOut=$productDetail['stock_out'];
                             if($productStockOut==null)
                            {
                                $productStockOut=0;
                            }
                            $productStockAvailable=$productDetail['stock_available'];
                            $productManame['stock_out']=$productStockOut+$request->product_quantity[$i];
                            $productManame['stock_available']=$productStockAvailable-$request->product_quantity[$i];
                            Product::where([['id', '=',$request->product_id[$i]]])->update($productManame);

                       } 
                        if($request->is_complete==1)
                        {
                             // Mail::to($request->email)->send(new SendMailToCustomer($request->id));
                            //   SendEMailJob::dispatch($request->email,$request->id)
                            // ->delay(now()->addSeconds(5));
                        }
                       
                            $request->session()->flash('message.level', 'success');
                            $request->session()->flash('message.content', ' updated Successfully!');
                        }
                    }
                    $viewData['workshopId'] = $request->id;
                     return redirect('/AutoCare/sale/edit/'.$request->id);
                }
    }
}

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SaleProduct  $saleProduct
     * @return \Illuminate\Http\Response
     */
    public function destroy(SaleProduct $saleProduct)
    {
        //
    }
}
