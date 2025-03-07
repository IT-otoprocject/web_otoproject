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
    public function index()
    {
        // Mengambil semua data SPK dari database
        $spks = Spk::all();

        // Debugging: Periksa apakah $spks adalah array atau objek
        //  dd($spks);
        // dd($spks->toArray());


        // Pastikan $spks adalah array atau objek
        if ($spks->isEmpty()) {
            // Jika kosong, Anda bisa mengatur pesan atau tindakan lain
            return view('spk.index', ['spks' => []]);
        }

        return view('spk.index', ['spks' => $spks]);
    }
    // public function index()
    // {
    //     $spks = Spk::where('status', 'baru diterbitkan')->get();
    //     return view('mekanik.spk.index', compact('spks'));
    // }
}
