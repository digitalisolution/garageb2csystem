<?php

namespace App\Http\Controllers;

use App\Models\PaymentRecord;
use App\Models\HeaderLink;
use Illuminate\Http\Request;

class PaymentRecordController extends Controller
{
    // Display a listing of vehicles
    public function index()
    {
        $viewData['header_link'] = HeaderLink::where("menu_id", '14')->select("link_title", "link_name")->orderBy('id', 'ASC')->get();
        $viewData['paymentRecords'] = PaymentRecord::orderBy('id', 'desc')->get(); // Load 25 per page
        return view('AutoCare.paymentRecords.index', $viewData);
    }

}