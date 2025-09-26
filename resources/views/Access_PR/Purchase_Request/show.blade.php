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
                                    <span class="text-sm text-gray-900 dark:text-gray-100">: {{ $purchaseRequest->location }}</span>
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
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-list-ul mr-2 text-blue-500"></i>
                            Items yang Diminta
                        </h3>

                        <!-- Desktop View -->
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Deskripsi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Qty</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Satuan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Harga Est.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                    @foreach($purchaseRequest->items as $index => $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
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
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                    @if($purchaseRequest->items->whereNotNull('estimated_price')->count() > 0)
                                    <tr class="bg-blue-50 dark:bg-blue-900/20 border-t-2 border-blue-200 dark:border-blue-700">
                                        <td colspan="4" class="px-4 py-3 text-sm font-semibold text-gray-900 dark:text-gray-100">Total Estimasi</td>
                                        <td colspan="2" class="px-4 py-3 text-sm font-bold text-blue-600 dark:text-blue-400">
                                            Rp {{ number_format($purchaseRequest->items->sum('estimated_price'), 0, ',', '.') }}
                                        </td>
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
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Harga Est.:</span>
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                @if($item->estimated_price)
                                                Rp {{ number_format($item->estimated_price, 0, ',', '.') }}
                                                @else
                                                <span class="text-gray-400">-</span>
                                                @endif
                                            </p>
                                        </div>
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
                                        Rp {{ number_format($purchaseRequest->items->sum('estimated_price'), 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status Updates (untuk Purchasing) -->
                    @if($purchaseRequest->statusUpdates->count() > 0 || $canUpdateStatus)
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6 mb-6">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2 sm:mb-0 flex items-center">
                                <i class="fas fa-clipboard-list mr-2 text-orange-500"></i>
                                Update Status Purchasing
                            </h3>
                            @if($canUpdateStatus)
                            <button type="button"
                                onclick="showUpdateStatusModal()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Update
                            </button>
                            @endif
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
                                @if($purchaseRequest->user_id === auth()->id() && $purchaseRequest->status === 'DRAFT')
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
    </div>

    <!-- Approve Modal -->
    @if($canApprove)
    <div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700">
            <div class="mt-3">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 dark:bg-green-900 rounded-full">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white text-center mt-4">Setujui Purchase Request</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center mt-2">
                    Anda akan menyetujui Purchase Request <strong>#{{ $purchaseRequest->pr_number }}</strong>
                </p>

                <div class="mt-6">
                    <form action="{{ route('purchase-request.approve', $purchaseRequest) }}" method="POST">
                        @csrf
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
        });

        // Close modals dengan ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideApproveModal();
                hideRejectModal();
                hideUpdateStatusModal();
            }
        });
    </script>
</x-app-layout>