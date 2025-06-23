<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;


class ApiOrderController extends Controller
{
    public function viewApiOrders()
    {
        // Fetch API orders ordered by ID in descending order and paginate with 25 items per page
        $apiOrders = DB::table('api_tyre_orders')
                        ->orderBy('id', 'desc')
                        ->get();        
    
        return view('AutoCare.api_orders.show', compact('apiOrders'));
    }

}
