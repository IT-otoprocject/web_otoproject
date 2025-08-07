<?php

namespace App\Exports;

use App\Models\SpkItem;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class SpkAvgMekanik implements FromCollection, WithHeadings
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
        $query = SpkItem::whereNotNull('selisih_waktu')
            ->whereNotNull('mekanik_id');
        if ($this->tanggalMulai && $this->tanggalAkhir) {
            $query->whereHas('spk', function($q) {
                $q->whereBetween('tanggal', [$this->tanggalMulai, $this->tanggalAkhir]);
            });
        }
        $items = $query->select('mekanik_id', 'selisih_waktu', 'qty')->get();

        $grouped = $items->groupBy('mekanik_id');
        $rows = new Collection();
        foreach ($grouped as $mekanik_id => $group) {
            $mekanik = User::find($mekanik_id);
            $mekanik_name = $mekanik ? $mekanik->name : '-';
            $total_qty = $group->sum('qty');
            $total_seconds = 0;
            foreach ($group as $item) {
                $total_seconds += $this->durationToSeconds($item->selisih_waktu);
            }
            $avg_per_item = $total_qty > 0 ? (int)($total_seconds / $total_qty) : 0;
            $avg_formatted = $this->secondsToDuration($avg_per_item);
            $rows->push([
                $mekanik_name,
                $total_qty,
                $avg_formatted
            ]);
        }
        return $rows;
    }

    private function durationToSeconds($duration)
    {
        $parts = explode(':', $duration);
        if (count($parts) !== 3) return 0;
        return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
    }

    private function secondsToDuration($seconds)
    {
        $h = floor($seconds / 3600);
        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return sprintf('%02d:%02d:%02d', $h, $m, $s);
    }

    public function headings(): array
    {
        return [
            'Nama Mekanik',
            'Jumlah Barang',
            'Rata-rata Waktu',
        ];
    }
}
