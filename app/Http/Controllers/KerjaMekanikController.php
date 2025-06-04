<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KerjaMekanikController extends Controller
{

    public function waktu_mulai_kerja($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);

        if (!$spk->waktu_mulai_kerja) {
            $spk->waktu_mulai_kerja = now();
            $saved = $spk->save();

            if ($saved) {
                logger("✅ Waktu Mulai Kerja berhasil disimpan: " . $spk->waktu_mulai_kerja);
            } else {
                logger("❌ Gagal menyimpan Waktu Mulai Kerja untuk SPK ID: " . $spk_id);
            }
        } else {
            logger("⏳ Waktu Mulai Kerja sudah ada: " . $spk->waktu_mulai_kerja);
        }

        return redirect()->route('kerja.mekanik', $spk_id);
    }
    

    public function show($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);

        // Ubah status menjadi "Dalam Proses"
        $spk->status = "Dalam Proses";
        $spk->save(); // Simpan perubahan

        // Flash message untuk notifikasi
        session()->flash('message', 'Pekerjaan Dimulai! Status telah diperbarui menjadi Dalam Proses 🔥');

        // Tampilkan halaman kerja mekanik
        return view('mekanik.spk.kerja_mekanik', compact('spk'));
    }


    // public function show($spk_id)
    // {
    //     $spk = Spk::findOrFail($spk_id);

    //     // Simpan waktu mulai kerja dalam waktu lokal
    //     $spk->waktu_mulai_kerja = Carbon::now('Asia/Jakarta');

    //     // Ubah status menjadi "Dalam Proses"
    //     $spk->status = "Dalam Proses";

    //     // Simpan perubahan ke database
    //     $spk->save();



    //     session()->flash('message', 'Pekerjaan Dimulai! Status telah diperbarui menjadi Dalam Proses 🔥');
    //     return view('mekanik.spk.kerja_mekanik', compact('spk'));
    // }


    public function selesai(Request $request, $spk_id)
    {
        $spk = Spk::findOrFail($spk_id);
        $spk->status = 'Sudah Selesai';
        $spk->waktu_kerja = $request->input('worked_time');
        $spk->catatan_kerja = $request->input('notes');
        $spk->teknisi_selesai = Auth::user()->name;
        $spk->save();

        // Set session flash message
        session()->flash('message', 'Pekerjaan Telah Direcord, Kerja Bagus 🕺');

        return redirect()->route('spk.items.pilihMekanik', ['spk' => $spk_id])->with('success');
    }
}
