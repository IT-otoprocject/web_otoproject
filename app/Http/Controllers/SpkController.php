<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use App\Models\SpkItem;

class SpkController extends Controller
{
    public function create()
    {
        return view('spk.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'garage' => 'required|string',
            'tanggal' => 'required|date',
            'teknisi_1' => 'required|string',
            'teknisi_2' => 'nullable|string',
            'customer' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_mobil' => 'required|string',
            'no_plat' => 'required|string',
            'nama_barang' => 'required|array',
            'nama_barang.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        // Format the date to 'dmy' (e.g., 311225 for 31 December 2025)
        $formattedDate = \Carbon\Carbon::parse($validatedData['tanggal'])->format('dmy');

        // Get the count of existing SPK for the same date
        $existingCount = Spk::whereDate('tanggal', $validatedData['tanggal'])->count();

        // Generate the next sequence number
        $nextNumber = $existingCount + 1;

        // Create the no_spk in the desired format
        $no_spk = "SPK/{$formattedDate}/{$nextNumber}";

        // Simpan data SPK
        $spk = Spk::create([
            'no_spk' => $no_spk,
            'garage' => $validatedData['garage'],
            'tanggal' => $validatedData['tanggal'],
            'teknisi_1' => $validatedData['teknisi_1'],
            'teknisi_2' => $validatedData['teknisi_2'],
            'customer' => $validatedData['customer'],
            'alamat' => $validatedData['alamat'],
            'no_hp' => $validatedData['no_hp'],
            'jenis_mobil' => $validatedData['jenis_mobil'],
            'no_plat' => $validatedData['no_plat'],
            'catatan' => $validatedData['catatan'] ?? null,
        ]);



        // Loop untuk menyimpan setiap item ke database
        foreach ($validatedData['nama_barang'] as $index => $nama_barang) {
            SpkItem::create([
                'spk_id' => $spk->id,
                'nama_barang' => $nama_barang,
                'qty' => $validatedData['qty'][$index],
            ]);
        }

        // Set session flash message popup
        session()->flash('message', 'SPK telah dibuat segera Infokan ke Mekanik 😁');

        // Redirect ke halaman untuk mekanik
        return redirect()->route('mekanik.spk.show', $spk->id)->with('success', 'SPK berhasil diterbitkan.');
    }

    // Menampilkan SPK untuk Mekanik
    public function show($id)
    {
        $spk = Spk::findOrFail($id);
        return view('mekanik.spk.show', compact('spk'));
    }
    // public function show(Spk $spk)
    // {
    //     return view('mekanik.spk.show', compact('spk'));
    // }




    // menampilkan daftar SPK yang baru diterbitkan untuk mekanik.
    public function index(Request $request)
    {
        // Query awal untuk semua data SPK
        $query = SPK::query();

        // Filter pencarian (opsional)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('customer', 'like', "%{$search}%")
                    ->orWhere('no_hp', 'like', "%{$search}%")
                    ->orWhere('no_plat', 'like', "%{$search}%");
            });
        }

        // Filter tambahan berdasarkan garage, status, dan range tanggal
        if ($request->filled('garage')) {
            $query->where('garage', $request->garage);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
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
        END ASC
    ");

        // Ambil hasil query
        $spks = $query->get();

        return view('spk.index', compact('spks'));
    }

    // edit SPK 
    public function edit($spk_id)
    {
        // Ambil data SPK berdasarkan ID
        $spk = Spk::findOrFail($spk_id);

        // Kirim data ke view edit
        return view('spk.edit', compact('spk'));
    }

    public function update(Request $request, $spk_id)
    {
        // Validasi input dan simpan hasil ke variabel $validatedData
        $validatedData = $request->validate([
            'garage' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'teknisi_1' => 'nullable|string|max:255',
            'teknisi_2' => 'nullable|string|max:255',
            'customer' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:500',
            'no_hp' => 'required|string|max:20',
            'jenis_mobil' => 'nullable|string|max:255',
            'no_plat' => 'nullable|string|max:20',
            'catatan' => 'nullable|string|max:1000',
            'nama_barang' => 'required|array', // Pastikan array barang harus ada
            'nama_barang.*' => 'required|string|max:255', // Validasi setiap elemen array nama_barang
            'qty' => 'required|array', // Pastikan array qty harus ada
            'qty.*' => 'required|integer|min:1', // Validasi setiap elemen array qty
        ]);

        // Update data utama SPK
        $spk = Spk::findOrFail($spk_id);
        $spk->update([
            'garage' => $validatedData['garage'],
            'tanggal' => $validatedData['tanggal'],
            'teknisi_1' => $validatedData['teknisi_1'],
            'teknisi_2' => $validatedData['teknisi_2'],
            'customer' => $validatedData['customer'],
            'alamat' => $validatedData['alamat'],
            'no_hp' => $validatedData['no_hp'],
            'jenis_mobil' => $validatedData['jenis_mobil'],
            'no_plat' => $validatedData['no_plat'],
            'catatan' => $validatedData['catatan'],
        ]);

        // Hapus barang lama yang terkait dengan SPK
        $spk->items()->delete();

        // Tambahkan barang baru
        foreach ($validatedData['nama_barang'] as $index => $nama_barang) {
            SpkItem::create([
                'spk_id' => $spk->id,
                'nama_barang' => $nama_barang,
                'qty' => $validatedData['qty'][$index],
            ]);
        }

        // Set session flash message popup
        session()->flash('message', 'SPK Berhasil di Edit 👌');

        // Redirect ke halaman detail dengan pesan sukses
        return redirect()->route('mekanik.spk.show', ['id' => $spk->id])->with('success', 'SPK berhasil diperbarui.');
    }





    // public function index()
    // {
    //     $spks = Spk::where('status', 'baru diterbitkan')->get();
    //     return view('mekanik.spk.index', compact('spks'));
    // }

    // controller untuk tombol cancel (detail SPK)
    public function cancel(Request $request, $id)
    {
        // Validasi input alasan
        $validatedData = $request->validate([
            'reason' => 'required|string|max:1000', // Alasan wajib diisi
        ]);

        // Ambil data SPK berdasarkan ID
        $spk = Spk::findOrFail($id);

        // Periksa apakah status SPK dapat dibatalkan
        if ($spk->status !== 'Baru Diterbitkan') {
            return redirect()->back()->with('error', 'SPK tidak dapat dibatalkan karena statusnya sudah berubah!');
        }

        // Perbarui status dan simpan alasan pembatalan
        $spk->update([
            'status' => 'Cancel', // Perbaiki ejaan status agar seragam dan konsisten
            'cancel_reason' => $validatedData['reason'], // Simpan alasan pembatalan
        ]);

        // Set session flash message popup
        session()->flash('message', 'SPK Berhasil di Cancel 🧐');

        // Redirect ke halaman show dengan pesan sukses
        return redirect()->route('mekanik.spk.show', ['id' => $spk->id])
            ->with('success', 'SPK berhasil dibatalkan dengan alasan: ' . $validatedData['reason']);
    }
}
