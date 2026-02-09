<?php

namespace App\Http\Controllers;
use App\Models\HeaderLink;
use Illuminate\Support\Facades\DB;


class ApiOrderController extends Controller
{
    public function viewApiOrders()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '13')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        // Fetch API orders ordered by ID in descending order and paginate with 25 items per page
        $viewData['apiOrders'] = DB::table('api_tyre_orders')
                        ->orderBy('id', 'desc')
                        ->get();        
    
        return view('AutoCare.api_orders.show', $viewData);
    }

}
