<?php

namespace App\Http\Controllers;

use App\Models\PaymentRecord;
use Illuminate\Http\Request;

class PaymentRecordController extends Controller
{
    // Display a listing of vehicles
    public function index()
    {
        $paymentRecords = PaymentRecord::orderBy('id', 'desc')->get(); // Load 25 per page
        return view('AutoCare.paymentRecords.index', compact('paymentRecords'));
    }

}