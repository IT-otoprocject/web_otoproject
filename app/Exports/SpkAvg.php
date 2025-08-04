<?php

namespace App\Exports;

use App\Models\SpkItem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SpkAvg implements FromCollection, WithHeadings
{
    protected $tanggalMulai;
    protected $tanggalAkhir;

    public function __construct($tanggalMulai = null, $tanggalAkhir = null)
    {
        $this->tanggalMulai = $tanggalMulai;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        // Ambil semua item, group by SKU dan nama_barang
        $query = SpkItem::whereNotNull('selisih_waktu')
            ->whereNotNull('sku')
            ->where('sku', '!=', '');
        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereHas('spk', function($q) {
                $q->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalAkhir]);
            });
        }
        $items = $query->select('sku', 'nama_barang', 'selisih_waktu', 'qty')->get();

        $grouped = $items->groupBy(function($item) {
            return $item->sku . '|' . $item->nama_barang;
        });

        $rows = new Collection();
        foreach ($grouped as $key => $group) {
            $sku = $group->first()->sku;
            $nama_barang = $group->first()->nama_barang;
            // Bagi selisih_waktu dengan qty sebelum di-average
            $perItemDurations = $group->map(function($item) {
                return $this->divideDuration($item->selisih_waktu, $item->qty);
            })->toArray();
            $avg = $this->averageDuration($perItemDurations);
            $rows->push([
                $sku,
                $nama_barang,
                $avg
            ]);
        }
        return $rows;
    }

    // Helper membagi durasi (HH:mm:ss) dengan qty
    private function divideDuration($duration, $qty)
    {
        if (!$duration || !$qty || $qty < 1) return '00:00:00';
        $parts = explode(':', $duration);
        if (count($parts) !== 3) return '00:00:00';
        $totalSeconds = ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
        $perItem = (int)($totalSeconds / $qty);
        $h = floor($perItem / 3600);
        $m = floor(($perItem % 3600) / 60);
        $s = $perItem % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    private function averageDuration($durations)
    {
        $totalSeconds = 0;
        $count = 0;
        foreach ($durations as $d) {
            $parts = explode(':', $d);
            if (count($parts) === 3) {
                $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                $count++;
            }
        }
        if ($count === 0) return '00:00:00';
        $avg = (int)($totalSeconds / $count);
        $h = floor($avg / 3600);
        $m = floor(($avg % 3600) / 60);
        $s = $avg % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Nama Barang',
            'Rata-rata Pengerjaan',
        ];
    }
}
