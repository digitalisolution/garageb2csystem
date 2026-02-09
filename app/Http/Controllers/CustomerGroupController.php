<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index()
    {
        $groups = CustomerGroup::orderBy('name')->get();
        return view('AutoCare.customer-groups.index', compact('groups'));
    }

    public function create()
    {
        return view('AutoCare.customer-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'due_date_option' => 'nullable|in:first_date,last_date,30_days_invoice_date,manual',
            'manual_due_date' => 'nullable|date',
            'product_type' => 'nullable|array',
            'product_type.*' => 'in:tyre,service',
        ]);

        CustomerGroup::create([
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'due_date_option' => $request->due_date_option,
            'manual_due_date' => $request->due_date_option === 'manual' ? $request->manual_due_date : null,
            'product_type' => $request->product_type,
        ]);

        return redirect()->route('customer-groups.index')->with('success', 'Customer Group created successfully.');
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('AutoCare.customer-groups.create', compact('customerGroup'));
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'due_date_option' => 'nullable|in:first_date,last_date,30_days_invoice_date,manual',
            'manual_due_date' => 'nullable|date',
            'product_type' => 'nullable|array',
            'product_type.*' => 'in:tyre,service',
        ]);

        $customerGroup->update([
            'name' => $request->name,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'due_date_option' => $request->due_date_option,
            'manual_due_date' => $request->due_date_option === 'manual' ? $request->manual_due_date : null,
            'product_type' => $request->product_type,
        ]);

        return redirect()->route('customer-groups.index')->with('success', 'Customer Group updated successfully.');
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        $customerGroup->delete();
        return redirect()->route('customer-groups.index')->with('success', 'Customer Group deleted successfully.');
    }
}