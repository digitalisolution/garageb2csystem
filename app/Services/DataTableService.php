<?php

namespace App\Services;

use Yajra\DataTables\Facades\DataTables;

class DataTableService
{
    public function getDataTable($query, array $columns)
    {
        return DataTables::of($query)
            ->addColumn('actions', function ($row) {
                return view('components.datatables.actions', compact('row'))->render();
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
