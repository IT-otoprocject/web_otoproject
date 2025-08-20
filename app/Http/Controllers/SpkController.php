<?php

namespace App\Http\Controllers;

use App\Models\Spk;
use Illuminate\Http\Request;
use App\Models\SpkItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

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
            'customer' => 'required|string',
            'alamat' => 'required|string',
            'no_hp' => 'required|string',
            'jenis_mobil' => 'required|string',
            'no_plat' => 'required|string',
            'nama_barang' => 'required|array',
            'nama_barang.*' => 'required|string',
            'qty' => 'required|array',
            'qty.*' => 'required|integer',
            'sku' => 'nullable|array',
            'sku.*' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);

        // Validasi backend: nama_barang tidak boleh duplikat (case-insensitive)
        $lowered = array_map(fn($v) => mb_strtolower(trim($v)), $validatedData['nama_barang']);
        if (count($lowered) !== count(array_unique($lowered))) {
            return back()->withInput()->withErrors(['nama_barang' => 'Terdapat nama product yang sama, silakan hapus duplikat dan ubah Qty jika dibutuhkan.']);
        }

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
                'sku' => $validatedData['sku'][$index] ?? null,
            ]);
        }

        // Set session flash message popup
        session()->flash('message', 'SPK telah dibuat segera Infokan ke Mekanik 😁');

        // Redirect ke halaman untuk mekanik
        return redirect()->route('mekanik.spk.show', $spk->id)->with('success', 'SPK berhasil diterbitkan.');
    }

        /**
     * Menampilkan daftar SPK untuk hari ini saja (daily),
     * jika user punya garage, hanya tampilkan SPK untuk garage tersebut.
     */
    public function daily()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $query = Spk::whereDate('created_at', $today)
            ->whereIn('status', ['Baru Diterbitkan', 'Dalam Proses']);
        if ($user && $user->garage) {
            $query->where('garage', $user->garage);
        }
        $spks = $query->get();
        return view('spk.daily_spk', compact('spks'));
    }

    // Menampilkan SPK untuk Mekanik
    public function show($id)
    {
        $spk = Spk::findOrFail($id);

        $barangLama = $spk->items()->where('is_new', false)->get();
        $barangBaru = $spk->items()->where('is_new', true)->get();

        return view('mekanik.spk.show', compact('spk', 'barangLama', 'barangBaru'));
    }
    // public function show(Spk $spk)
    // {
    //     return view('mekanik.spk.show', compact('spk'));
    // }




    // menampilkan daftar SPK yang baru diterbitkan untuk mekanik.
    public function index(Request $request)
    {
        $query = Spk::query();
        $userGarage = \Illuminate\Support\Facades\Auth::user()->garage ?? null;
        // Filter otomatis berdasarkan garage user jika ada
        if ($userGarage) {
            $query->where('garage', $userGarage);
        }
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
        $query->orderBy('tanggal', 'desc')
            ->orderByRaw("
        CASE
            WHEN status = 'Baru Diterbitkan' THEN 1
            WHEN status = 'Dalam Pengerjaan' THEN 2
            WHEN status = 'Cancel' THEN 3
            WHEN status = 'Sudah Selesai' THEN 4
            ELSE 5
        END ASC
    ");
        $spks = $query->paginate(10);
        return view('spk.index', compact('spks'));
    }

    // edit SPK 
    public function edit($spk_id)
    {
        // Ambil data SPK berdasarkan ID
        $spk = Spk::findOrFail($spk_id);

        if ($spk->status !== 'Dalam Proses') {
            return redirect()->back()->with('error', 'SPK hanya dapat diedit jika statusnya "Dalam Proses".');
        }

        // Kirim data ke view edit
        return view('spk.edit', compact('spk'));
    }

    public function update(Request $request, $spk_id)
    {
        // Validasi input dan simpan hasil ke variabel $validatedData
        $validatedData = $request->validate([
            'garage' => 'required|string|max:255',
            'tanggal' => 'required|date',
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
                'is_new' => true, // Tandai sebagai barang baru
            ]);
        }

        // Set session flash message popup
        session()->flash('message', 'SPK Berhasil di Edit 👌');

        // Redirect ke halaman detail dengan pesan sukses
        return redirect()->route('mekanik.spk.show', ['id' => $spk->id])->with('success', 'SPK berhasil diperbarui.');
    }

    public function editBarang($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);

        if ($spk->status !== 'Dalam Proses') {
            return redirect()->back()->with('error', 'SPK hanya dapat diedit jika statusnya "Dalam Proses".');
        }

        $barangLama = $spk->items()->where('is_new', false)->get();
        $barangBaru = $spk->items()->where('is_new', true)->get();

        return view('mekanik.spk.edit-barang', compact('spk', 'barangLama', 'barangBaru'));
    }

    public function updateBarang(Request $request, $spk_id)
    {
        $validatedData = $request->validate([
            'nama_barang' => 'required|array',
            'nama_barang.*' => 'required|string|max:255',
            'qty' => 'required|array',
            'qty.*' => 'required|integer|min:1',
            'sku' => 'nullable|array',
            'sku.*' => 'nullable|string',
            'waktu_pengerjaan_barang' => 'nullable|array',
            'waktu_pengerjaan_barang.*' => 'nullable|string',
        ]);

        // Validasi backend: nama_barang tidak boleh duplikat (case-insensitive)
        $lowered = array_map(fn($v) => mb_strtolower(trim($v)), $validatedData['nama_barang']);
        if (count($lowered) !== count(array_unique($lowered))) {
            return back()->withInput()->withErrors(['nama_barang' => 'Terdapat nama product yang sama, silakan hapus duplikat dan ubah Qty jika dibutuhkan.']);
        }

        $spk = Spk::findOrFail($spk_id);
        if ($spk->status !== 'Dalam Proses') {
            return redirect()->back()->with('error', 'SPK hanya dapat diedit jika statusnya "Dalam Proses".');
        }

        $existingItems = $spk->items()->get();
        $submittedItems = [];
        foreach ($validatedData['nama_barang'] as $index => $nama_barang) {
            $sku = $validatedData['sku'][$index] ?? null;
            $qty = $validatedData['qty'][$index];
            $waktu_pengerjaan_barang = $validatedData['waktu_pengerjaan_barang'][$index] ?? null;
            $existing = $existingItems->first(function($item) use ($nama_barang) {
                return $item->nama_barang === $nama_barang;
            });
            if ($existing) {
                if ($existing->sku === $sku) {
                    // SKU sama, update qty dan sku, waktu_pengerjaan_barang tetap
                    $existing->update([
                        'qty' => $qty,
                        'sku' => $sku,
                        'is_new' => true,
                        'waktu_pengerjaan_barang' => $waktu_pengerjaan_barang,
                    ]);
                } else {
                    // SKU berubah, update semua dan waktu_pengerjaan_barang diisi sekarang
                    $existing->update([
                        'qty' => $qty,
                        'sku' => $sku,
                        'is_new' => true,
                        'waktu_pengerjaan_barang' => now(),
                    ]);
                }
                $submittedItems[] = $existing->id;
            } else {
                // Barang baru, waktu pengerjaan barang dikosongkan
                $newItem = SpkItem::create([
                    'spk_id' => $spk->id,
                    'nama_barang' => $nama_barang,
                    'qty' => $qty,
                    'sku' => $sku,
                    'is_new' => true,
                    'waktu_pengerjaan_barang' => null,
                ]);
                $submittedItems[] = $newItem->id;
            }
        }
        $toDelete = $existingItems->whereNotIn('id', $submittedItems);
        foreach ($toDelete as $item) {
            $item->delete();
        }
        session()->flash('message', 'Barang berhasil diperbarui.');
        return redirect()->route('mekanik.spk.show', $spk->id)->with('success', 'Barang berhasil diperbarui.');
    }

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

    public function destroyBarang($id)
    {
        $barang = SpkItem::findOrFail($id);

        if ($barang->is_new) {
            $barang->delete();
            return response()->json(['message' => 'Barang berhasil dihapus.'], 200);
        }

        return response()->json(['message' => 'Barang lama tidak dapat dihapus.'], 403);
    }
    
    // Untuk kebutuhan AJAX reload produk di kerja_mekanik.blade.php
    public function itemsJson($spk_id)
    {
        $spk = Spk::findOrFail($spk_id);
        $barangLama = $spk->items()->where('is_new', false)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'qty' => $item->qty,
                'waktu_pengerjaan_barang' => $item->waktu_pengerjaan_barang,
            ];
        });
        $barangBaru = $spk->items()->where('is_new', true)->get()->map(function($item) {
            return [
                'id' => $item->id,
                'nama_barang' => $item->nama_barang,
                'qty' => $item->qty,
                'waktu_pengerjaan_barang' => $item->waktu_pengerjaan_barang,
            ];
        });
        return response()->json([
            'barangLama' => $barangLama,
            'barangBaru' => $barangBaru,
        ]);
    }
}
