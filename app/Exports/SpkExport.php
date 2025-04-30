<?php

namespace App\Exports;

use App\Models\Spk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class SpkExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    private function calculateAverageDuration($spks)
    {
        $totalSeconds = 0;
        $count = 0;

        foreach ($spks as $spk) {
            if ($spk->waktu_terbit_spk && $spk->waktu_mulai_kerja) {
                $waktuTerbit = Carbon::parse($spk->waktu_terbit_spk);
                $waktuMulai = Carbon::parse($spk->waktu_mulai_kerja);
                $totalSeconds += abs($waktuMulai->diffInSeconds($waktuTerbit));
                $count++;
            }
        }

        return $count > 0 ? gmdate('H:i:s', (int)($totalSeconds / $count)) : '00:00:00';
    }

    private function calculateAverageWorkTime($spks)
    {
        $totalSeconds = 0;
        $count = 0;

        foreach ($spks as $spk) {
            if ($spk->waktu_kerja) {
                $parts = explode(':', $spk->waktu_kerja);
                if (count($parts) === 3) {
                    // Format HH:mm:ss
                    $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                } elseif (count($parts) === 2) {
                    // Format HH:mm
                    $totalSeconds += ($parts[0] * 3600) + ($parts[1] * 60);
                }
                $count++;
            }
        }

        if ($count === 0) {
            return '00:00:00';
        }

        $averageSeconds = (int)($totalSeconds / $count);
        $hours = floor($averageSeconds / 3600);
        $minutes = floor(($averageSeconds % 3600) / 60);
        $seconds = $averageSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function collection()
    {
        $query = Spk::with('items');

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('tanggal', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['garage'])) {
            $query->where('garage', $this->filters['garage']);
        }

        $spks = $query->get();
        $rows = new Collection();

        foreach ($spks as $spk) {
            // Get the first item if exists
            $firstItem = $spk->items->first();
            
            $rows->push([
                $spk->no_spk,
                $spk->garage,
                $spk->tanggal,
                $spk->teknisi_1,
                $spk->teknisi_2,
                $spk->customer,
                $spk->alamat,
                $spk->no_hp,
                $spk->jenis_mobil,
                $spk->no_plat,
                $spk->catatan,
                $spk->waktu_terbit_spk,
                $spk->waktu_mulai_kerja,
                $spk->durasi,
                $spk->status,
                $spk->waktu_kerja,
                $spk->catatan_kerja,
                $spk->teknisi_selesai,
                $firstItem ? $firstItem->nama_barang : '',
                $firstItem ? $firstItem->qty : ''
            ]);

            // Add remaining items starting from the second item
            foreach ($spk->items->slice(1) as $item) {
                $rows->push([
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                    $item->nama_barang,
                    $item->qty
                ]);
            }
        }

        // Add empty row as separator
        $rows->push(array_fill(0, 20, ''));

        // Add summary rows
        $rows->push([
            'RINGKASAN:', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        ]);

        $rows->push([
            'Rata-rata Durasi Tunggu:', $this->calculateAverageDuration($spks),
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        ]);

        $rows->push([
            'Rata-rata Waktu Kerja:', $this->calculateAverageWorkTime($spks),
            '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''
        ]);

        return $rows;
    }

    public function headings(): array
    {
        return [
            'No SPK',
            'Garage',
            'Tanggal',
            'Teknisi 1',
            'Teknisi 2',
            'Customer',
            'Alamat',
            'No. HP',
            'Jenis Mobil',
            'No Plat',
            'Catatan',
            'Waktu Terbit',
            'Waktu Mulai Kerja',
            'Durasi',
            'Status',
            'Waktu Kerja',
            'Catatan Kerja',
            'Teknisi Selesai',
            'Nama Barang',
            'Qty'
        ];
    }
}
