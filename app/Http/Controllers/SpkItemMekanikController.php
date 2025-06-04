<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Spk;
use App\Models\User;
use App\Models\SpkItem;

class SpkItemMekanikController extends Controller
{
    // Tampilkan form pilih mekanik untuk setiap item SPK
    public function form($spk_id)
    {
        $spk = Spk::with('items')->findOrFail($spk_id);
        $mekaniks = User::where('level', 'mekanik')->get();
        return view('mekanik.spk.pilih_mekanik', compact('spk', 'mekaniks'));
    }

    // Simpan mekanik yang dipilih untuk setiap item SPK
    public function assign(Request $request, $spk_id)
    {
        $mekanikData = $request->input('mekanik', []);
        foreach ($mekanikData as $item_id => $mekanik_id) {
            $item = SpkItem::where('spk_id', $spk_id)->where('id', $item_id)->first();
            if ($item) {
                $item->mekanik_id = $mekanik_id;
                $item->save();
            }
        }
        return redirect()->route('mekanik.spk.show', $spk_id)->with('success', 'Mekanik berhasil disimpan!');
    }
}
