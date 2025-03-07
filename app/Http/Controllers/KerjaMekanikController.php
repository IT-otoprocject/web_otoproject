<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use Illuminate\Support\Facades\Auth;

class KerjaMekanikController extends Controller
{
    public function show($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);

        // Mengubah status menjadi "Dalam Pengerjaan"
        $spk->status = "Dalam Pengerjaan";
        $spk->save(); // Simpan perubahan ke database

        return view('mekanik.spk.kerja_mekanik', compact('spk'));
    }


    public function selesai(Request $request, $spk_id)
    {
        $spk = Spk::findOrFail($spk_id);
        $spk->status = 'Sudah Selesai';
        $spk->waktu_kerja = $request->input('worked_time');
        $spk->catatan_kerja = $request->input('notes');
        $spk->teknisi_selesai = Auth::user()->name;
        $spk->save();

        return redirect()->route('spk.index', ['spk' => $spk_id])->with('success');
    }
}
