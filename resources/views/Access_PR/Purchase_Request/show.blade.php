<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Purchase Request') }} #{{ $purchaseRequest->pr_number }}
            @php
            $statusClass = match($purchaseRequest->status) {
            'DRAFT' => 'bg-gray-500',
            'SUBMITTED' => 'bg-yellow-500',
            'APPROVED' => 'bg-green-500',
            'REJECTED' => 'bg-red-500',
            'COMPLETED' => 'bg-blue-500',
            default => 'bg-gray-500'
            };
            @endphp
            <span class="inline-block px-3 py-1 text-white text-sm rounded ml-3 {{ $statusClass }}">{{ $purchaseRequest->status }}</span>
        </h2>
    </x-slot>

    <!-- Popup Notification -->
    @if (session('success'))
    <div id="notifPopup" class="notif-popup">
        <p>{{ session('success') }}</p>
    </div>
    @endif

    @if (session('error'))
    <div id="notifPopup" class="notif-popup bg-red-500">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Informasi Umum -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Info Pemohon -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                                Informasi Pemohon
                            </h3>
                            <div class="space-y-3">
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Nomor PR</span>
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->pr_number }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Tanggal Request</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->request_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Nama</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->user->name }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Email</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->user->email }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Divisi</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->user->divisi ?? 'N/A' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Level</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ ucfirst($purchaseRequest->user->level) }}</span>
                                </div>
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Lokasi</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->location->name ?? 'N/A' }}</span>
                                </div>
                                @if($purchaseRequest->due_date)
                                <div class="flex">
                                    <span class="w-32 text-sm font-medium text-gray-600 dark:text-gray-400">Jatuh Tempo</span>
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->due_date->format('d/m/Y') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Approval -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-tasks mr-2 text-green-500"></i>
                                Status Approval
                            </h3>
                            <div class="space-y-3">
                                @foreach($purchaseRequest->approval_flow as $level)
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
                                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $levelName }}</span>
                                        @if($status['approved'] === true)
                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            {{ $status['status_text'] ?? 'Approved' }}
                                        </div>
                                        @php
                                        $approvals = $purchaseRequest->approvals ?? [];
                                        $approvalData = $approvals[$level] ?? null;
                                        @endphp
                                        @if($approvalData)
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            Oleh: {{ $approvalData['approved_by_name'] ?? 'N/A' }}
                                            @if(isset($approvalData['approved_by_divisi']) && isset($approvalData['approved_by_level']))
                                            ({{ $approvalData['approved_by_divisi'] }} - {{ ucfirst($approvalData['approved_by_level']) }})
                                            @endif
                                        </div>
                                        @if($level === 'finance_dept' && isset($approvalData['fat_department']))
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            <span class="font-semibold">Perusahaan:</span> {{ $approvalData['fat_department'] }}
                                        </div>
                                        <div class="text-xs text-blue-600 dark:text-blue-400">
                                            <span class="font-semibold">Jenis:</span> 
                                            @if($approvalData['fat_approval_type'] === 'asset')
                                            <span class="bg-green-100 text-green-800 px-1 rounded">Asset</span>
                                            @else
                                            <span class="bg-blue-100 text-blue-800 px-1 rounded">Cost</span>
                                            @endif
                                        </div>
                                        @if($approvalData['fat_approval_type'] === 'asset' && $purchaseRequest->asset_number)
                                        <div class="text-xs text-green-600 dark:text-green-400 mt-1">
                                            <span class="font-semibold">Asset Number:</span> 
                                            <span class="bg-green-100 text-green-800 px-1 rounded font-mono">{{ $purchaseRequest->asset_number }}</span>
                                        </div>
                                        @endif
                                        @endif
                                        @endif
                                        @if(isset($status['notes']) && $status['notes'])
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Catatan: {{ $status['notes'] }}
                                        </div>
                                        @endif
                                        @elseif($status['approved'] === false)
                                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                                            {{ $status['status_text'] ?? 'Rejected' }}
                                        </div>
                                        @php
                                        $approvals = $purchaseRequest->approvals ?? [];
                                        $approvalData = $approvals[$level] ?? null;
                                        @endphp
                                        @if($approvalData)
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                            Oleh: {{ $approvalData['approved_by_name'] ?? 'N/A' }}
                                            @if(isset($approvalData['approved_by_divisi']) && isset($approvalData['approved_by_level']))
                                            ({{ $approvalData['approved_by_divisi'] }} - {{ ucfirst($approvalData['approved_by_level']) }})
                                            @endif
                                        </div>
                                        @endif
                                        @if(isset($status['notes']) && $status['notes'])
                                        <div class="text-xs text-red-500 dark:text-red-400 mt-1">
                                            Alasan: {{ $status['notes'] }}
                                        </div>
                                        @endif
                                        @else
                                        <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $status['status_text'] ?? 'Menunggu persetujuan' }}
                                        </div>
                                        @endif
                                    </div>
                                    @if($status['approved'] === true)
                                    <div class="flex items-center">
                                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                        <span class="text-xs text-green-600 dark:text-green-400">
                                            Approved
                                        </span>
                                    </div>
                                    @elseif($status['approved'] === false)
                                    <div class="flex items-center">
                                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                                        <span class="text-xs text-red-600 dark:text-red-400">Rejected</span>
                                    </div>
                                    @elseif($purchaseRequest->getCurrentApprovalLevel() === $level)
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-yellow-500 mr-2"></i>
                                        <span class="text-xs text-yellow-600 dark:text-yellow-400">Menunggu</span>
                                    </div>
                                    @else
                                    <div class="flex items-center">
                                        <i class="fas fa-circle text-gray-400 mr-2"></i>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Belum</span>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    

                    <!-- Deskripsi -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-file-alt mr-2 text-purple-500"></i>
                            Deskripsi & Catatan
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <span class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Keterangan Kebutuhan</span>
                                <p class="text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">{{ $purchaseRequest->description }}</p>
                            </div>
                            @if($purchaseRequest->notes)
                            <div>
                                <span class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Catatan Tambahan</span>
                                <p class="text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded-md">{{ $purchaseRequest->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items yang Diminta -->
                    <div id="asset-section" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                            Items yang Diminta
                        </h3>

                        @php
                            // Flags for GA and completion, reused in desktop & mobile rows
                            $isGAUserInline = Auth::user()->divisi === 'HCGA';
                            $isPurchasingCompleteInline = $purchaseRequest->areAllItemsCompleted();
                            // Payment method visibility access
                            $canSeePaymentMethod = (
                                Auth::user()->level === 'admin' ||
                                in_array(Auth::user()->level, ['ceo','cfo']) ||
                                (Auth::user()->divisi === 'PURCHASING' && in_array(Auth::user()->level, ['manager','spv','staff'])) ||
                                (Auth::user()->divisi === 'FAT' && in_array(Auth::user()->level, ['manager','spv']))
                            );
                        @endphp

                        <!-- Desktop View -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        @if($canUpdateStatus)
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        @endif
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Satuan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Est. Satuan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Harga Barang</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Barang</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catatan Purchasing</th>
                                        @if($canSeePaymentMethod)
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment Method</th>
                                        @endif
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catatan</th>
                                        @php $gaShowCol = Auth::user()->divisi === 'HCGA'; @endphp
                                        @if($gaShowCol)
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No Asset</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($purchaseRequest->items as $index => $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        @if($canUpdateStatus)
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $completedStatuses = ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'];
                                                $canSelectItem = !in_array($item->item_status, $completedStatuses);
                                            @endphp
                                            @if($canSelectItem)
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-desc="{{ $item->description }}" data-qty="{{ $item->quantity }}" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            @else
                                            <span class="text-xs text-gray-400 italic">Selesai</span>
                                            @endif
                                        </td>
                                        @endif
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->description }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->quantity) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ $item->unit ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            @if($item->estimated_price)
                                            Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            @if($item->estimated_price)
                                            @php
                                                $itemTotal = $item->quantity * $item->estimated_price;
                                            @endphp
                                            Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                            @else
                                            <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $statusClass = match($item->item_status ?? 'PENDING') {
                                                    'PENDING' => 'bg-gray-100 text-gray-800',
                                                    'VENDOR_SEARCH' => 'bg-yellow-100 text-yellow-800',
                                                    'PRICE_COMPARISON' => 'bg-blue-100 text-blue-800',
                                                    'PO_CREATED' => 'bg-purple-100 text-purple-800',
                                                    'GOODS_RECEIVED' => 'bg-green-100 text-green-800',
                                                    'GOODS_RETURNED' => 'bg-red-100 text-red-800',
                                                    'COMPLAIN' => 'bg-orange-100 text-orange-800',
                                                    'TERSEDIA_DI_GA' => 'bg-emerald-100 text-emerald-800',
                                                    'REJECTED' => 'bg-red-500 text-white font-medium',
                                                    'CLOSED' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <div class="flex items-center flex-wrap gap-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                                    {{ $item->item_status_label ?? 'Pending' }}
                                                </span>
                                                @if($item->is_asset)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border border-amber-400 bg-amber-50 text-amber-700 dark:border-amber-600 dark:bg-amber-900/20 dark:text-amber-200">
                                                        Asset Pajak
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $item->purchasing_notes ?? '-' }}
                                        </td>
                                        @if($canSeePaymentMethod)
                                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                            @php $pmName = optional($item->paymentMethod)->name; @endphp
                                            {!! $pmName ? e($pmName) : '<span class="text-gray-400">-</span>' !!}
                                        </td>
                                        @endif
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->notes ?? '-' }}</td>
                                        @if($gaShowCol)
                                        <td class="px-4 py-3 text-sm">
                                            @php
                                                $assetCodes = method_exists($item, 'assets') ? $item->assets->pluck('asset_code')->toArray() : \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('purchase_request_item_id', $item->id)->pluck('asset_code')->toArray();
                                                $assetCount = count($assetCodes);
                                                $alreadyGenerated = $assetCount > 0;
                                                // Allow GA to click when purchasing is complete or if already has records (to view)
                                                $canClickGenerate = $isPurchasingCompleteInline || $alreadyGenerated;
                                                $canGenerateMore = $assetCount < $item->quantity;
                                                $btnLabel = !$alreadyGenerated ? 'Generate' : ($canGenerateMore ? 'Lanjut' : 'Lihat');
                                                $btnColor = $canGenerateMore ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700';
                                                $dataCanGenerate = ($canGenerateMore && $canClickGenerate) ? '1' : '0';
                                            @endphp
                                            @php 
                                                $isNonAssetGA = ($item->is_asset_hcga === false);
                                                if ($isNonAssetGA) { 
                                                    // Override to conversion intent UI
                                                    $btnLabel = 'Ubah jadi Asset';
                                                    $btnColor = 'bg-yellow-600 hover:bg-yellow-700';
                                                }
                                            @endphp
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                    class="inline-flex items-center px-2.5 py-1.5 rounded text-xs text-white {{ $btnColor }} {{ $canClickGenerate ? '' : 'opacity-60' }}"
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-name="{{ $item->description }}"
                                                    data-qty="{{ $item->quantity }}"
                                                    data-can-generate="{{ $dataCanGenerate }}"
                                                    data-assets="{{ $alreadyGenerated ? implode(', ', $assetCodes) : '' }}"
                                                    data-existing-count="{{ $assetCount }}"
                                                    data-purchasing-complete="{{ $isPurchasingCompleteInline ? '1' : '0' }}"
                                                    data-is-non-asset-ga="{{ $isNonAssetGA ? '1' : '0' }}"
                                                    onclick="openNoAssetModal(this)">
                                                    <i class="fas fa-barcode mr-1"></i> {{ $btnLabel }}
                                                </button>

                                                @if($isPurchasingCompleteInline && !$alreadyGenerated && !$isNonAssetGA)
                                                <form action="{{ route('purchase-request.mark-non-asset-ga', $purchaseRequest) }}" method="POST" class="inline-block"
                                                      onsubmit="return confirm('Tandai item ini sebagai Non-Asset GA?')">
                                                    @csrf
                                                    <input type="hidden" name="non_asset_ga_item_ids[]" value="{{ $item->id }}">
                                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs text-white bg-gray-600 hover:bg-gray-700">
                                                        <i class="fas fa-ban mr-1"></i> Non-Asset GA
                                                    </button>
                                                </form>
                                                @endif

                                                @if($isNonAssetGA)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        Non-Asset GA
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                    @if($purchaseRequest->items->whereNotNull('estimated_price')->count() > 0 || $purchaseRequest->total_estimated_price)
                                    <tr class="bg-blue-50 dark:bg-blue-900/20 border-t-2 border-blue-200 dark:border-blue-700">
                                        @php
                                            // align the label cells so that the numeric total sits under Total Harga Barang column
                        $leadingCols =  ($canUpdateStatus ? 6 : 5); // until before Harga Est. Satuan
                                        @endphp
                                        <td colspan="{{ $leadingCols }}" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Total Estimasi</td>
                                        <td class="px-4 py-3 text-sm font-bold text-blue-600 dark:text-blue-400">
                                            @php
                                                // Calculate total based on quantity × unit price for each item
                                                $calculatedTotal = $purchaseRequest->items->sum(function($item) {
                                                    return $item->quantity * ($item->estimated_price ?? 0);
                                                });
                                                $total = $purchaseRequest->total_estimated_price ?? $calculatedTotal;
                                            @endphp
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </td>
                                        @php
                                            // fill the rest of the row with empty cells matching remaining columns after Total Harga Barang
                                            // Columns: Status, Catatan Purchasing, [Payment Method optional], Catatan, [No Asset optional]
                                            $trailingCols =  ($gaShowCol ? 4 : 3) + ($canSeePaymentMethod ? 1 : 0);
                                        @endphp
                                        <td colspan="{{ $trailingCols }}"></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile View -->
                        <div class="lg:hidden space-y-4">
                            @foreach($purchaseRequest->items as $index => $item)
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                        Item #{{ $index + 1 }}
                                    </span>
                                    @if($canUpdateStatus)
                                    @php
                                        $completedStatuses = ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'];
                                        $canSelectItem = !in_array($item->item_status, $completedStatuses);
                                    @endphp
                                    @if($canSelectItem)
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" data-desc="{{ $item->description }}" data-qty="{{ $item->quantity }}" class="item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    @else
                                    <span class="text-xs text-gray-400 italic">Selesai</span>
                                    @endif
                                    @endif
                                </div>
                                <div class="space-y-2">
                                    <div>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Deskripsi:</span>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->description }}</p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Quantity:</span>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->quantity) }} {{ $item->unit ?? '' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Harga Est. Satuan:</span>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                @if($item->estimated_price)
                                                Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                                @else
                                                <span class="text-gray-400">-</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2 mt-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Harga Barang:</span>
                                            <p class="text-sm font-bold text-blue-600 dark:text-blue-400">
                                                @if($item->estimated_price)
                                                @php
                                                    $itemTotal = $item->quantity * $item->estimated_price;
                                                @endphp
                                                Rp {{ number_format($itemTotal, 0, ',', '.') }}
                                                @else
                                                <span class="text-gray-400">-</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 gap-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status Barang:</span>
                                            @php
                                                $statusClass = match($item->item_status ?? 'PENDING') {
                                                    'PENDING' => 'bg-gray-100 text-gray-800',
                                                    'VENDOR_SEARCH' => 'bg-yellow-100 text-yellow-800',
                                                    'PRICE_COMPARISON' => 'bg-blue-100 text-blue-800',
                                                    'PO_CREATED' => 'bg-purple-100 text-purple-800',
                                                    'GOODS_RECEIVED' => 'bg-green-100 text-green-800',
                                                    'GOODS_RETURNED' => 'bg-red-100 text-red-800',
                                                    'TERSEDIA_DI_GA' => 'bg-emerald-100 text-emerald-800',
                                                    'REJECTED' => 'bg-red-500 text-white font-medium',
                                                    'CLOSED' => 'bg-gray-100 text-gray-800',
                                                    default => 'bg-gray-100 text-gray-800'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }} mt-1">
                                                {{ $item->item_status_label ?? 'Pending' }}
                                            </span>
                                            @if($item->is_asset)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-amber-100 text-amber-800 border border-amber-300 dark:bg-amber-900/30 dark:text-amber-200 dark:border-amber-700">
                                                    Asset Pajak
                                                </span>
                                            @endif
                        @if($isGAUserInline)
                                                @php
                            $assetCodes = method_exists($item, 'assets') ? $item->assets->pluck('asset_code')->toArray() : \App\Models\Access_PR\Purchase_Request\PurchaseRequestItemAsset::where('purchase_request_item_id', $item->id)->pluck('asset_code')->toArray();
                            $assetCount = count($assetCodes);
                            $alreadyGenerated = $assetCount > 0;
                            $canClickGenerate = $isPurchasingCompleteInline || $alreadyGenerated;
                                                @endphp
                                                @php
                                                    $canGenerateMoreCard = $assetCount < $item->quantity;
                                                    $dataCanGenerateCard = ($canGenerateMoreCard && $isPurchasingCompleteInline) ? '1' : '0';
                                                    $isNonAssetGA = ($item->is_asset_hcga === false);
                                                @endphp
                                                <div class="mt-2 flex items-center gap-2">
                                                    @php 
                                                        $btnLabelCard = $alreadyGenerated ? 'Lihat No Asset' : 'No Asset';
                                                        $btnColorCard = $alreadyGenerated ? 'bg-blue-600 hover:bg-blue-700' : 'bg-green-600 hover:bg-green-700';
                                                        if ($isNonAssetGA) { $btnLabelCard = 'Ubah jadi Asset'; $btnColorCard = 'bg-yellow-600 hover:bg-yellow-700'; }
                                                    @endphp
                                                    <button type="button"
                                                        class="inline-flex items-center px-2.5 py-1.5 rounded text-xs text-white {{ $btnColorCard }} {{ $canClickGenerate ? '' : 'opacity-60' }}"
                                                        data-item-id="{{ $item->id }}"
                                                        data-item-name="{{ $item->description }}"
                                                        data-qty="{{ $item->quantity }}"
                                                        data-can-generate="{{ $dataCanGenerateCard }}"
                                                        data-assets="{{ $alreadyGenerated ? implode(', ', $assetCodes) : '' }}"
                                                        data-is-non-asset-ga="{{ $isNonAssetGA ? '1' : '0' }}"
                                                        onclick="openNoAssetModal(this)">
                                                        <i class="fas fa-barcode mr-1"></i> {{ $btnLabelCard }}
                                                    </button>

                                                    @if($isPurchasingCompleteInline && !$alreadyGenerated && !$isNonAssetGA)
                                                    <form action="{{ route('purchase-request.mark-non-asset-ga', $purchaseRequest) }}" method="POST" class="inline-block"
                                                          onsubmit="return confirm('Tandai item ini sebagai Non-Asset GA?')">
                                                        @csrf
                                                        <input type="hidden" name="non_asset_ga_item_ids[]" value="{{ $item->id }}">
                                                        <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded text-xs text-white bg-gray-600 hover:bg-gray-700">
                                                            <i class="fas fa-ban mr-1"></i> Non-Asset GA
                                                        </button>
                                                    </form>
                                                    @endif

                                                    @if($isNonAssetGA)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-medium bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        Non-Asset GA
                                                    </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                        @if($canSeePaymentMethod && optional($item->paymentMethod)->name)
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Method:</span>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $item->paymentMethod->name }}</p>
                                        </div>
                                        @endif
                                        @if($item->purchasing_notes)
                                        <div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Catatan Purchasing:</span>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">{{ $item->purchasing_notes }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    @if($item->notes)
                                    <div>
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Catatan:</span>
                                        <p class="text-sm text-gray-900 dark:text-gray-100">{{ $item->notes }}</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endforeach

                            @if($purchaseRequest->items->whereNotNull('estimated_price')->count() > 0)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-2 border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Total Estimasi:</span>
                                    <span class="text-lg font-bold text-blue-600 dark:text-blue-400">
                                        @php
                                            // Calculate total based on quantity × unit price for each item
                                            $calculatedTotal = $purchaseRequest->items->sum(function($item) {
                                                return $item->quantity * ($item->estimated_price ?? 0);
                                            });
                                            $total = $purchaseRequest->total_estimated_price ?? $calculatedTotal;
                                        @endphp
                                        Rp {{ number_format($total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Purchasing Actions (untuk Purchasing) -->
                        @if($canUpdateStatus)
                        @php
                            // Filter items yang belum final status untuk purchasing
                            $finalStatuses = ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'];
                            $purchasingItems = $purchaseRequest->items->whereNotIn('item_status', $finalStatuses);
                            $hasItemsToProcess = $purchasingItems->count() > 0;
                        @endphp
                        
                        @if($hasItemsToProcess)
                        @php
                            // Check if user has processed any items (by checking if any items have VENDOR_SEARCH status from pending)
                            $hasProcessedItems = $purchaseRequest->items->where('item_status', 'VENDOR_SEARCH')->count() > 0;
                        @endphp
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                            <!-- Step 1: Proses Items -->
                            <div id="processItemsSection" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4 {{ $hasProcessedItems ? 'step1-minimized' : '' }}">
                                <div class="flex items-center justify-between cursor-pointer" onclick="toggleProcessItemsSection()">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">1</span>
                                        <i class="fas fa-clipboard-check mr-2 text-blue-600"></i>
                                        Proses Items (Langkah Pertama)
                                        @if($hasProcessedItems)
                                        <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                            <i class="fas fa-check mr-1"></i>Sudah Diproses
                                        </span>
                                        @endif
                                    </h4>
                                    <div class="flex items-center">
                                        @if($hasProcessedItems)
                                        <span class="text-xs text-blue-600 mr-2">Klik untuk expand/minimize</span>
                                        @endif
                                        <i id="processItemsToggle" class="fas {{ $hasProcessedItems ? 'fa-chevron-down' : 'fa-chevron-up' }} text-blue-600"></i>
                                    </div>
                                </div>
                                <div id="processItemsContent" class="{{ $hasProcessedItems ? 'hidden' : '' }}">
                                    <div class="mb-4 {{ $hasProcessedItems ? 'mt-3' : '' }}">
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                            Pilih item yang akan diproses. Item yang disetujui akan mulai dengan status "Pencarian Vendor".
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $purchasingItems->count() }} item tersedia untuk diproses (item yang sudah selesai tidak ditampilkan)
                                        </p>
                                    </div>
                                    <button type="button" onclick="showSimplifiedPurchasingModal()" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors inline-flex items-center">
                                        <i class="fas fa-play mr-2"></i>
                                        Mulai Proses Items
                                    </button>
                                </div>
                            </div>

                            <!-- Step 2: Update Status Barang (Bulk) -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                    <span class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs mr-2">2</span>
                                    <i class="fas fa-edit mr-2 text-yellow-600"></i>
                                    Update Status Barang (Langkah Lanjutan)
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Setelah memproses items, gunakan form ini untuk update status sesuai progress (Vendor ditemukan → PO dibuat → Barang diterima)
                                </p>
                                <form id="bulkUpdateForm" action="{{ route('purchase-request.bulk-update-item-status', $purchaseRequest) }}" method="POST">
                                    @csrf
                                    <!-- Hidden container for selected item IDs -->
                                    <div id="selectedItemsContainer"></div>
                                    <input type="hidden" id="hidden_payment_method_id" name="payment_method_id" value="">
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                        <div>
                                            <label for="item_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Barang</label>
                                            <select name="item_status" id="item_status" required class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                <option value="">Pilih Status</option>
                                                @foreach(\App\Models\Access_PR\Purchase_Request\PurchaseRequestItem::getItemStatusLabels() as $value => $label)
                                                    @if(!in_array($value, ['TERSEDIA_DI_GA', 'PENDING', 'CLOSED', 'REJECTED']))
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <!-- Inline payment method (kept hidden; selection via modal) -->
                                        <div id="paymentMethodContainer" class="hidden md:col-span-1"></div>
                                        <div class="md:col-span-2">
                                            <label for="purchasing_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan dari Purchasing</label>
                                            <textarea name="purchasing_notes" id="purchasing_notes" rows="2" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Masukkan keterangan (opsional)"></textarea>
                                        </div>
                                    </div>
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2 sm:mb-0">
                                            <span id="selectedCount">0</span> item dipilih
                                        </div>
                                        <button type="submit" id="bulkUpdateBtn" disabled class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 disabled:bg-gray-400 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors inline-flex items-center">
                                            <i class="fas fa-save mr-2"></i>
                                            Update Status Item Terpilih
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @else
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                            <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-center text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-check-circle mr-2 text-green-500"></i>
                                    <span class="text-sm">Semua item sudah tersedia di GA. Tidak ada yang perlu diproses purchasing.</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>

                    <!-- File Attachments Section -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">

                        
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 sm:mb-0 flex items-center">
                                <i class="fas fa-paperclip mr-2 text-indigo-500"></i>
                                File Lampiran 
                                @if($purchaseRequest->attachments && count($purchaseRequest->attachments) > 0)
                                    ({{ count($purchaseRequest->attachments) }} file)
                                @endif
                            </h3>
                            @php
                                $currentFileCount = count($purchaseRequest->attachments ?? []);
                                $isOwner = Auth::user()->id == $purchaseRequest->user_id; // loose comparison untuk handle string vs int
                                $isAdmin = Auth::user()->level === 'admin';
                                $statusOk = !in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED']);
                                $fileCountOk = $currentFileCount < 5;
                                $canAddFile = ($isOwner || $isAdmin) && $statusOk && $fileCountOk;
                            @endphp
                            

                            
                            @if($canAddFile)
                                <button type="button"
                                    onclick="showAddFileModal()"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors inline-flex items-center">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah File ({{ $currentFileCount }}/5)
                                </button>
                            @elseif($currentFileCount >= 5)
                                <span class="px-3 py-2 bg-gray-400 text-white text-sm rounded-lg inline-flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-2"></i>
                                    Maksimal 5 file tercapai
                                </span>
                            @elseif(in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED']))
                                <span class="text-sm text-gray-500">Status: {{ $purchaseRequest->status }} - File tidak dapat ditambah</span>
                            @elseif(Auth::user()->id !== $purchaseRequest->user_id && Auth::user()->level !== 'admin')
                                <span class="text-sm text-gray-500">Hanya pemilik PR yang dapat menambah file</span>
                            @endif
                        </div>
                        
                        @if($purchaseRequest->attachments && count($purchaseRequest->attachments) > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($purchaseRequest->attachments as $index => $attachment)
                                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                        @if(str_contains($attachment['mime_type'] ?? '', 'image'))
                                            <!-- Image Preview -->
                                            <div class="aspect-w-16 aspect-h-9 bg-gray-100 dark:bg-gray-700">
                                                <img src="{{ asset('storage/' . ($attachment['path'] ?? '')) }}" 
                                                     alt="{{ $attachment['original_name'] ?? 'Image' }}"
                                                     class="w-full h-32 object-cover cursor-pointer image-preview"
                                                     data-image-url="{{ asset('storage/' . ($attachment['path'] ?? '')) }}"
                                                     data-image-name="{{ $attachment['original_name'] ?? 'Image' }}">
                                            </div>
                                        @else
                                            <!-- File Icon for non-images -->
                                            <div class="h-32 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                                @if(str_contains($attachment['mime_type'] ?? '', 'pdf'))
                                                    <i class="fas fa-file-pdf text-red-500 text-4xl"></i>
                                                @else
                                                    <i class="fas fa-file text-gray-500 text-4xl"></i>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="p-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate" title="{{ $attachment['original_name'] ?? 'Unknown File' }}">
                                                {{ $attachment['original_name'] ?? 'Unknown File' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ number_format(($attachment['size'] ?? 0) / 1024 / 1024, 2) }} MB
                                            </div>
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                <a href="{{ asset('storage/' . ($attachment['path'] ?? '')) }}" 
                                                   target="_blank"
                                                   class="inline-flex items-center px-2 py-1 bg-blue-100 hover:bg-blue-200 dark:bg-blue-800 dark:hover:bg-blue-700 text-blue-800 dark:text-blue-200 text-xs rounded transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>
                                                    Lihat
                                                </a>
                                                <a href="{{ asset('storage/' . ($attachment['path'] ?? '')) }}" 
                                                   download="{{ $attachment['original_name'] ?? 'download' }}"
                                                   class="inline-flex items-center px-2 py-1 bg-green-100 hover:bg-green-200 dark:bg-green-800 dark:hover:bg-green-700 text-green-800 dark:text-green-200 text-xs rounded transition-colors">
                                                    <i class="fas fa-download mr-1"></i>
                                                    Download
                                                </a>
                                                @if((Auth::user()->id == $purchaseRequest->user_id || Auth::user()->level === 'admin') && !in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED']))
                                                    <button type="button"
                                                        class="delete-file-btn inline-flex items-center px-2 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-800 dark:hover:bg-red-700 text-red-800 dark:text-red-200 text-xs rounded transition-colors"
                                                        data-file-index="{{ $index }}"
                                                        data-file-name="{{ $attachment['original_name'] ?? 'File' }}">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Hapus
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada file lampiran.</p>
                                @php
                                    $canAddFirstFile = (Auth::user()->id == $purchaseRequest->user_id || Auth::user()->level === 'admin') 
                                                     && !in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED']);
                                @endphp
                                
                                @if($canAddFirstFile)
                                    <button type="button"
                                        onclick="showAddFileModal()"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center">
                                        <i class="fas fa-plus mr-2"></i>
                                        Tambah File Pertama (0/5)
                                    </button>
                                @elseif(in_array($purchaseRequest->status, ['COMPLETED', 'CANCELLED']))
                                    <p class="text-sm text-gray-500">File tidak dapat ditambah karena PR sudah {{ $purchaseRequest->status }}</p>
                                @else
                                    <p class="text-sm text-gray-500">Hanya pemilik PR yang dapat menambah file</p>
                                @endif
                            </div>
                        @endif

                        <!-- File Activity Log -->
                        @if($purchaseRequest->file_logs && count($purchaseRequest->file_logs) > 0)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-600">
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                    <i class="fas fa-history mr-2 text-gray-500"></i>
                                    Riwayat Aktivitas File
                                </h4>
                                <div class="space-y-2">
                                    @foreach($purchaseRequest->file_logs as $log)
                                        <div class="flex items-center space-x-3 p-2 bg-gray-50 dark:bg-gray-700 rounded text-xs">
                                            <span class="flex-shrink-0">
                                                @if($log['action'] === 'added')
                                                    <i class="fas fa-plus text-green-500"></i>
                                                @else
                                                    <i class="fas fa-trash text-red-500"></i>
                                                @endif
                                            </span>
                                            <span class="flex-1">
                                                {{ $log['message'] }}
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($log['timestamp'])->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Status Updates (untuk Purchasing) -->
                    @if($purchaseRequest->statusUpdates->count() > 0 || $canUpdateStatus)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                <i class="fas fa-clipboard-list mr-2 text-orange-500"></i>
                                Update Status Purchasing
                            </h3>
                        </div>

                        @if($purchaseRequest->statusUpdates->count() > 0)
                        <div class="space-y-4">
                            @foreach($purchaseRequest->statusUpdates->sortByDesc('created_at') as $update)
                            <div class="flex space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-blue-500">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400 text-sm"></i>
                                    </div>
                                </div>
                                <div class="flex-grow">
                                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start mb-2">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $update->update_type_label }}
                                        </h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 sm:mt-0">
                                            {{ $update->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{{ $update->description }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        oleh {{ $update->updatedBy->name }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">Belum ada update dari purchasing.</p>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
                            <a href="{{ route('purchase-request.index') }}"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Kembali
                            </a>

                            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                                <a href="{{ route('purchase-request.print', $purchaseRequest) }}" target="_blank"
                                    class="inline-flex items-center px-4 py-2 border border-indigo-300 dark:border-indigo-600 rounded-lg text-sm font-medium text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <i class="fas fa-print mr-2"></i>
                                    Print
                                </a>
                                @if($purchaseRequest->user_id == auth()->id() && $purchaseRequest->status === 'DRAFT')
                                <a href="{{ route('purchase-request.edit', $purchaseRequest) }}"
                                    class="inline-flex items-center px-4 py-2 border border-yellow-300 dark:border-yellow-600 rounded-lg text-sm font-medium text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/40 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>
                                    Edit
                                </a>
                                @endif

                                @if($canApprove)
                                <button type="button"
                                    onclick="showRejectModal()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                    <i class="fas fa-times mr-2"></i>
                                    Tolak
                                </button>
                                <button type="button"
                                    onclick="showApproveModal()"
                                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check mr-2"></i>
                                    Setujui
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Approve Modal -->
    @if($canApprove)
    <div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[600px] shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mt-4">Setujui Purchase Request</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">
                    Anda akan menyetujui Purchase Request <strong>#{{ $purchaseRequest->pr_number }}</strong>
                </p>

                @php
                    $currentApprovalLevel = $purchaseRequest->getCurrentApprovalLevel();
                    $isGAApproval = $currentApprovalLevel === 'ga' && Auth::user()->divisi === 'HCGA';
                @endphp

                <div class="mt-6">
                    <form action="{{ $isGAApproval ? route('purchase-request.ga-approve-with-items', $purchaseRequest) : route('purchase-request.approve', $purchaseRequest) }}" method="POST">
                        @csrf
                        
                        @if($isGAApproval)
                        <!-- GA Item Selection with Quantity -->
                        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                <i class="fas fa-boxes mr-2 text-blue-600"></i>
                                Pilih Barang yang Tersedia di GA (Opsional)
                            </h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                                Centang barang yang tersedia di GA dan tentukan jumlah yang tersedia. Jika hanya sebagian, sisa barang akan dilanjutkan ke proses selanjutnya.
                            </p>
                            <div class="space-y-3 max-h-60 overflow-y-auto">
                                @foreach($purchaseRequest->items as $item)
                                <div class="p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg">
                                    <div class="flex items-start space-x-3">
                                        <input type="checkbox" 
                                               name="available_items[]" 
                                               value="{{ $item->id }}" 
                                               class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500 item-checkbox-ga"
                                               id="item_{{ $item->id }}"
                                               >
                                        <div class="flex-1 min-w-0">
                                            <label for="item_{{ $item->id }}" class="cursor-pointer">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $item->description }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Total diminta: {{ number_format($item->quantity) }} {{ $item->unit ?? '' }}
                                                    @if($item->estimated_price)
                                                    | Harga satuan: Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                                    @endif
                                                </div>
                                            </label>
                                            <div class="mt-2 hidden" id="qty_input_{{ $item->id }}">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Qty Tersedia di GA:
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <input type="number" 
                                                           name="available_quantities[{{ $item->id }}]" 
                                                           min="1" 
                                                           max="{{ $item->quantity }}"
                                                           class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                           placeholder="{{ $item->quantity }}">
                                                    <span class="text-xs text-gray-500">dari {{ number_format($item->quantity) }} {{ $item->unit ?? '' }}</span>
                                                </div>
                                                <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Kosongkan jika semua quantity tersedia
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @php
                            $isFATApproval = $currentApprovalLevel === 'finance_dept' && Auth::user()->divisi === 'FAT';
                        @endphp

                        @if($isFATApproval)
                        <!-- FAT Approval Fields -->
                        <div class="mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                <i class="fas fa-building mr-2 text-blue-600"></i>
                                Informasi FAT Approval
                            </h4>
                            
                            <!-- Company Selection -->
                            <div class="mb-3">
                                <label for="fat_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Perusahaan <span class="text-red-500">*</span>
                                </label>
                                <select name="fat_department" id="fat_department" required onchange="handleFATDepartmentChange()"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                    <option value="">Pilih Perusahaan</option>
                                    <option value="PIS">PIS</option>
                                    <option value="MMI">MMI</option>
                                    <option value="AOS">AOS</option>
                                    <option value="LAINNYA">Lainnya</option>
                                </select>
                            </div>

                            <!-- Other Company Input (shown when "Lainnya" is selected) -->
                            <div class="mb-3 hidden" id="other_department_div">
                                <label for="other_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Sebutkan Perusahaan
                                </label>
                                <input type="text" name="other_department" id="other_department"
                                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Masukkan nama perusahaan">
                            </div>

                            <!-- Per-item Asset/Non-Asset Classification -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Klasifikasi Item (Asset / Non-Asset) <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2 max-h-56 overflow-auto pr-1">
                                    @foreach($purchaseRequest->items as $prItem)
                                    <div class="flex items-center justify-between bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-3 py-2">
                                        <div class="text-xs text-gray-700 dark:text-gray-200 flex-1 pr-2">
                                            <div class="font-medium">{{ $prItem->description }}</div>
                                            <div class="text-[11px] text-gray-500">Qty: {{ $prItem->quantity }} {{ $prItem->unit ?? '' }}</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <label class="inline-flex items-center text-xs">
                                                <input type="radio" name="fat_item_types[{{ $prItem->id }}]" value="asset" class="mr-1">
                                                Asset
                                            </label>
                                            <label class="inline-flex items-center text-xs">
                                                <input type="radio" name="fat_item_types[{{ $prItem->id }}]" value="non_asset" class="mr-1" checked>
                                                Non-Asset
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Jika item di-split saat proses berikutnya, status Asset/Non-Asset akan mengikuti item asal.</p>
                            </div>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label for="approve_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Catatan Persetujuan (opsional)
                            </label>
                            <textarea name="notes"
                                id="approve_notes"
                                rows="3"
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Tambahkan catatan persetujuan jika diperlukan"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancel-approve"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-check mr-2"></i>
                                Ya, Setujui
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mt-4">Tolak Purchase Request</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">
                    Anda akan menolak Purchase Request <strong>#{{ $purchaseRequest->pr_number }}</strong>
                </p>
                <p class="text-xs text-red-600 dark:text-red-400 text-center mt-1">
                    ⚠️ Status ini akan final dan tidak dapat diubah lagi
                </p>

                <div class="mt-6">
                    <form action="{{ route('purchase-request.reject', $purchaseRequest) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="reject_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alasan Penolakan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="notes"
                                id="reject_notes"
                                rows="3"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Jelaskan alasan penolakan secara detail (wajib diisi)"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancel-reject"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Ya, Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- No Asset Modal (kept globally, triggered from item rows) -->
    <div id="no-asset-modal" class="fixed inset-0 bg-gray-900/50 hidden z-50">
        <div class="bg-white dark:bg-gray-800 w-[560px] max-w-[92vw] mx-auto mt-24 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100"><i class="fas fa-barcode mr-2 text-green-600"></i><span id="noAssetTitle">No Asset</span></h4>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeNoAssetModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="p-4 space-y-3">
                <div>
                    <div class="text-sm font-medium text-gray-800 dark:text-gray-100" id="noAssetItemName"></div>
                    <div class="text-xs text-gray-500" id="noAssetQty"></div>
                </div>

                <form id="noAssetForm" action="{{ route('purchase-request.assign-asset-numbers', $purchaseRequest) }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="__single_item_id" id="noAssetItemId" />
                    <div id="noAssetInputRow" class="flex items-center gap-3">
                        <label class="text-sm text-gray-700 dark:text-gray-300 w-32">Kode Dasar</label>
                        <input type="text" id="noAssetBaseCode" class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white" placeholder="Contoh: A1">
                    </div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Format hasil: BASE-001, BASE-002, ... sesuai qty.</div>
                    <div class="flex justify-end">
                        <button type="submit" id="noAssetSubmitBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm">Generate</button>
                    </div>
                </form>

                <div id="noAssetList" class="hidden">
                    <h5 class="text-xs font-semibold text-gray-900 dark:text-gray-100 mb-2">Nomor Asset</h5>
                    <div id="noAssetListGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    @if($canUpdateStatus)
    <div id="update-status-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[500px] shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-blue-100 dark:bg-blue-900 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mt-4">Tambah Update Status</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">
                    Update progress Purchase Request <strong>#{{ $purchaseRequest->pr_number }}</strong>
                </p>

                <div class="mt-6">
                    <form action="{{ route('purchase-request.update-status', $purchaseRequest) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="update_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Jenis Update <span class="text-red-500">*</span>
                            </label>
                            <select name="update_type"
                                id="update_type"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Pilih Jenis Update</option>
                                <option value="VENDOR_SEARCH">Pencarian Vendor</option>
                                <option value="PRICE_COMPARISON">Perbandingan Harga</option>
                                <option value="PO_CREATED">PO ke Vendor</option>
                                <option value="GOODS_RECEIVED">Barang Diterima</option>
                                <option value="GOODS_RETURNED">Barang Dikembalikan</option>
                                <option value="COMPLAIN">Complain</option>
                                <option value="CLOSED">Completed</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="update_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Deskripsi Update <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description"
                                id="update_description"
                                rows="4"
                                required
                                class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white resize-none"
                                placeholder="Jelaskan detail progress dan update yang dilakukan"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <button type="button" id="cancel-update-status"
                                class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-lg transition-all duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Image Preview Modal -->
    <div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
        <div class="relative max-w-4xl max-h-full">
            <button id="close-image-modal" class="absolute -top-10 right-0 text-white hover:text-gray-300 text-2xl">
                <i class="fas fa-times"></i>
            </button>
            <img id="modal-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg shadow-lg">
            <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-75 text-white p-3 rounded-b-lg">
                <p id="modal-image-name" class="text-sm font-medium"></p>
            </div>
        </div>
    </div>

    <!-- Add File Modal -->
    <div id="add-file-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-[500px] shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-indigo-100 dark:bg-indigo-900 rounded-full">
                    <i class="fas fa-paperclip text-indigo-600 dark:text-indigo-400 text-xl"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Tambah File Lampiran
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Pilih file yang ingin dilampirkan (JPG, JPEG, PNG, PDF - maksimal 2MB)
                        </p>
                    </div>
                </div>
            </div>
            <form action="{{ route('purchase-request.add-attachment', $purchaseRequest) }}" method="POST" enctype="multipart/form-data" id="add-file-form" class="mt-6">
                @csrf
                <div class="space-y-4">
                    <div>
                        <input type="file" 
                               name="attachments[]" 
                               id="file-input" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               accept=".jpg,.jpeg,.png,.pdf"
                               multiple
                               onchange="validateFileSize(this)"
                               required>
                        <div id="file-preview-add" class="mt-2 space-y-2"></div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="cancel-add-file"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white rounded-lg transition-all duration-200 flex items-center">
                        <i class="fas fa-upload mr-2"></i>
                        Upload File
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete File Confirmation Modal -->
    <div id="delete-file-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-[400px] shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 dark:bg-red-900 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Konfirmasi Hapus File
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Apakah Anda yakin ingin menghapus file "<span id="file-to-delete"></span>"?
                        </p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                            File yang dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <form action="{{ route('purchase-request.delete-attachment', $purchaseRequest) }}" method="POST" id="delete-file-form" class="mt-6">
                @csrf
                @method('DELETE')
                <input type="hidden" name="file_index" id="delete-file-index">
                <div class="flex justify-center space-x-3">
                    <button type="button" id="cancel-delete-file"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-400 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-white rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-lg transition-all duration-200 flex items-center">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus File
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions untuk Approve
        function showApproveModal() {
            document.getElementById('approve-modal').classList.remove('hidden');
        }

        function hideApproveModal() {
            document.getElementById('approve-modal').classList.add('hidden');
            document.getElementById('approve_notes').value = '';
        }

        // Modal functions untuk Reject  
        function showRejectModal() {
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function hideRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
            document.getElementById('reject_notes').value = '';
        }

        function showUpdateStatusModal() {
            document.getElementById('update-status-modal').classList.remove('hidden');
        }

        function hideUpdateStatusModal() {
            document.getElementById('update-status-modal').classList.add('hidden');
            document.getElementById('update_type').value = '';
            document.getElementById('update_description').value = '';
        }

        // FAT Department selection handler
        function handleFATDepartmentChange() {
            const departmentSelect = document.getElementById('fat_department');
            const otherDepartmentDiv = document.getElementById('other_department_div');
            const otherDepartmentInput = document.getElementById('other_department');
            
            if (departmentSelect && otherDepartmentDiv && otherDepartmentInput) {
                if (departmentSelect.value === 'LAINNYA') {
                    otherDepartmentDiv.classList.remove('hidden');
                    otherDepartmentInput.required = true;
                } else {
                    otherDepartmentDiv.classList.add('hidden');
                    otherDepartmentInput.required = false;
                    otherDepartmentInput.value = '';
                }
            }
        }

        // Image preview modal functions
        function showImageModal(imageUrl, imageName) {
            const modal = document.getElementById('image-modal');
            const modalImage = document.getElementById('modal-image');
            const modalImageName = document.getElementById('modal-image-name');
            
            modalImage.src = imageUrl;
            modalImage.alt = imageName;
            modalImageName.textContent = imageName;
            modal.classList.remove('hidden');
        }

        function hideImageModal() {
            const modal = document.getElementById('image-modal');
            modal.classList.add('hidden');
        }

        // Add file modal functions
        function showAddFileModal() {
            document.getElementById('add-file-modal').classList.remove('hidden');
        }

        function hideAddFileModal() {
            document.getElementById('add-file-modal').classList.add('hidden');
            document.getElementById('file-input').value = '';
            document.getElementById('file-preview-add').innerHTML = '';
        }

        // Delete file modal functions
        function confirmDeleteFile(fileIndex, fileName) {
            document.getElementById('file-to-delete').textContent = fileName;
            document.getElementById('delete-file-index').value = fileIndex;
            document.getElementById('delete-file-modal').classList.remove('hidden');
        }

        function hideDeleteFileModal() {
            document.getElementById('delete-file-modal').classList.add('hidden');
        }

        // ========= No Asset Modal (GA) =========
        function openNoAssetModal(btn) {
            const modal = document.getElementById('no-asset-modal');
            const itemId = btn.getAttribute('data-item-id');
            const itemName = btn.getAttribute('data-item-name');
            const qty = btn.getAttribute('data-qty');
            const canGenerate = btn.getAttribute('data-can-generate') === '1';
            const assetsStr = btn.getAttribute('data-assets') || '';
            const isNonAssetGA = btn.getAttribute('data-is-non-asset-ga') === '1';

            // Populate header
            document.getElementById('noAssetItemName').textContent = itemName;
            document.getElementById('noAssetQty').textContent = `Qty: ${qty}`;

            // Configure form for single item
            const input = document.getElementById('noAssetBaseCode');
            input.value = '';
            input.setAttribute('name', `asset_bases[${itemId}]`);
            const formRow = document.getElementById('noAssetInputRow');
            const submitBtn = document.getElementById('noAssetSubmitBtn');
            const form = document.getElementById('noAssetForm');

            const listWrap = document.getElementById('noAssetList');
            const listGrid = document.getElementById('noAssetListGrid');
            listGrid.innerHTML = '';

            if (canGenerate) {
                // Show form for base input; hide list
                formRow.classList.remove('hidden');
                submitBtn.classList.remove('hidden');
                form.classList.remove('pointer-events-none', 'opacity-60');
                listWrap.classList.add('hidden');
                document.getElementById('noAssetTitle').textContent = 'Generate No Asset';

                // Attach submit confirm if converting from Non-Asset GA to Asset GA
                form.onsubmit = (e) => {
                    // remove any previous hidden input
                    const prev = form.querySelector(`input[name="convert_non_asset_ga[${itemId}]"]`);
                    if (prev) prev.remove();
                    if (isNonAssetGA) {
                        const proceed = confirm('Item ini sudah ditandai Non-Asset GA. Ubah menjadi Asset GA dan generate nomor?');
                        if (!proceed) { e.preventDefault(); return false; }
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = `convert_non_asset_ga[${itemId}]`;
                        hidden.value = '1';
                        form.appendChild(hidden);
                    }
                    return true;
                };
            } else {
                // View mode only
                formRow.classList.add('hidden');
                submitBtn.classList.add('hidden');
                form.classList.add('pointer-events-none', 'opacity-60');
                document.getElementById('noAssetTitle').textContent = 'Daftar No Asset';

                // Render existing asset codes
                const codes = assetsStr ? assetsStr.split(',') : [];
                codes.forEach(c => {
                    const el = document.createElement('div');
                    el.className = 'text-xs bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded px-2 py-1 font-mono text-green-700 dark:text-green-300';
                    el.textContent = c.trim();
                    listGrid.appendChild(el);
                });
                listWrap.classList.remove('hidden');
            }

            modal.classList.remove('hidden');
        }

        function closeNoAssetModal() {
            document.getElementById('no-asset-modal').classList.add('hidden');
        }

        // Validate file size and type for add file modal
        function validateFileSize(input) {
            const files = input.files;
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            const previewContainer = document.getElementById('file-preview-add');
            
            // Get current file count from existing attachment cards
            const attachmentCards = document.querySelectorAll('.bg-gray-50.dark\\:bg-gray-700.border.border-gray-200.dark\\:border-gray-600.rounded-lg.overflow-hidden');
            const currentFileCount = attachmentCards.length;
            const maxFiles = 5;
            
            previewContainer.innerHTML = ''; // Clear previous previews
            
            // Check total file limit
            if (currentFileCount + files.length > maxFiles) {
                alert(`Maksimal hanya dapat mengunggah ${maxFiles} file. Saat ini sudah ada ${currentFileCount} file. Anda hanya dapat menambah ${maxFiles - currentFileCount} file lagi.`);
                input.value = '';
                return false;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} tidak didukung. Hanya JPG, JPEG, PNG, dan PDF yang diperbolehkan.`);
                    input.value = '';
                    return false;
                }
                
                // Check file size
                if (file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar. Maksimal ukuran file adalah 2MB.`);
                    input.value = '';
                    return false;
                }
                
                // Create file preview
                const filePreview = document.createElement('div');
                filePreview.className = 'flex items-center space-x-2 p-2 bg-gray-100 dark:bg-gray-700 rounded';
                
                const fileIcon = file.type.includes('image') ? '🖼️' : '📄';
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                filePreview.innerHTML = `
                    <span class="text-lg">${fileIcon}</span>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${file.name}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">${fileSize} MB</p>
                    </div>
                    <span class="text-green-500">✓</span>
                `;
                
                previewContainer.appendChild(filePreview);
            }
            
            return true;
        }

        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Approve modal event listeners
            const approveModal = document.getElementById('approve-modal');
            const cancelApprove = document.getElementById('cancel-approve');

            if (cancelApprove) {
                cancelApprove.addEventListener('click', hideApproveModal);
            }

            if (approveModal) {
                approveModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideApproveModal();
                    }
                });
            }

            // Reject modal event listeners
            const rejectModal = document.getElementById('reject-modal');
            const cancelReject = document.getElementById('cancel-reject');

            if (cancelReject) {
                cancelReject.addEventListener('click', hideRejectModal);
            }

            if (rejectModal) {
                rejectModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideRejectModal();
                    }
                });
            }

            // Update Status modal event listeners
            const updateStatusModal = document.getElementById('update-status-modal');
            const cancelUpdateStatus = document.getElementById('cancel-update-status');

            if (cancelUpdateStatus) {
                cancelUpdateStatus.addEventListener('click', hideUpdateStatusModal);
            }

            if (updateStatusModal) {
                updateStatusModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideUpdateStatusModal();
                    }
                });
            }

            // Image preview modal event listeners
            const imageModal = document.getElementById('image-modal');
            const closeImageModal = document.getElementById('close-image-modal');
            
            // Image preview clicks
            document.querySelectorAll('.image-preview').forEach(img => {
                img.addEventListener('click', function() {
                    const imageUrl = this.getAttribute('data-image-url');
                    const imageName = this.getAttribute('data-image-name');
                    showImageModal(imageUrl, imageName);
                });
            });
            
            // Close image modal
            if (closeImageModal) {
                closeImageModal.addEventListener('click', hideImageModal);
            }
            
            if (imageModal) {
                imageModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideImageModal();
                    }
                });
            }

            // Add file modal event listeners
            const addFileModal = document.getElementById('add-file-modal');
            const cancelAddFile = document.getElementById('cancel-add-file');
            
            if (cancelAddFile) {
                cancelAddFile.addEventListener('click', hideAddFileModal);
            }
            
            if (addFileModal) {
                addFileModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideAddFileModal();
                    }
                });
            }

            // Delete file modal event listeners
            const deleteFileModal = document.getElementById('delete-file-modal');
            const cancelDeleteFile = document.getElementById('cancel-delete-file');
            
            // Delete file button clicks
            document.querySelectorAll('.delete-file-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const fileIndex = this.getAttribute('data-file-index');
                    const fileName = this.getAttribute('data-file-name');
                    confirmDeleteFile(fileIndex, fileName);
                });
            });
            
            if (cancelDeleteFile) {
                cancelDeleteFile.addEventListener('click', hideDeleteFileModal);
            }
            
            if (deleteFileModal) {
                deleteFileModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        hideDeleteFileModal();
                    }
                });
            }

            // Bind GA checkbox quantity toggle (replaces inline onchange)
            document.querySelectorAll('.item-checkbox-ga').forEach(cb => {
                cb.addEventListener('change', () => {
                    const id = cb.id.replace('item_', '');
                    if (id) {
                        toggleQuantityInput(id);
                    }
                });
            });

            // Bind simplified purchasing action change (replaces inline onchange)
            document.querySelectorAll('[id^="simple_action_"]').forEach(sel => {
                sel.addEventListener('change', () => {
                    const id = sel.id.replace('simple_action_', '');
                    if (id) {
                        toggleSimplePurchasingAction(id);
                    }
                });
            });
        });

        // Bulk item update functionality
        function updateSelectedCount() {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            const count = checkboxes.length;
            const selectedCountSpan = document.getElementById('selectedCount');
            const bulkUpdateBtn = document.getElementById('bulkUpdateBtn');
            const selectedItemsContainer = document.getElementById('selectedItemsContainer');
            
            if (selectedCountSpan) {
                selectedCountSpan.textContent = count;
            }
            
            if (bulkUpdateBtn) {
                bulkUpdateBtn.disabled = count === 0;
            }
            
            // Update hidden inputs for selected item IDs
            if (selectedItemsContainer) {
                selectedItemsContainer.innerHTML = '';
                checkboxes.forEach(checkbox => {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'item_ids[]';
                    hiddenInput.value = checkbox.value;
                    selectedItemsContainer.appendChild(hiddenInput);
                });
            }
        }

        // Select All functionality
        document.getElementById('selectAll')?.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.item-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        document.querySelectorAll('.item-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                
                // Update select all checkbox
                const allCheckboxes = document.querySelectorAll('.item-checkbox');
                const checkedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                const selectAllCheckbox = document.getElementById('selectAll');
                
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0;
                    selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
                }
            });
        });

        // Handle bulk update form submission
        document.getElementById('bulkUpdateForm')?.addEventListener('submit', function(e) {
            const checkboxes = document.querySelectorAll('.item-checkbox:checked');
            if (checkboxes.length === 0) {
                e.preventDefault();
                alert('Pilih minimal satu item untuk diupdate.');
                return false;
            }
            
            // Update hidden inputs before submission
            updateSelectedCount();
            
            // Confirm submission
            const itemStatus = document.getElementById('item_status').value;
            const statusLabel = document.getElementById('item_status').selectedOptions[0].text;
            
            // If GOODS_RECEIVED, ensure payment method selected
            if (itemStatus === 'GOODS_RECEIVED') {
                const pmId = document.getElementById('hidden_payment_method_id').value;
                if (!pmId) {
                    e.preventDefault();
                    openPaymentMethodModal(checkboxes);
                    return false;
                }
            }
            
            if (!confirm(`Apakah Anda yakin ingin mengupdate ${checkboxes.length} item menjadi status "${statusLabel}"?`)) {
                e.preventDefault();
                return false;
            }
        });

        // Toggle payment method container visibility when status changes
        document.getElementById('item_status')?.addEventListener('change', function() {
            const isGoodsReceived = this.value === 'GOODS_RECEIVED';
            const pmContainer = document.getElementById('paymentMethodContainer');
            if (pmContainer) {
                pmContainer.classList.toggle('hidden', !isGoodsReceived);
            }
        });

        // Payment method modal logic
        function openPaymentMethodModal(checkboxes) {
            let modal = document.getElementById('payment-method-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'payment-method-modal';
                modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
                modal.innerHTML = `
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-credit-card mr-2 text-emerald-600"></i>
                            Pilih Payment Method
                        </h3>
                        <div id="pm-items" class="max-h-40 overflow-auto mb-4 p-2 bg-gray-50 dark:bg-gray-700 rounded"></div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metode Pembayaran</label>
                            <select id="pm-select" class="w-full rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">
                                <option value="">Pilih Metode Pembayaran</option>
                                @foreach(\App\Models\Access_PR\PaymentMethod::where('is_active', true)->orderBy('name')->get() as $pm)
                                    <option value="{{ $pm->id }}">{{ $pm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" id="pm-cancel" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded">Batal</button>
                            <button type="button" id="pm-apply" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded">Terapkan</button>
                        </div>
                    </div>`;
                document.body.appendChild(modal);
                modal.addEventListener('click', (ev) => { if (ev.target === modal) closePaymentMethodModal(); });
                document.getElementById('pm-cancel').addEventListener('click', closePaymentMethodModal);
                document.getElementById('pm-apply').addEventListener('click', applyPaymentMethodSelection);
            }

            // Render selected items
            const container = document.getElementById('pm-items');
            container.innerHTML = '';
            checkboxes.forEach(cb => {
                const row = document.createElement('div');
                row.className = 'text-sm text-gray-700 dark:text-gray-200 py-1';
                row.textContent = `• ${cb.dataset.desc || 'Item'} (Qty: ${cb.dataset.qty || '-'})`;
                container.appendChild(row);
            });

            modal.classList.remove('hidden');
        }

        function closePaymentMethodModal() {
            const modal = document.getElementById('payment-method-modal');
            if (modal) modal.classList.add('hidden');
        }

        function applyPaymentMethodSelection() {
            const select = document.getElementById('pm-select');
            const val = select?.value;
            if (!val) {
                alert('Pilih metode pembayaran terlebih dahulu.');
                return;
            }
            document.getElementById('hidden_payment_method_id').value = val;
            closePaymentMethodModal();
            // Resubmit form after selection
            document.getElementById('bulkUpdateForm').requestSubmit();
        }

        // GA approval quantity input toggle
        function toggleQuantityInput(itemId) {
            const checkbox = document.getElementById(`item_${itemId}`);
            const quantityDiv = document.getElementById(`qty_input_${itemId}`);
            
            if (checkbox.checked) {
                quantityDiv.classList.remove('hidden');
            } else {
                quantityDiv.classList.add('hidden');
                // Clear the quantity input when unchecked
                const quantityInput = quantityDiv.querySelector('input[type="number"]');
                if (quantityInput) {
                    quantityInput.value = '';
                }
            }
        }

        // Initialize count on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCount();
        });

        // Close modals dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideApproveModal();
                hideRejectModal();
                hideUpdateStatusModal();
                hideImageModal();
                hideAddFileModal();
                hideDeleteFileModal();
                closeSimplifiedPurchasingModal();
            }
        });

        // Minimize/expand functionality for Process Items section
        function toggleProcessItemsSection() {
            const content = document.getElementById('processItemsContent');
            const chevronIcon = document.getElementById('processItemsToggle');
            
            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                chevronIcon.classList.remove('fa-chevron-down');
                chevronIcon.classList.add('fa-chevron-up');
            } else {
                content.classList.add('hidden');
                chevronIcon.classList.remove('fa-chevron-up');
                chevronIcon.classList.add('fa-chevron-down');
            }
        }

        // Simplified purchasing modal functions
        function showSimplifiedPurchasingModal() {
            document.getElementById('simplifiedPurchasingModal').classList.remove('hidden');
        }

        function closeSimplifiedPurchasingModal() {
            document.getElementById('simplifiedPurchasingModal').classList.add('hidden');
        }

        function toggleSimplePurchasingAction(itemId) {
            const actionSelect = document.getElementById(`simple_action_${itemId}`);
            const quantityDiv = document.getElementById(`simple_qty_input_${itemId}`);
            const reasonDiv = document.getElementById(`simple_reason_input_${itemId}`);
            
            // Hide all first
            quantityDiv.classList.add('hidden');
            reasonDiv.classList.add('hidden');
            
            if (actionSelect.value === 'partial') {
                quantityDiv.classList.remove('hidden');
            } else if (actionSelect.value === 'reject') {
                reasonDiv.classList.remove('hidden');
            }
        }
    </script>

    <!-- Simplified Purchasing Modal -->
    <div id="simplifiedPurchasingModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-5xl shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 dark:border-gray-600">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-shopping-cart mr-2 text-blue-600"></i>
                        Proses Items Purchasing
                    </h3>
                    <button onclick="closeSimplifiedPurchasingModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mt-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-3 mb-4">
                        <div class="text-sm text-blue-800 dark:text-blue-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span class="font-semibold">Alur Proses:</span>
                            </div>
                            <ul class="text-xs space-y-1 ml-4">
                                <li><strong>• Proses (Full):</strong> Item akan dimulai dengan status "Pencarian Vendor"</li>
                                <li><strong>• Sebagian:</strong> Qty yang disetujui mulai "Pencarian Vendor", sisanya tetap pending</li>
                                <li><strong>• Tolak:</strong> Item ditolak dengan alasan yang diberikan</li>
                                <li class="text-blue-600"><strong>• Selanjutnya:</strong> Gunakan "Update Status Barang (Bulk)" untuk lanjutkan proses vendor → PO → diterima</li>
                            </ul>
                        </div>
                    </div>

                    <form action="{{ route('purchase-request.purchasing-partial-approval', $purchaseRequest) }}" method="POST">
                        @csrf
                        <div class="max-h-80 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg">
                            @php
                                $finalStatuses = ['TERSEDIA_DI_GA', 'CLOSED', 'GOODS_RECEIVED', 'REJECTED'];
                                $purchasingItems = $purchaseRequest->items->whereNotIn('item_status', $finalStatuses);
                            @endphp
                            
                            @foreach($purchasingItems as $item)
                            <div class="p-4 border-b border-gray-100 dark:border-gray-600 {{ $loop->last ? 'border-b-0' : '' }}">
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-center">
                                    <!-- Item Info -->
                                    <div class="lg:col-span-1">
                                        <div class="font-medium text-gray-900 dark:text-gray-100">{{ $item->description }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Diminta: <span class="font-semibold">{{ $item->quantity }} {{ $item->unit }}</span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">Status: {{ $item->item_status_label }}</div>
                                    </div>
                                    
                                    <!-- Action Select -->
                                    <div class="lg:col-span-1">
                                        <select name="actions[{{ $item->id }}]" id="simple_action_{{ $item->id }}" 
                                            class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                            <option value="">-- Pilih Action --</option>
                                            <option value="approve">Proses (Full)</option>
                                            <option value="partial">Sebagian</option>
                                            <option value="reject">Tolak</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Quantity/Reason Input -->
                                    <div class="lg:col-span-2">
                                        <!-- Quantity Input for Partial -->
                                        <div id="simple_qty_input_{{ $item->id }}" class="hidden">
                                            <div class="flex items-center space-x-2">
                                                <input type="number" name="quantities[{{ $item->id }}]" 
                                                    min="1" max="{{ $item->quantity }}" 
                                                    class="flex-1 text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                                    placeholder="Qty yang bisa dipenuhi">
                                                <span class="text-sm text-gray-500">/ {{ $item->quantity }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Reason Input for Reject -->
                                        <div id="simple_reason_input_{{ $item->id }}" class="hidden">
                                            <input type="text" name="reasons[{{ $item->id }}]" 
                                                class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                                placeholder="Alasan penolakan (misal: tidak tersedia, discontinued, dll)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-4 border-t border-gray-200 dark:border-gray-600 pt-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Catatan Purchasing (Opsional)
                                </label>
                                <textarea name="purchasing_notes" rows="2" 
                                    class="w-full text-sm rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                                    placeholder="Catatan umum untuk proses ini (vendor info, estimasi waktu, dll)"></textarea>
                            </div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeSimplifiedPurchasingModal()" 
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg">
                                    Batal
                                </button>
                                <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg">
                                    <i class="fas fa-cog mr-2"></i>Proses Items
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>