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

    <style>
        .search-highlight {
            background-color: #fef3cd;
            padding: 2px 4px;
            border-radius: 3px;
        }
        
        .search-input-container {
            position: relative;
        }
        
        .search-input-container input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            border-color: #3b82f6;
        }
        
        .search-results-info {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
        }
        
        .dark .search-results-info {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            border: 1px solid #3b82f6;
        }
        
        .search-tips {
            font-size: 0.875rem;
            line-height: 1.25rem;
        }
        
        .search-stats {
            font-size: 0.75rem;
            font-weight: 500;
        }
    </style>

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
                                di kolom Approval Level dan <strong>ditampilkan paling awal</strong> di daftar.
                            </p>
                            <div class="bg-red-100 dark:bg-red-800/50 p-3 rounded-lg mt-3">
                                <p class="text-red-800 dark:text-red-200 text-sm font-medium">
                                    <i class="fas fa-sort-amount-down mr-1"></i> Urutan Tampilan:
                                </p>
                                <p class="text-red-700 dark:text-red-300 text-xs mt-1">
                                    1. PR yang perlu action Anda (paling atas, diurutkan tanggal terbaru) <br>
                                    2. PR lainnya (diurutkan berdasarkan tanggal terbaru)
                                </p>
                            </div>
                            <p class="text-red-500 dark:text-red-300 text-xs mt-2 italic">
                                <i class="fas fa-info-circle mr-1"></i>
                                Jumlah ini mencakup <strong>semua halaman</strong>, tidak hanya halaman saat ini. Notifikasi ini akan muncul kembali setiap kali Anda login.
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
                                dan <strong>ditampilkan paling awal</strong> di daftar.
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
                                Jumlah ini mencakup <strong>semua halaman</strong>, tidak hanya halaman saat ini. Notifikasi ini akan muncul kembali setiap kali Anda login untuk mengingatkan adanya PR yang perlu diproses.
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
                        <!-- Filter info and statistics -->
                        <div class="flex items-center space-x-4">
                            <!-- Filter Button -->
                            <button type="button" 
                                    class="btn {{ request('status_filter') || request('approval_filter') || request('show_needs_action') === 'false' ? 'btn-warning' : 'btn-outline-secondary' }} border border-gray-300 dark:border-gray-600"
                                    onclick="openFilterModal()" 
                                    title="Filter data berdasarkan status dan approval level">
                                <i class="fas fa-filter mr-1"></i>Filter
                                @if(request('status_filter') || request('approval_filter') || request('show_needs_action') === 'false')
                                <span class="ml-1 px-1 py-0.5 bg-orange-200 text-orange-800 text-xs rounded-full">
                                    Aktif
                                </span>
                                @endif
                            </button>
                            
                            @if(request('search'))
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded px-3 py-2">
                                <i class="fas fa-search text-blue-500 mr-2"></i>
                                <span class="text-blue-700 dark:text-blue-300 text-sm">
                                    Pencarian: <strong>"{{ request('search') }}"</strong>
                                </span>
                                <span class="text-blue-600 dark:text-blue-400 text-xs ml-2">
                                    ({{ $purchaseRequests->total() }} hasil)
                                </span>
                            </div>
                            @endif
                            
                            <div class="text-gray-600 dark:text-gray-400 text-sm">
                                Total: {{ $purchaseRequests->total() }} PR
                            </div>
                        </div>

                        <!-- Enhanced Search Bar -->
                        <div class="flex items-center space-x-2">
                            <form id="search-form" method="GET" action="{{ route('purchase-request.index') }}" class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control pr-10" 
                                           placeholder="Cari No. PR, Pemohon, Deskripsi, Item..." 
                                           value="{{ request('search') }}"
                                           style="min-width: 300px;">
                                    @if(request('search'))
                                    <a href="{{ route('purchase-request.index') }}" 
                                       class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"
                                       title="Hapus pencarian">
                                        <i class="fas fa-times"></i>
                                    </a>
                                    @else
                                    <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Hidden filter inputs -->
                                <input type="hidden" name="status_filter" id="status-filter-input" value="{{ request('status_filter') }}">
                                <input type="hidden" name="approval_filter" id="approval-filter-input" value="{{ request('approval_filter') }}">
                                <input type="hidden" name="show_needs_action" id="show-needs-action-input" value="{{ request('show_needs_action', 'true') }}">
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search mr-1"></i>Cari
                                </button>
                                @if(request('search'))
                                <a href="{{ route('purchase-request.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times mr-1"></i>Reset
                                </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    <!-- Search Tips -->
                    @if(request('search'))
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Tips pencarian:</strong> Gunakan kata kunci seperti nomor PR, nama pemohon, email, divisi, deskripsi PR, atau deskripsi item untuk hasil yang lebih spesifik.
                    </div>
                    @endif

                    <!-- Filter Info Display -->
                    @if(request('status_filter') || request('approval_filter') || request('show_needs_action') === 'false')
                    <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-filter text-amber-600"></i>
                                <span class="text-amber-800 dark:text-amber-200 font-medium">Filter Aktif:</span>
                                
                                @if(request('status_filter'))
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">
                                    Status: {{ ucfirst(str_replace('_', ' ', request('status_filter'))) }}
                                </span>
                                @endif
                                
                                @if(request('approval_filter'))
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded">
                                    Approval: {{ ucwords(str_replace('_', ' ', request('approval_filter'))) }}
                                </span>
                                @endif
                                
                                @if(request('show_needs_action') === 'false')
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">
                                    Semua Data (Termasuk yang tidak perlu action)
                                </span>
                                @endif
                            </div>
                            <a href="{{ route('purchase-request.index', ['search' => request('search')]) }}" 
                               class="text-amber-600 hover:text-amber-800 text-sm">
                                <i class="fas fa-times mr-1"></i>Hapus Filter
                            </a>
                        </div>
                    </div>
                    @endif

                    <!-- Filter Modal -->
                    <div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" style="background-color: rgba(0,0,0,0.5);">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full mx-4">
                                <!-- Modal Header -->
                                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-600 rounded-t-2xl">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        <i class="fas fa-filter mr-2 text-blue-500"></i>Filter Purchase Request
                                    </h3>
                                    <button type="button" onclick="closeFilterModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-full p-1 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i class="fas fa-times text-xl"></i>
                                    </button>
                                </div>

                                <!-- Modal Body -->
                                <div class="p-6">
                                    <form id="filter-form">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Status Filter -->
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                                                    <i class="fas fa-tag mr-2 text-green-500"></i>Filter Status
                                                </h4>
                                                <div class="space-y-2">
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="" class="mr-2" {{ !request('status_filter') ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">Semua Status</span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="draft" class="mr-2" {{ request('status_filter') === 'draft' ? 'checked' : '' }}>
                                                        <span class="inline-flex items-center">
                                                            <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full mr-2">DRAFT</span>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">Draft</span>
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="submitted" class="mr-2" {{ request('status_filter') === 'submitted' ? 'checked' : '' }}>
                                                        <span class="inline-flex items-center">
                                                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full mr-2">SUBMITTED</span>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">Submitted</span>
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="approved" class="mr-2" {{ request('status_filter') === 'approved' ? 'checked' : '' }}>
                                                        <span class="inline-flex items-center">
                                                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full mr-2">APPROVED</span>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">Approved</span>
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="rejected" class="mr-2" {{ request('status_filter') === 'rejected' ? 'checked' : '' }}>
                                                        <span class="inline-flex items-center">
                                                            <span class="px-2 py-1 bg-red-500 text-white text-xs rounded-full mr-2">REJECTED</span>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">Rejected</span>
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="status_filter" value="completed" class="mr-2" {{ request('status_filter') === 'completed' ? 'checked' : '' }}>
                                                        <span class="inline-flex items-center">
                                                            <span class="px-2 py-1 bg-blue-500 text-white text-xs rounded-full mr-2">COMPLETED</span>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300">Completed</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Approval Level Filter -->
                                            <div>
                                                <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                                                    <i class="fas fa-user-check mr-2 text-purple-500"></i>Filter Approval Level
                                                </h4>
                                                <div class="space-y-2">
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="" class="mr-2" {{ !request('approval_filter') ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">Semua Level</span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="department_head" class="mr-2" {{ request('approval_filter') === 'department_head' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-user-tie mr-1 text-blue-500"></i>Department Head
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="ga_approval" class="mr-2" {{ request('approval_filter') === 'ga_approval' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-building mr-1 text-green-500"></i>GA Approval
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="finance_department" class="mr-2" {{ request('approval_filter') === 'finance_department' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-calculator mr-1 text-yellow-500"></i>Finance Department
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="ceo_approval" class="mr-2" {{ request('approval_filter') === 'ceo_approval' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-crown mr-1 text-yellow-600"></i>CEO Approval
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="cfo_approval" class="mr-2" {{ request('approval_filter') === 'cfo_approval' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-chart-line mr-1 text-red-500"></i>CFO Approval
                                                        </span>
                                                    </label>
                                                    <label class="flex items-center">
                                                        <input type="radio" name="approval_filter" value="fully_approved" class="mr-2" {{ request('approval_filter') === 'fully_approved' ? 'checked' : '' }}>
                                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                                            <i class="fas fa-check-double mr-1 text-green-600"></i>Fully Approved
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Show Needs Action Toggle -->
                                        <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">
                                                        <i class="fas fa-bell mr-2 text-red-500"></i>Prioritas Data yang Perlu Action
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                                        Aktif: Tampilkan data yang perlu action di urutan teratas.<br>
                                                        Nonaktif: Tampilkan semua data tanpa prioritas khusus.
                                                    </p>
                                                </div>
                                                <label class="relative inline-flex items-center cursor-pointer">
                                                    <input type="checkbox" name="show_needs_action" value="true" class="sr-only peer" {{ request('show_needs_action', 'true') === 'true' ? 'checked' : '' }}>
                                                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                    <span id="toggle-label" class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">
                                                        Aktif
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- Modal Footer -->
                                <div class="flex items-center justify-between p-4 border-t border-gray-200 dark:border-gray-600 rounded-b-2xl">
                                    <button type="button" 
                                            onclick="resetFilters()" 
                                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 rounded-xl transition-colors">
                                        <i class="fas fa-undo mr-1"></i>Reset Filter
                                    </button>
                                    <div class="space-x-2">
                                        <button type="button" 
                                                onclick="closeFilterModal()" 
                                                class="px-4 py-2 text-gray-600 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-gray-300 rounded-xl transition-colors">
                                            Batal
                                        </button>
                                        <button type="button" 
                                                onclick="applyFilters()" 
                                                class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl transition-colors">
                                            <i class="fas fa-filter mr-1"></i>Terapkan Filter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
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

                                            @if($pr->user_id == auth()->id() && $pr->status === 'DRAFT')
                                            <a href="{{ route('purchase-request.edit', $pr) }}"
                                                class="inline-block px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded transition-colors duration-200"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if(in_array(auth()->user()->level, ['admin']) ||
                                            ($pr->user_id == auth()->id() && $pr->status === 'DRAFT'))
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
                                    <td colspan="7" class="border-collapse border border-gray-300 dark:border-gray-600 text-center py-12">
                                        <div class="empty-state rounded-lg mx-4 my-4 p-8">
                                            @if(request('search'))
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-search-minus fa-4x mb-4 text-gray-300 dark:text-gray-600"></i>
                                                <h3 class="text-lg font-semibold mb-2">Tidak Ada Hasil</h3>
                                                <p class="mb-4">Tidak ditemukan Purchase Request yang sesuai dengan pencarian "<strong>{{ request('search') }}</strong>"</p>
                                                <div class="space-y-2 text-sm">
                                                    <p>💡 <strong>Saran:</strong></p>
                                                    <ul class="text-left max-w-md mx-auto space-y-1">
                                                        <li>• Periksa ejaan kata kunci</li>
                                                        <li>• Gunakan kata kunci yang lebih umum</li>
                                                        <li>• Coba cari dengan nomor PR atau nama pemohon</li>
                                                        <li>• Gunakan sebagian kata saja</li>
                                                    </ul>
                                                </div>
                                                <div class="mt-6">
                                                    <a href="{{ route('purchase-request.index') }}" 
                                                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                                        <i class="fas fa-times mr-2"></i>Hapus Filter
                                                    </a>
                                                </div>
                                            </div>
                                            @else
                                            <div class="text-gray-500 dark:text-gray-400">
                                                <i class="fas fa-inbox fa-4x mb-4 text-gray-300 dark:text-gray-600"></i>
                                                <h3 class="text-lg font-semibold mb-2">Belum Ada Purchase Request</h3>
                                                <p class="mb-6">Belum ada Purchase Request yang dibuat. Mulai dengan membuat PR pertama Anda.</p>
                                                <a href="{{ route('purchase-request.create') }}" 
                                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                                    <i class="fas fa-plus mr-2"></i>Buat PR Baru
                                                </a>
                                            </div>
                                            @endif
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
        // Force refresh - Version 1.0.2 - DIRECT FIX
        console.log('Script loaded at:', new Date().toLocaleTimeString());
        
        // DIRECT IMMEDIATE FUNCTION FOR TOGGLE
        window.addEventListener('load', function() {
            console.log('Window fully loaded, setting up toggle immediately');
            setupToggle();
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up toggle via DOMContentLoaded');
            setTimeout(setupToggle, 100);
            setTimeout(setupToggle, 500);
            setTimeout(setupToggle, 1000);
        });
        
        function setupToggle() {
            const actionCheckbox = document.querySelector('input[type="checkbox"][name="show_needs_action"]');
            const toggleLabel = document.getElementById('toggle-label');
            
            console.log('Setup toggle - checkbox:', !!actionCheckbox, 'label:', !!toggleLabel);
            
            if (actionCheckbox && toggleLabel) {
                // Simple function to update text
                function updateText() {
                    console.log('Checkbox checked status:', actionCheckbox.checked);
                    console.log('Checkbox value:', actionCheckbox.value);
                    console.log('Checkbox type:', actionCheckbox.type);
                    
                    const newText = actionCheckbox.checked ? 'Aktif' : 'Nonaktif';
                    toggleLabel.textContent = newText;
                    console.log('Toggle text updated to:', newText);
                }
                
                // Set initial state
                updateText();
                
                // Add event listeners
                actionCheckbox.addEventListener('change', function(e) {
                    console.log('Change event triggered');
                    updateText();
                });
                
                actionCheckbox.addEventListener('click', function(e) {
                    console.log('Click event triggered');
                    setTimeout(updateText, 10);
                });
                
                // Also listen for click events on the toggle container
                const toggleContainer = toggleLabel.closest('.relative');
                if (toggleContainer) {
                    toggleContainer.addEventListener('click', function(e) {
                        console.log('Container clicked');
                        // Small delay to allow checkbox state to update
                        setTimeout(updateText, 50);
                    });
                }
            }
        }
        
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

            // Enhanced search functionality
            const searchForm = document.getElementById('search-form');
            const searchInput = searchForm ? searchForm.querySelector('input[name="search"]') : null;
            
            if (searchInput && searchForm) {
                // Auto-focus on search input when pressing '/' key
                document.addEventListener('keydown', function(e) {
                    if (e.key === '/' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        searchInput.focus();
                    }
                });

                // Clear search with Escape key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        searchInput.value = '';
                        searchInput.blur();
                    }
                });

                // Submit form on Enter - now properly targeted
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchForm.submit();
                    }
                });

                // Add loading state to search button
                searchForm.addEventListener('submit', function(e) {
                    const submitBtn = searchForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mencari...';
                        
                        // Reset button state if form submission fails
                        setTimeout(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-search mr-1"></i>Cari';
                        }, 5000);
                    }
                });
            }
        });

        // Clear search function
        function clearSearch() {
            const searchForm = document.getElementById('search-form');
            const searchInput = searchForm ? searchForm.querySelector('input[name="search"]') : null;
            if (searchInput) {
                searchInput.value = '';
                searchInput.focus();
            }
        }

        // Highlight search terms in results
        function highlightSearchTerms() {
            const searchTerm = '{{ request("search") }}';
            if (searchTerm) {
                const regex = new RegExp(`(${searchTerm.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
                const elements = document.querySelectorAll('td');
                
                elements.forEach(element => {
                    if (element.textContent && !element.querySelector('input') && !element.querySelector('button')) {
                        element.innerHTML = element.innerHTML.replace(regex, '<mark class="search-highlight">$1</mark>');
                    }
                });
            }
        }

        // Call highlight function after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            highlightSearchTerms();
        });

        // Filter Modal Functions
        function openFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; // Prevent background scroll
                
                // Focus on modal for accessibility
                modal.querySelector('.bg-white').focus();
            }
        }

        function closeFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scroll
            }
        }

        function resetFilters() {
            const form = document.getElementById('filter-form');
            if (form) {
                // Reset all radio buttons to default (first option)
                const statusRadios = form.querySelectorAll('input[name="status_filter"]');
                const approvalRadios = form.querySelectorAll('input[name="approval_filter"]');
                const actionCheckbox = form.querySelector('input[type="checkbox"][name="show_needs_action"]');
                
                if (statusRadios.length > 0) statusRadios[0].checked = true;
                if (approvalRadios.length > 0) approvalRadios[0].checked = true;
                if (actionCheckbox) actionCheckbox.checked = true;
                
                // Update toggle display
                setupToggle();
            }
        }

        function applyFilters() {
            const form = document.getElementById('filter-form');
            const searchForm = document.getElementById('search-form');
            
            if (form && searchForm) {
                // Get selected values
                const statusFilter = form.querySelector('input[name="status_filter"]:checked');
                const approvalFilter = form.querySelector('input[name="approval_filter"]:checked');
                const actionCheckbox = form.querySelector('input[type="checkbox"][name="show_needs_action"]');
                
                // Update hidden inputs in search form
                document.getElementById('status-filter-input').value = statusFilter ? statusFilter.value : '';
                document.getElementById('approval-filter-input').value = approvalFilter ? approvalFilter.value : '';
                document.getElementById('show-needs-action-input').value = actionCheckbox && actionCheckbox.checked ? 'true' : 'false';
                
                // Submit the search form
                searchForm.submit();
            }
        }

        function updateToggleDisplay() {
            // Simple call to setupToggle to refresh the display
            setupToggle();
        }

        // Filter Modal Functions
        function openFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                
                // Setup toggle when modal opens
                setTimeout(setupToggle, 200);
                
                // Focus on modal for accessibility
                const modalContent = modal.querySelector('.bg-white') || modal.querySelector('.relative');
                if (modalContent) modalContent.focus();
            }
        }

        function closeFilterModal() {
            const modal = document.getElementById('filterModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; // Restore scroll
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            const modal = document.getElementById('filterModal');
            if (modal && e.target === modal) {
                closeFilterModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('filterModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeFilterModal();
                }
            }
        });
    </script>
</x-app-layout>