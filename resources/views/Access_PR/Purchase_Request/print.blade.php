<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Request #{{ $purchaseRequest->pr_number }}</title>
    <style>
        @page { margin: 28px 28px 50px 28px; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; color: #111; }
        .h-title { font-size: 18px; font-weight: bold; letter-spacing: .3px; }
        .muted { color:#6b7280; }
        .badge { display:inline-block; padding: 2px 6px; border-radius: 3px; color:#fff; font-size: 10px; line-height: 1.4; }
        .status-DRAFT{ background:#6b7280;} .status-SUBMITTED{background:#f59e0b;} .status-APPROVED{background:#10b981;} .status-REJECTED{background:#ef4444;} .status-COMPLETED{background:#3b82f6;}

        .section { margin-top: 16px; }
        .section-title { font-size: 13px; font-weight: 700; margin: 0 0 8px; text-transform: uppercase; border-bottom:1px solid #e5e7eb; padding-bottom:6px; }

        table { width:100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #e5e7eb; padding: 6px 8px; vertical-align: top; word-wrap: break-word; }
    th { background:#f8fafc; text-align:center; font-size: 11px; }
        tfoot td { font-weight:bold; background:#f8fafc; }
        .no-border td, .no-border th { border: none; }
        .tight td { padding: 4px 6px; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .w-5 { width: 40px; }
        .w-8 { width: 70px; }
        .w-10 { width: 90px; }
        .w-14 { width: 120px; }
        .w-16 { width: 140px; }
        .zebra tbody tr:nth-child(odd) { background:#fcfcfd; }
        tr { page-break-inside: avoid; }

        /* Header & Footer */
        .doc-head { margin-bottom: 10px; }
        .doc-meta-table { width:100%; border-collapse: collapse; }
        .doc-meta-table td { border: none; padding: 0; }
        .doc-meta-left { font-size: 12px; }
        .doc-meta-right { text-align: right; font-size: 12px; }

        .signature-table { width:100%; border-collapse: collapse; margin-top: 18px; }
        .signature-table th, .signature-table td { border: 1px solid #e5e7eb; padding: 12px 8px; text-align: center; }
        .signature-placeholder { height: 60px; }

    </style>
</head>
<body>
    <div class="doc-head">
        <table class="doc-meta-table">
            <tr>
                <td class="doc-meta-left">
                    <div class="h-title">Purchase Request</div>
                    <div class="muted">{{ config('app.name') }}</div>
                </td>
                <td class="doc-meta-right">
                    <div><strong>No. PR:</strong> {{ $purchaseRequest->pr_number }}</div>
                    <div class="muted">Tanggal: {{ optional($purchaseRequest->request_date)->format('d/m/Y') }}</div>
                    <div style="margin-top:4px">
                        <span class="badge status-{{ $purchaseRequest->status }}">{{ $purchaseRequest->status }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Informasi Pemohon -->
    <div class="section">
        <div class="section-title">Informasi Pemohon</div>
        <table class="tight">
            <colgroup>
                <col style="width: 22%">
                <col style="width: 28%">
                <col style="width: 22%">
                <col style="width: 28%">
            </colgroup>
            <tbody>
                <tr>
                    <td class="muted">Nomor PR</td>
                    <td><strong>{{ $purchaseRequest->pr_number }}</strong></td>
                    <td class="muted">Nama</td>
                    <td><strong>{{ $purchaseRequest->user->name }}</strong></td>
                </tr>
                <tr>
                    <td class="muted">Tanggal Request</td>
                    <td>{{ optional($purchaseRequest->request_date)->format('d/m/Y') }}</td>
                    <td class="muted">Email</td>
                    <td>{{ $purchaseRequest->user->email }}</td>
                </tr>
                <tr>
                    <td class="muted">Divisi</td>
                    <td>{{ $purchaseRequest->user->divisi ?? 'N/A' }}</td>
                    <td class="muted">Level</td>
                    <td>{{ ucfirst($purchaseRequest->user->level) }}</td>
                </tr>
                <tr>
                    <td class="muted">Lokasi</td>
                    <td>{{ $purchaseRequest->location->name ?? 'N/A' }}</td>
                    <td class="muted">Jatuh Tempo</td>
                    <td>{{ $purchaseRequest->due_date ? optional($purchaseRequest->due_date)->format('d/m/Y') : '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Status Approval -->
    <div class="section">
        <div class="section-title">Status Approval</div>
        <table class="zebra">
            <thead>
                <tr>
                    <th style="width: 28%">Level</th>
                    <th style="width: 18%">Status</th>
                    <th style="width: 22%">Tanggal</th>
                    <th style="width: 32%">Catatan / Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach(($purchaseRequest->approval_flow ?? []) as $level)
                    @php
                        $status = $approvalStatus[$level] ?? ['approved' => null, 'status_text' => 'Menunggu persetujuan'];
                        $levelName = match($level) {
                            'dept_head' => 'Department Head',
                            'manager' => 'Manager',
                            'spv' => 'SPV',
                            'headstore' => 'Head Store',
                            'ga' => 'GA',
                            'finance_dept' => 'Finance Department',
                            'ceo' => 'CEO',
                            'cfo' => 'CFO',
                            default => ucfirst(str_replace('_', ' ', $level))
                        };
                    @endphp
                    <tr>
                        <td>{{ $levelName }}</td>
                        <td>
                            @if($status['approved'] === true)
                                Disetujui
                            @elseif($status['approved'] === false)
                                Ditolak
                            @else
                                Menunggu
                            @endif
                        </td>
                        <td>{{ $status['formatted_date'] ?? '-' }}</td>
                        <td>
                            @if(!empty($status['notes']))
                                Catatan: {{ $status['notes'] }}
                                @if(!empty($status['approved_by_name']))<br>@endif
                            @endif
                            @if(!empty($status['approved_by_name']))
                                Oleh: {{ $status['approved_by_name'] }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Items -->
    <div class="section">
        <div class="section-title">Item</div>
        <table class="zebra">
        <thead>
                <tr>
            <th class="w-5 text-center">no</th>
            <th class="text-center">Nama Barang</th>
            <th class="w-8 text-center">Qty</th>
            <th class="w-10 text-center">Satuan</th>
            <th class="w-14 text-center">Harga Est. Satuan</th>
            <th class="w-16 text-center">Total Harga Barang</th>
                </tr>
            </thead>
            <tbody>
                @foreach($purchaseRequest->items as $index => $item)
                    @php
                        $qty = $item->quantity ?? 0;
                        $price = $item->estimated_price ?? 0;
                        $itemTotal = $qty * $price;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ number_format($qty) }}</td>
                        <td class="text-center">{{ $item->unit ?? '-' }}</td>
                        <td class="text-right">@if($price) Rp {{ number_format($price, 0, ',', '.') }} @else - @endif</td>
                        <td class="text-right">@if($price) Rp {{ number_format($itemTotal, 0, ',', '.') }} @else - @endif</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-right">Total Estimasi</td>
                    <td class="text-right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Deskripsi & Catatan (dipindah ke bawah Item) -->
    <div class="section">
        <div class="section-title">Deskripsi & Catatan</div>
        <table class="tight">
            <tbody>
                <tr>
                    <td style="width: 22%" class="muted">Keterangan</td>
                    <td style="width: 78%">{{ $purchaseRequest->description }}</td>
                </tr>
                @if($purchaseRequest->notes)
                <tr>
                    <td class="muted">Catatan</td>
                    <td>{{ $purchaseRequest->notes }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} / {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "normal");
            $size = 9;
            $y = $pdf->get_height() - 28;
            // right aligned page text
            $w = $fontMetrics->get_text_width($text, $font, $size);
            $pdf->page_text($pdf->get_width() - $w - 28, $y, $text, $font, $size, array(0,0,0));

            $printed = "Dicetak: " . date('d/m/Y H:i');
            $pdf->page_text(28, $y, $printed, $font, $size, array(0,0,0));
        }
    </script>
</body>
</html>
