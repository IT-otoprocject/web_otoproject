<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SpkExport;

class ReportSpkController extends Controller
{
    public function index()
    {
        return view('report.spk.report_spk');
    }

    public function export(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'nullable|string',
            'garage' => 'nullable|string',
        ]);

        $filters = [
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'garage' => $request->garage,
        ];

        return Excel::download(new SpkExport($filters), 'report_spk_' . date('Y-m-d_His') . '.xlsx');
    }
}
