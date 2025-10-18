<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Purchase Request') }}
            <a href="{{ route('purchase-request.create') }}" class="btn btn-primary float-end">
                <i class="fas fa-plus me-1"></i>
                Buat PR Baru
            </a>
        </h2>
    </x-slot>

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

            @php
            // Count PRs that need approval by current user
            $pendingApprovalsCount = $purchaseRequests->filter(function($pr) {
            return $pr->canBeApprovedByUser(auth()->user());
            })->count();

            // Count PRs that are APPROVED and need purchasing action
            $approvedPRsCount = 0;
            $isPurchasing = auth()->user()->divisi === 'PURCHASING' && in_array(auth()->user()->level, ['manager', 'spv', 'staff']);
            if ($isPurchasing) {
            $approvedPRsCount = $purchaseRequests->filter(function($pr) {
            return $pr->status === 'APPROVED';
            })->count();
            }

            // Get user's approval role for display
            $user = auth()->user();
            $approvalRole = '';
            $approvalDescription = '';

            if ($user->level === 'admin') {
            $approvalRole = 'Administrator';
            $approvalDescription = 'Anda dapat menyetujui semua level approval';
            } elseif ($user->level === 'ceo') {
            $approvalRole = 'CEO Approval';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval CEO';
            } elseif ($user->level === 'cfo') {
            $approvalRole = 'CFO Approval';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval CFO';
            } elseif ($user->level === 'manager') {
            $approvalRole = 'Department Head';
            $approvalDescription = 'Anda dapat menyetujui PR dari divisi ' . $user->divisi;
            } elseif ($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff'])) {
            $approvalRole = 'GA Approval';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval GA (HCGA Department)';
            } elseif ($user->divisi === 'FAT' && in_array($user->level, ['manager', 'spv'])) {
            $approvalRole = 'Finance Department';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval Finance Department (FAT Manager/SPV)';
            } elseif (stripos($user->name, 'CEO') !== false) {
            $approvalRole = 'CEO Approval';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval CEO';
            } elseif (stripos($user->name, 'CFO') !== false) {
            $approvalRole = 'CFO Approval';
            $approvalDescription = 'Anda dapat menyetujui PR yang membutuhkan approval CFO';
            } elseif ($isPurchasing) {
            $approvalRole = 'Purchasing Department';
            $approvalDescription = 'Anda bertanggung jawab untuk memproses PR yang sudah disetujui hingga status COMPLETED';
            }
            @endphp

            <!-- Notification for Pending Approvals -->
            @if($pendingApprovalsCount > 0)
            <div id="approvalNotification" class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-lg shadow-md relative">
                <!-- Close button positioned at top right -->
                <button onclick="dismissNotification()" class="absolute top-2 right-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 transition-colors p-1 rounded-full hover:bg-red-100 dark:hover:bg-red-900/30" title="Tutup notifikasi (akan muncul lagi setelah login kembali)">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <div class="flex items-start pr-8">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-500 animate-pulse">
                            <i class="fas fa-bell text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div>
                            <h3 class="text-red-800 dark:text-red-200 font-bold text-lg">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                {{ $pendingApprovalsCount }} Purchase Request Menunggu Approval Anda!
                            </h3>
                            <p class="text-red-700 dark:text-red-300 mt-1">
                                Sebagai <strong>{{ $approvalRole }}</strong>, {{ $approvalDescription }}
                            </p>
                            <p class="text-red-600 dark:text-red-400 text-sm mt-2">
                                PR yang memerlukan action Anda akan ditandai dengan badge
                                <span class="inline-block px-2 py-1 bg-red-600 text-white text-xs rounded animate-pulse border border-red-700 mx-1">
                                    <i class="fas fa-bell mr-1"></i>Perlu Action
                                </span>
                                di kolom Approval Level.
                            </p>
                            <p class="text-red-500 dark:text-red-300 text-xs mt-2 italic">
                                <i class="fas fa-info-circle mr-1"></i>
                                Notifikasi ini akan muncul kembali setiap kali Anda login untuk mengingatkan adanya PR yang perlu disetujui.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Notification for Purchasing Department - PR APPROVED -->
            @if($isPurchasing && $approvedPRsCount > 0)
            <div id="purchasingNotification" class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-md relative">
                <!-- Close button positioned at top right -->
                <button onclick="dismissPurchasingNotification()" class="absolute top-2 right-2 text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200 transition-colors p-1 rounded-full hover:bg-green-100 dark:hover:bg-green-900/30" title="Tutup notifikasi (akan muncul lagi setelah login kembali)">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <div class="flex items-start pr-8">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-500 animate-bounce">
                            <i class="fas fa-shopping-cart text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div>
                            <h3 class="text-green-800 dark:text-green-200 font-bold text-lg">
                                <i class="fas fa-check-circle mr-2"></i>
                                {{ $approvedPRsCount }} Purchase Request Siap untuk Diproses!
                            </h3>
                            <p class="text-green-700 dark:text-green-300 mt-1">
                                Sebagai <strong>{{ $approvalRole }}</strong>, {{ $approvalDescription }}
                            </p>
                            <p class="text-green-600 dark:text-green-400 text-sm mt-2">
                                PR dengan status <span class="inline-block px-2 py-1 bg-green-500 text-white text-xs rounded mx-1">APPROVED</span>
                                perlu diproses hingga status
                                <span class="inline-block px-2 py-1 bg-blue-500 text-white text-xs rounded mx-1">COMPLETED</span>
                            </p>
                            <div class="bg-green-100 dark:bg-green-800/50 p-3 rounded-lg mt-3">
                                <h4 class="text-green-800 dark:text-green-200 font-semibold text-sm mb-2">
                                    <i class="fas fa-clipboard-list mr-1"></i> Langkah-langkah Purchasing:
                                </h4>
                                <ol class="text-green-700 dark:text-green-300 text-xs space-y-1 list-decimal list-inside">
                                    <li>Buka detail PR dengan status APPROVED</li>
                                    <li>Review item-item yang dibutuhkan</li>
                                    <li>Lakukan purchasing/procurement</li>
                                    <li>Update status PR melalui tombol "Update Status" di halaman detail</li>
                                    <li>Ubah status menjadi COMPLETED setelah barang diterima</li>
                                </ol>
                            </div>
                            <p class="text-green-500 dark:text-green-300 text-xs mt-2 italic">
                                <i class="fas fa-info-circle mr-1"></i>
                                Notifikasi ini akan muncul kembali setiap kali Anda login untuk mengingatkan adanya PR yang perlu diproses.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Info Box about Approval Levels -->
            <div id="approvalInfoBox" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6 relative">
                <!-- Close button positioned at top right -->
                <button onclick="dismissInfoBox()" class="absolute top-2 right-2 text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-200 transition-colors p-1 rounded-full hover:bg-blue-100 dark:hover:bg-blue-900/30" title="Tutup info box (akan muncul lagi setelah login kembali)">
                    <i class="fas fa-times text-lg"></i>
                </button>

                <div class="flex items-start pr-8">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-500">
                            <i class="fas fa-info-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div>
                            <h4 class="text-blue-800 dark:text-blue-200 font-bold mb-3">Penjelasan Approval Level Purchase Request</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-shield-alt mr-1"></i>User
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">Wajib mengkomunikasikan ke Purchasing sebelum memilih approval</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-user-tie mr-1"></i>Department Head
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">Manager dari divisi yang sama dengan pembuat PR</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-building mr-1"></i>GA (General Affairs)
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">Staff, SPV, atau Manager dari divisi HCGA</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-calculator mr-1"></i>Finance Department
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">SPV atau Manager dari divisi FAT</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-crown mr-1"></i>CEO Approval
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">Chief Executive Officer</p>
                                </div>
                                <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-200 dark:border-gray-600">
                                    <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">
                                        <i class="fas fa-chart-line mr-1"></i>CFO Approval
                                    </h5>
                                    <p class="text-gray-700 dark:text-gray-300">Chief Financial Officer</p>
                                </div>

                            </div>
                            <p class="text-blue-500 dark:text-blue-300 text-xs mt-3 italic">
                                <i class="fas fa-info-circle mr-1"></i>
                                Info box ini akan muncul kembali setiap kali Anda login untuk membantu memahami sistem approval.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info untuk GA (existing info box - more specific and includes pending asset numbering) -->
            @if(auth()->user()->divisi === 'HCGA' && in_array(auth()->user()->level, ['manager', 'spv', 'staff']))
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <h4 class="text-green-800 dark:text-green-200 font-medium">Status GA Approver</h4>
                        <p class="text-green-700 dark:text-green-300 text-sm mt-1">
                            Anda dapat melihat: PR milik Anda sendiri, PR yang membutuhkan approval GA, dan semua PR yang masih dalam proses approval.
                            PR yang memerlukan action Anda akan ditandai dengan badge merah berkedip.
                        </p>
                        @if(isset($gaAssetPendingCount) && $gaAssetPendingCount > 0)
                        <div class="mt-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded p-3">
                            <p class="text-yellow-800 dark:text-yellow-200 text-sm">
                                <i class="fas fa-tasks mr-1"></i>
                                Ada <strong>{{ $gaAssetPendingCount }}</strong> PR yang memerlukan <strong>Action HCGA</strong>: pilih Generate Nomor Asset atau tandai <strong>Non-Asset GA</strong> per item (purchasing selesai).
                            </p>
                            <p class="text-yellow-700 dark:text-yellow-300 text-xs mt-1">Buka detail PR, pilih item: isi kode dasar (mis. A1) lalu Generate, atau klik Non-Asset GA.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <!-- Filter buttons could go here -->
                        <div>
                            <button type="button" class="btn btn-secondary">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>

                        <!-- Search Bar -->
                        <form method="GET" action="{{ route('purchase-request.index') }}" class="search-bar">
                            <input type="text" name="search" class="form-control" placeholder="Cari No. PR, Pemohon, atau Deskripsi" value="{{ request('search') }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>
                    </div>

                    <!-- Tabel Data PR -->
                    @if ($purchaseRequests->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">No. PR</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Tanggal</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Pemohon</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Deskripsi</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Status</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Approval Level</th>
                                    <th class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseRequests as $pr)
                                <tr>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                                        <span class="text-gray-900 dark:text-white font-semibold">{{ $pr->pr_number }}</span>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                                        <span class="text-gray-900 dark:text-white">{{ $pr->request_date->format('d/m/Y') }}</span>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                                        <div>
                                            <span class="text-gray-900 dark:text-white font-medium">{{ $pr->user->name }}</span>
                                            <br>
                                            <small class="text-gray-500 dark:text-gray-400">{{ $pr->user->email }}</small>
                                            <br>
                                            <small class="text-blue-600 dark:text-blue-400">{{ $pr->user->divisi ?? 'N/A' }} - {{ ucfirst($pr->user->level) }}</small>
                                        </div>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white">
                                        <div class="text-truncate" style="max-width: 200px;" title="{{ $pr->description }}">
                                            <span class="text-gray-900 dark:text-white">{{ $pr->description }}</span>
                                        </div>
                                        <small class="text-gray-500 dark:text-gray-400">{{ $pr->items->count() }} item(s)</small>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-center">
                                        @php
                                        $statusClass = match($pr->status) {
                                        'DRAFT' => 'bg-gray-500',
                                        'SUBMITTED' => 'bg-yellow-500',
                                        'APPROVED' => 'bg-green-500',
                                        'REJECTED' => 'bg-red-500',
                                        'COMPLETED' => 'bg-blue-500',
                                        default => 'bg-gray-500'
                                        };
                                        @endphp
                                        <div class="flex flex-col items-center space-y-1">
                                            <span class="inline-block px-2 py-1 text-white text-xs rounded {{ $statusClass }}">{{ $pr->status }}</span>

                                            @if($pr->status === 'APPROVED' && $isPurchasing)
                                            <span class="inline-block px-2 py-1 bg-red-600 text-white text-xs rounded animate-pulse border border-red-700 shadow-lg">
                                                <i class="fas fa-shopping-cart mr-1"></i>Perlu Proses
                                            </span>
                                            @endif
                                            @if(auth()->user()->divisi === 'HCGA' && in_array(auth()->user()->level, ['manager','spv','staff']))
                                                @if(isset($pr->needs_ga_action) && $pr->needs_ga_action)
                                                <span class="inline-block px-2 py-1 bg-yellow-500 text-white text-xs rounded animate-pulse border border-yellow-600">
                                                    <i class="fas fa-tasks mr-1"></i>Action HCGA
                                                </span>
                                                @elseif(isset($pr->has_any_assets) && $pr->has_any_assets)
                                                <span class="inline-block px-2 py-1 bg-green-600 text-white text-xs rounded">
                                                    <i class="fas fa-barcode mr-1"></i>Nomor Asset Ada
                                                </span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-center">
                                        @php
                                        $currentLevel = $pr->getCurrentApprovalLevel();
                                        $canApprove = $pr->canBeApprovedByUser(auth()->user());
                                        @endphp

                                        <div class="flex flex-col items-center space-y-1">
                                            @if($currentLevel)
                                                @if($currentLevel === 'tersedia_di_ga')
                                                <span class="inline-block px-2 py-1 bg-blue-500 text-white text-xs rounded">
                                                    Tersedia di GA
                                                </span>
                                                @else
                                                <span class="inline-block px-2 py-1 bg-yellow-500 text-white text-xs rounded">
                                                    {{ ucwords(str_replace('_', ' ', $currentLevel)) }}
                                                </span>
                                                @endif
                                            @else
                                            <span class="inline-block px-2 py-1 bg-green-500 text-white text-xs rounded">Fully Approved</span>
                                            @endif

                                            @if($canApprove)
                                            <span class="inline-block px-2 py-1 bg-red-600 text-white text-xs rounded animate-pulse border border-red-700 shadow-lg">
                                                <i class="fas fa-bell mr-1"></i>Perlu Action
                                            </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="border-collapse border border-gray-300 dark:border-gray-600 text-center">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('purchase-request.show', $pr) }}"
                                                class="inline-block px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white rounded transition-colors duration-200"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if(auth()->user()->divisi === 'HCGA' && in_array(auth()->user()->level, ['manager','spv','staff']))
                                                @if(isset($pr->needs_ga_action) && $pr->needs_ga_action)
                                                <a href="{{ route('purchase-request.show', $pr) }}#asset-section"
                                                   class="inline-block px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors duration-200"
                                                   title="Action HCGA">
                                                    <i class="fas fa-tasks"></i>
                                                </a>
                                                @endif
                                            @endif

                                            @if(isset($pr->has_asset_numbers) && $pr->has_asset_numbers)
                                            <a href="{{ route('purchase-request.show', $pr) }}#asset-section"
                                               class="inline-block px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded transition-colors duration-200"
                                               title="Lihat Nomor Asset">
                                                <i class="fas fa-barcode"></i>
                                            </a>
                                            @endif

                                            @if($pr->user_id === auth()->id() && $pr->status === 'DRAFT')
                                            <a href="{{ route('purchase-request.edit', $pr) }}"
                                                class="inline-block px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors duration-200"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(in_array(auth()->user()->level, ['admin']) ||
                                            ($pr->user_id === auth()->id() && $pr->status === 'DRAFT'))
                                            <form action="{{ route('purchase-request.destroy', $pr) }}"
                                                method="POST"
                                                class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus PR ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="inline-block px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded transition-colors duration-200"
                                                    title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="border-collapse border border-gray-300 dark:border-gray-600 text-center py-8">
                                        <div class="text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-inbox fa-3x mb-4"></i>
                                            <p>Belum ada Purchase Request</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($purchaseRequests->hasPages())
                    <div class="mt-4 justify-end">
                        {{ $purchaseRequests->links() }}
                    </div>
                    @endif
                    @else
                    <p class="text-center text-gray-500 dark:text-gray-400">Tidak ada data Purchase Request.</p>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        function dismissNotification() {
            const notification = document.getElementById('approvalNotification');
            if (notification) {
                notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    notification.remove();
                }, 300);

                // Save dismissal in sessionStorage (akan hilang saat logout/close browser)
                sessionStorage.setItem('approval_notification_dismissed', 'true');
            }
        }

        function dismissPurchasingNotification() {
            const notification = document.getElementById('purchasingNotification');
            if (notification) {
                notification.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    notification.remove();
                }, 300);

                // Save dismissal in sessionStorage (akan hilang saat logout/close browser)
                sessionStorage.setItem('purchasing_notification_dismissed', 'true');
            }
        }

        function dismissInfoBox() {
            const infoBox = document.getElementById('approvalInfoBox');
            if (infoBox) {
                infoBox.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                infoBox.style.opacity = '0';
                infoBox.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    infoBox.remove();
                }, 300);

                // Save dismissal in sessionStorage (akan hilang saat logout/close browser)
                sessionStorage.setItem('approval_info_dismissed', 'true');
            }
        }

        // Check if notifications were previously dismissed dalam session ini saja
        document.addEventListener('DOMContentLoaded', function() {
            // Check approval notification - gunakan sessionStorage
            if (sessionStorage.getItem('approval_notification_dismissed') === 'true') {
                const notification = document.getElementById('approvalNotification');
                if (notification) {
                    notification.style.display = 'none';
                }
            }

            // Check purchasing notification - gunakan sessionStorage
            if (sessionStorage.getItem('purchasing_notification_dismissed') === 'true') {
                const purchasingNotification = document.getElementById('purchasingNotification');
                if (purchasingNotification) {
                    purchasingNotification.style.display = 'none';
                }
            }

            // Check info box - gunakan sessionStorage  
            if (sessionStorage.getItem('approval_info_dismissed') === 'true') {
                const infoBox = document.getElementById('approvalInfoBox');
                if (infoBox) {
                    infoBox.style.display = 'none';
                }
            }
        });
    </script>
</x-app-layout>