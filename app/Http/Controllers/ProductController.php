<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use DB;
use App\Modal;
use App\Models\Brand;
use App\Models\HeaderLink;

class ProductController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
	}
	public function save(Request $request, $id = null)
	{
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
		$viewData['pageTitle'] = 'Supplier Dretail';
		$viewData['pageTitle'] = 'Add Purchase Detail';
		$viewData['option1'] = 'Add Supplier';
		$viewData['optionValue1'] = "AutoCare/supplier/add";
		$viewData['option2'] = 'Add Product Detail';
		$viewData['optionValue2'] = "AutoCare/product/add";


		$viewData['model_select'] = Modal::pluck('model_name', 'id');
		$viewData['brand_select'] = Brand::pluck('brand_name', 'id');
		// This if condition for fill detail for update otherwise for save and update 
		if (isset($id) && $id != null) {
			$getFormAutoFillup = Product::whereId($id)->first()->toArray();
			return view('AutoCare.product.add', $viewData)->with($getFormAutoFillup);
		} else if ((!isset($id) && $id == null) && !$request->isMethod('post')) {
			return view('AutoCare.product.add', $viewData);
		} else {
			// This if condition for fill detail for  update otherwise for  save 
			if ($request->isMethod('post')) {
				$getFormAutoFillup = array();
				if (isset($request->id) && $request->id != null) {
					if ($request->isMethod('post')) {
						$productManame = request()->except(['_token']);
						if (Product::where([['id', '=', $request->id]])->update($productManame)) {
							$request->session()->flash('message.level', 'success');
							$request->session()->flash('message.content', 'Product updated Successfully!');
						}
					}
					return redirect('/AutoCare/product/add/' . $request->id);
				} else {
					$productManame = $request->all();
					//print_r($productManame);
					///exit;
					$productManame = new Product($productManame);

					if ($productManame->save()) {
						$request->session()->flash('message.level', 'success');
						$request->session()->flash('message.content', ' Saved Successfully!');
					}
					return view('AutoCare.product.add', $viewData);
				}
			}
		}
	}
	public function view(Request $request)
	{
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
		$viewData['model_select'] = Modal::pluck('model_name', 'id');
		$viewData['brand_select'] = Brand::pluck('brand_name', 'id');

		if ($request->isMethod('post')) {
			$viewData['pageTitle'] = 'Supplier';
			$Supplier = DB::table('products');
			$Supplier->leftJoin('modals', 'modals.id', '=', 'products.model_number');
			$Supplier->leftJoin('brands', 'brands.id', '=', 'products.company_name');
			$Supplier->where('products.deleted_at', '=', null);
			if ($request->has('id') && $request->id != '') {
				$Supplier->where('products.id', '=', $request->id);
			}
			if ($request->has('product_name') && $request->product_name != '') {
				$Supplier->where('product_name', 'like', '%' . $request->product_name . '%');
			}
			if ($request->has('created_at_from') && $request->created_at_from != '') {
				$Supplier->whereDate('products.created_at', '<=', $request->created_at_from);
			}
			if ($request->has('created_at_to') && $request->created_at_to != '') {
				$Supplier->whereDate('products.created_at', '>=', $request->created_at_to);
			}
			// if($request->has('model_number') && $request->model_number !=''){
			// 	$Supplier->where('mobile', '=', $request->model_number);
			// }
			// if($request->has('email') && $request->email !=''){
			// 	$Supplier->where('products.email', '=', $request->email);
			// }
			if ($request->has('brand') && $request->brand != '') {
				$Supplier->where('products.company_name', '=', $request->brand);
			}
			if ($request->has('model_number') && $request->model_number != '') {
				$Supplier->where('products.model_number', '=', $request->model_number);
			}
			$Supplier->orderBy('products.id', 'desc');
			$Supplier->select('products.*', 'brands.brand_name as brand_name_from_brand', 'modals.model_name as model_name_from_modal');
			$Supplier = $Supplier->get();
			$viewData['product'] = json_decode(json_encode($Supplier), true);
			return view('AutoCare.product.search', $viewData);

		} else {
			$viewData['pageTitle'] = 'Supplier';
			$Supplier = DB::table('products');
			$Supplier->leftJoin('modals', 'modals.id', '=', 'products.model_number');
			$Supplier->leftJoin('brands', 'brands.id', '=', 'products.company_name');
			$Supplier->where('products.deleted_at', '=', null);
			$Supplier->orderBy('products.id', 'desc');
			$Supplier->select('products.*', 'brands.brand_name as brand_name_from_brand', 'modals.model_name as model_name_from_modal');
			$Supplier = $Supplier->get();
			$viewData['product'] = json_decode(json_encode($Supplier), true);
			// $viewData['product'] = Product::orderBy('id','desc')->get();
			//	$Supplier= DB::table('Suppliers');
			//$Supplier->orderBy('id','desc');
			//$Supplier= $Supplier->get();
			//$viewData['Supplier']=json_decode(json_encode($Supplier), true);
			return view('AutoCare.product.search', $viewData);
		}

	}
	public function trash(Request $request, $id)
	{
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
		$viewData['model_select'] = Modal::pluck('model_name', 'id');
		$viewData['brand_select'] = Brand::pluck('brand_name', 'id');
		if (($id != null) && (Product::where('id', $id)->delete())) {
			$request->session()->flash('message.level', 'warning');
			$request->session()->flash('message.content', 'Product was Trashed!');
			$viewData['pageTitle'] = 'Supplier';
			$Supplier = DB::table('products');
			$Supplier->leftJoin('modals', 'modals.id', '=', 'products.model_number');
			$Supplier->leftJoin('brands', 'brands.id', '=', 'products.company_name');
			$Supplier->where('products.deleted_at', '=', null);
			$Supplier->orderBy('products.id', 'desc');
			$Supplier->select('products.*', 'brands.brand_name as brand_name_from_brand', 'modals.model_name as model_name_from_modal');
			$Supplier = $Supplier->get();
			$viewData['product'] = json_decode(json_encode($Supplier), true);
			return view('AutoCare.product.search', $viewData);
		} else {
			session()->flash('status', ['danger', 'Operation was Failed!']);
			$viewData['pageTitle'] = 'Supplier';
			$Supplier = DB::table('products');
			$Supplier->leftJoin('modals', 'modals.id', '=', 'products.model_number');
			$Supplier->leftJoin('brands', 'brands.id', '=', 'products.company_name');
			$Supplier->where('products.deleted_at', '=', null);
			$Supplier->orderBy('products.id', 'desc');
			$Supplier->select('products.*', 'brands.brand_name as brand_name_from_brand', 'modals.model_name as model_name_from_modal');
			$Supplier = $Supplier->get();
			$viewData['product'] = json_decode(json_encode($Supplier), true);
			return view('AutoCare.product.search', $viewData);
		}

	}
	public function trashedList()
	{
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
		$TrashedParty = Product::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
		// $TrashedParty=$TrashedParty
		return view('AutoCare.product.delete', compact('TrashedParty', 'TrashedParty'));
	}
	public function permanemetDelete(Request $request, $id)
	{
		$viewData['header_link'] = HeaderLink::where("menu_id", '3')->select("link_title", "link_name")->orderBy('id', 'desc')->get();
		if (($id != null) && (Product::where('id', $id)->forceDelete())) {
			$request->session()->flash('message.level', 'warning');
			$request->session()->flash('message.content', "Product was deleted Permanently and Can't rollback in Future!");
		} else {
			session()->flash('status', ['danger', 'Operation was Failed!']);
		}

		$TrashedParty = Product::orderBy('deleted_at', 'desc')->onlyTrashed()->simplePaginate(10);
		return view('AutoCare.product.delete', compact('TrashedParty', 'TrashedParty'));
	}
}
