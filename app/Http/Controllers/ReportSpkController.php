<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SpkExport;
use App\Exports\SpkAvg;
use App\Exports\SpkAvgMekanikProduct;
use App\Exports\SpkAvgMekanik;

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

    public function exportAvgBarang(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        return \Maatwebsite\Excel\Facades\Excel::download(new SpkAvg($tanggalMulai, $tanggalAkhir), 'rata_rata_pengerjaan_barang.xlsx');
    }

    public function exportAvgMekanikProduct(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        return \Maatwebsite\Excel\Facades\Excel::download(new SpkAvgMekanikProduct($tanggalMulai, $tanggalAkhir), 'rata_rata_mekanik_per_produk.xlsx');
    }

    public function exportAvgMekanik(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        return \Maatwebsite\Excel\Facades\Excel::download(new SpkAvgMekanik($tanggalMulai, $tanggalAkhir), 'rata_rata_kerja_mekanik.xlsx');
    }
}
