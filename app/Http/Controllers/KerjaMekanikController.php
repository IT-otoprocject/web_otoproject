<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\SpkItem;
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

    // Tambahkan method berikut:
    public function setWaktuPengerjaanBarang(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:spk_items,id',
            'waktu_pengerjaan_barang' => 'required'
        ]);
        $item = SpkItem::findOrFail($request->item_id);

        // Ambil semua item dengan spk_id yang sama dan urutkan berdasarkan waktu_pengerjaan_barang (null di akhir)
        $allItems = SpkItem::where('spk_id', $item->spk_id)
            ->orderBy('id')
            ->get();

        // Cari urutan item ini di antara item lain dengan spk_id yang sama
        $currentIndex = $allItems->search(function($i) use ($item) {
            return $i->id === $item->id;
        });

        // Default waktu_sebelumnya
        $waktu_sebelumnya = '00:00:00';
        if ($currentIndex > 0) {
            // Cari item sebelumnya yang sudah punya waktu_pengerjaan_barang
            for ($i = $currentIndex - 1; $i >= 0; $i--) {
                if ($allItems[$i]->waktu_pengerjaan_barang) {
                    $waktu_sebelumnya = $allItems[$i]->waktu_pengerjaan_barang;
                    break;
                }
            }
        }

        // Hitung selisih waktu (format HH:mm:ss)
        $waktu_pengerjaan = $request->waktu_pengerjaan_barang;
        $selisih_waktu = $this->hitungSelisihWaktu($waktu_sebelumnya, $waktu_pengerjaan);

        $item->waktu_sebelumnya = $waktu_sebelumnya;
        $item->waktu_pengerjaan_barang = $waktu_pengerjaan;
        $item->selisih_waktu = $selisih_waktu;
        $item->save();

        return response()->json(['success' => true, 'message' => 'Waktu pengerjaan barang berhasil disimpan']);
    }

    // Helper untuk menghitung selisih waktu format HH:mm:ss
    private function hitungSelisihWaktu($start, $end)
    {
        try {
            $startArr = explode(':', $start);
            $endArr = explode(':', $end);
            $startSeconds = ($startArr[0] * 3600) + ($startArr[1] * 60) + ($startArr[2] ?? 0);
            $endSeconds = ($endArr[0] * 3600) + ($endArr[1] * 60) + ($endArr[2] ?? 0);
            $diff = max(0, $endSeconds - $startSeconds);
            $h = str_pad(floor($diff / 3600), 2, '0', STR_PAD_LEFT);
            $m = str_pad(floor(($diff % 3600) / 60), 2, '0', STR_PAD_LEFT);
            $s = str_pad($diff % 60, 2, '0', STR_PAD_LEFT);
            return "$h:$m:$s";
        } catch (\Exception $e) {
            return '00:00:00';
        }
    }
}
