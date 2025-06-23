<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PaymentHistoryService
{
    public function getPaymentHistory($jobId)
    {
        return DB::table('payment_histories')
        ->join('workshops', 'workshops.id', '=', 'payment_histories.job_id') // Join workshops table
        ->leftJoin('customers', 'customers.id', '=', 'workshops.customer_id') // Left join customers table
        ->leftJoin('customer_debit_logs', 'customer_debit_logs.payment_history_id', '=', 'payment_histories.id') // Directly join debit logs
        ->where('payment_histories.job_id', '=', $jobId) // Filter by job_id
        ->select(
            'payment_histories.*',
            'customers.*',
            'workshops.*',
            'workshops.id as workshop_id',
            'workshops.name as workshop_name',
            'customers.id as customer_id',
            DB::raw('COALESCE(customers.customer_name, workshops.name) as customer_name'),
            DB::raw('COALESCE(customers.customer_address, workshops.address) as customer_address'),
            DB::raw('COALESCE(customers.customer_contact_number, workshops.mobile) as customer_contact_number'),
            DB::raw('COALESCE(customers.customer_email, workshops.email) as customer_email'),
            'customer_debit_logs.id as debit_log_id',
            'customer_debit_logs.debit_amount',
            'customer_debit_logs.payment_type'
        )
        ->get();
    }
}