<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;

class SpkController extends Controller
{
    public function create()
    {
        return view('spk.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'no_spk' => 'required|string|unique:spks',
            'tanggal' => 'required|date',
            'no_so' => 'required|string',
            'teknisi_1' => 'required|string',
            'teknisi_2' => 'nullable|string',
            'customer' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_mobil' => 'required|string',
            'no_plat' => 'required|string',
            'nama_barang' => 'required|string',
            'qty' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        $spk = Spk::create($validatedData);

        // Redirect ke halaman untuk mekanik
        return redirect()->route('mekanik.spk.show', $spk->id)->with('success', 'SPK berhasil diterbitkan.');
    }

    // Menampilkan SPK untuk Mekanik
    // public function show(Spk $spk)
    // {
    //     return view('mekanik.spk.show', compact('spk'));
    // }

    
    

    // menampilkan daftar SPK yang baru diterbitkan untuk mekanik.
    public function index()
    {
        $spks = Spk::all(); // Ambil semua data SPK
        return view('spk.index', compact('spks'));
    }
    // public function index()
    // {
    //     $spks = Spk::where('status', 'baru diterbitkan')->get();
    //     return view('mekanik.spk.index', compact('spks'));
    // }
}
