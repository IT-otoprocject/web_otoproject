<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Query awal untuk semua data SPK
        $query = Spk::query();

        // Filter tambahan berdasarkan tanggal hari ini (Wajib)
        $query->whereDate('tanggal', Carbon::today()); // Tampilkan hanya data dengan tanggal hari ini

        // Filter pencarian (opsional)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan garage, status, dan range tanggal
        if ($request->filled('garage')) {
            $query->where('garage', $request->garage);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting berdasarkan tanggal dan status
        $query->orderBy('tanggal', 'desc') // Sorting tanggal terbaru ke terlama
            ->orderByRaw("
            CASE
                WHEN status = 'Baru Diterbitkan' THEN 1
                WHEN status = 'Dalam Pengerjaan' THEN 2
                WHEN status = 'Cancel' THEN 3
                WHEN status = 'Sudah Selesai' THEN 4
                ELSE 5
            END ASC");

        // Ambil hasil query
        $spks = $query->get();

        // Kirim data ke view
        return view('dashboard', compact('spks'));
    }
}
