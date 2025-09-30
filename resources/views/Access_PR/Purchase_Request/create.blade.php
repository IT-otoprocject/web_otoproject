<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Purchase Request Baru') }}
        </h2>
    </x-slot>

    <!-- Popup Notification -->
    @if (session('error'))
    <div id="notifPopup" class="notif-popup bg-red-500">
        <p>{{ session('error') }}</p>
    </div>
    @endif
    
    @if (session('success'))
    <div id="notifPopup" class="notif-popup bg-green-500">
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    @if ($errors->any())
    <div id="notifPopup" class="notif-popup bg-red-500">
        <p>Ada error dalam form:</p>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('purchase-request.store') }}" method="POST" id="prForm" class="space-y-6" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Informasi Pemohon -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-user-circle mr-2 text-blue-500"></i>
                                    Informasi Pemohon
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-not-allowed" 
                                               value="{{ auth()->user()->name }}" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                        <input type="email" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-not-allowed" 
                                               value="{{ auth()->user()->email }}" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Divisi</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-not-allowed" 
                                               value="{{ auth()->user()->divisi ?? 'N/A' }}" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Level</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-not-allowed" 
                                               value="{{ ucfirst(auth()->user()->level) }}" 
                                               readonly>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Request</label>
                                        <input type="text" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 cursor-not-allowed" 
                                               value="{{ date('d/m/Y') }}" 
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Request -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                    <i class="fas fa-clipboard-list mr-2 text-green-500"></i>
                                    Detail Request
                                </h3>
                                <div class="space-y-4">
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Lokasi <span class="text-red-500">*</span>
                                        </label>
                                        <select name="location" 
                                                id="location" 
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('location') border-red-500 @enderror" 
                                                required>
                                            <option value="">Pilih Lokasi</option>
                                            <option value="HQ" {{ old('location') === 'HQ' ? 'selected' : '' }}>HQ</option>
                                            <option value="BRANCH" {{ old('location') === 'BRANCH' ? 'selected' : '' }}>Branch</option>
                                            <option value="OTHER" {{ old('location') === 'OTHER' ? 'selected' : '' }}>Other</option>
                                        </select>
                                        @error('location')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Jatuh Tempo (untuk PR pembayaran)
                                        </label>
                                        <input type="date" 
                                               name="due_date" 
                                               id="due_date" 
                                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('due_date') border-red-500 @enderror"
                                               value="{{ old('due_date') }}"
                                               min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                        @error('due_date')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Keterangan Kebutuhan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="description" 
                                                  id="description" 
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror" 
                                                  rows="3" 
                                                  required
                                                  placeholder="Contoh: biaya konsultan sosmed OG periode Juli 2025">{{ old('description') }}</textarea>
                                        @error('description')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan</label>
                                        <textarea name="notes" 
                                                  id="notes" 
                                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" 
                                                  rows="2" 
                                                  placeholder="Catatan tambahan (opsional)">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Kategori PR -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-tags mr-2 text-purple-500"></i>
                                Kategori Purchase Request <span class="text-red-500 ml-1">*</span>
                            </h3>
                            
                            <!-- Hidden input untuk category_id -->
                            <input type="hidden" name="category_id" id="selected_category_id" value="{{ old('category_id') }}">
                            
                            <div id="category-not-selected" class="{{ old('category_id') ? 'hidden' : '' }}">
                                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
                                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Informasi:
                                    </h4>
                                    <p class="text-sm text-blue-800 dark:text-blue-200">
                                        Pilih kategori yang sesuai dengan jenis Purchase Request ini. 
                                        Alur persetujuan akan ditentukan otomatis berdasarkan kategori yang dipilih.
                                    </p>
                                </div>
                                
                                <button type="button" 
                                        class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 flex items-center justify-center"
                                        onclick="openCategoryModal()">
                                    <i class="fas fa-plus mr-2"></i>
                                    Pilih Kategori PR
                                </button>
                            </div>
                            
                            <div id="category-selected" class="{{ old('category_id') ? '' : 'hidden' }}">
                                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="text-sm font-medium text-green-900 dark:text-green-100 mb-2">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Kategori Terpilih:
                                            </h4>
                                            <div id="selected-category-details">
                                                <!-- Details akan diisi oleh JavaScript -->
                                            </div>
                                            <div class="flex flex-wrap gap-1 mt-3" id="selected-approval-flow">
                                                <!-- Approval flow akan diisi oleh JavaScript -->
                                            </div>
                                        </div>
                                        <button type="button" 
                                                class="ml-4 px-3 py-1 bg-orange-600 hover:bg-orange-700 text-white text-sm rounded transition-colors duration-200"
                                                onclick="openCategoryModal()">
                                            <i class="fas fa-edit mr-1"></i>
                                            Ganti
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Attachment Upload -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-paperclip mr-2 text-indigo-500"></i>
                                Lampiran File (Opsional)
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label for="attachments" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Upload File (foto/PDF, maksimal 2MB per file)
                                    </label>
                                    <input type="file" 
                                           name="attachments[]" 
                                           id="attachments" 
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('attachments') border-red-500 @enderror"
                                           accept=".jpg,.jpeg,.png,.pdf"
                                           multiple
                                           onchange="validateFileSize(this)">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Format yang didukung: JPG, JPEG, PNG, PDF. Maksimal 2MB per file.
                                    </p>
                                    @error('attachments')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                    @error('attachments.*')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div id="file-preview" class="space-y-2"></div>
                                <div id="clear-files-container" class="hidden">
                                    <button type="button" 
                                            class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md transition-colors duration-200"
                                            onclick="clearAllFiles()">
                                        <i class="fas fa-times mr-2"></i>
                                        Hapus Semua File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                                    <i class="fas fa-boxes mr-2 text-orange-500"></i>
                                    Item yang Diminta <span class="text-red-500 ml-1">*</span>
                                </h3>
                                <button type="button" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200"
                                        onclick="addItem()">
                                    <i class="fas fa-plus mr-2"></i>
                                    Tambah Item
                                </button>
                            </div>
                            
                            <!-- Total Estimasi -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-yellow-900 dark:text-yellow-100 mb-2">
                                    <i class="fas fa-calculator mr-1"></i>
                                    Total Estimasi Harga:
                                </h4>
                                <div class="text-lg font-bold text-yellow-900 dark:text-yellow-100" id="totalEstimation">
                                    Rp 0
                                </div>
                                <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                    Total akan dihitung otomatis berdasarkan estimasi harga item. CEO approval dapat dipilih jika total > Rp 5.000.000
                                </p>
                            </div>
                            
                            <div id="itemsContainer" class="space-y-4">
                                @if(old('items'))
                                    @foreach(old('items') as $index => $item)
                                        <div class="item-row bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-index="{{ $index }}">
                                            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                                <div class="md:col-span-2">
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Deskripsi Item <span class="text-red-500">*</span>
                                                    </label>
                                                    <textarea name="items[{{ $index }}][description]" 
                                                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items.'.$index.'.description') border-red-500 @enderror" 
                                                              rows="2" 
                                                              required>{{ $item['description'] ?? '' }}</textarea>
                                                    @error('items.'.$index.'.description')
                                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Qty <span class="text-red-500">*</span>
                                                    </label>
                                                    <input type="number" 
                                                           name="items[{{ $index }}][quantity]" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items.'.$index.'.quantity') border-red-500 @enderror" 
                                                           min="1" 
                                                           required
                                                           value="{{ $item['quantity'] ?? '' }}">
                                                    @error('items.'.$index.'.quantity')
                                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Satuan</label>
                                                    <input type="text" 
                                                           name="items[{{ $index }}][unit]" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items.'.$index.'.unit') border-red-500 @enderror" 
                                                           placeholder="pcs, kg, dll"
                                                           value="{{ $item['unit'] ?? '' }}">
                                                    @error('items.'.$index.'.unit')
                                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimasi Harga</label>
                                                    <input type="number" 
                                                           name="items[{{ $index }}][estimated_price]" 
                                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items.'.$index.'.estimated_price') border-red-500 @enderror" 
                                                           step="0.01" 
                                                           min="0"
                                                           value="{{ $item['estimated_price'] ?? '' }}"
                                                           onchange="calculateTotal()">
                                                    @error('items.'.$index.'.estimated_price')
                                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                                <div class="flex items-end">
                                                    <button type="button" 
                                                            class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-200" 
                                                            onclick="removeItem(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Item</label>
                                                <input type="text" 
                                                       name="items[{{ $index }}][notes]" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('items.'.$index.'.notes') border-red-500 @enderror"
                                                       placeholder="Catatan tambahan untuk item ini"
                                                       value="{{ $item['notes'] ?? '' }}">
                                                @error('items.'.$index.'.notes')
                                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="item-row bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-index="0">
                                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Deskripsi Item <span class="text-red-500">*</span>
                                                </label>
                                                <textarea name="items[0][description]" 
                                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                          rows="2" 
                                                          required></textarea>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Qty <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number" 
                                                       name="items[0][quantity]" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       min="1" 
                                                       required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Satuan</label>
                                                <input type="text" 
                                                       name="items[0][unit]" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       placeholder="pcs, kg, dll">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimasi Harga</label>
                                                <input type="number" 
                                                       name="items[0][estimated_price]" 
                                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                       step="0.01" 
                                                       min="0"
                                                       onchange="calculateTotal()">
                                            </div>
                                            <div class="flex items-end">
                                                <button type="button" 
                                                        class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-200" 
                                                        onclick="removeItem(this)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Item</label>
                                            <input type="text" 
                                                   name="items[0][notes]" 
                                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                   placeholder="Catatan tambahan untuk item ini">
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @error('items')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-4">
                                <a href="{{ route('purchase-request.index') }}" 
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-md transition-colors duration-200">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    Kembali
                                </a>
                                <button type="submit" 
                                        id="submitBtn"
                                        class="inline-flex items-center px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors duration-200">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Submit PR
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pilih Kategori -->
    <div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        <i class="fas fa-tags mr-2 text-purple-500"></i>
                        Pilih Kategori Purchase Request
                    </h3>
                    <button type="button" 
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            onclick="closeCategoryModal()">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                @if($prCategories->count() > 0)
                    <div class="max-h-96 overflow-y-auto space-y-3">
                        @foreach($prCategories as $category)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors cursor-pointer category-option"
                                 data-category-id="{{ $category->id }}"
                                 data-category-name="{{ $category->name }}"
                                 data-category-description="{{ $category->description ?? '' }}"
                                 data-approval-rules="{{ json_encode($category->approval_rules) }}"
                                 onclick="selectCategoryFromData(this)">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-1">
                                            {{ $category->name }}
                                        </h4>
                                        @if($category->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                {{ $category->description }}
                                            </p>
                                        @endif
                                        <div class="flex flex-wrap gap-1">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">Alur persetujuan:</span>
                                            @foreach($category->approval_rules as $rule)
                                                <span class="inline-block px-2 py-1 bg-green-100 dark:bg-green-800 text-green-800 dark:text-green-200 text-xs rounded">
                                                    {{ $approvalLevels[$rule] ?? $rule }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <i class="fas fa-arrow-right text-blue-500"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="text-gray-500 dark:text-gray-400 mb-2">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <p class="text-gray-600 dark:text-gray-400">
                            Belum ada kategori PR yang tersedia. 
                            Silakan hubungi FAT Manager atau SPV untuk membuat kategori.
                        </p>
                    </div>
                @endif
                
                <div class="flex justify-end mt-6">
                    <button type="button" 
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-md transition-colors duration-200"
                            onclick="closeCategoryModal()">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let itemIndex = 1;
        const approvalLevels = @json($approvalLevels);

        // Debug form submission
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, attaching form handler');
            
            const form = document.getElementById('prForm');
            if (!form) {
                console.error('Form not found!');
                return;
            }
            
            console.log('Form found:', form);
            
            form.addEventListener('submit', function(e) {
                console.log('Form submission started');
                
                // Add more debugging
                const formData = new FormData(this);
                console.log('Form data:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }
                
                // Validate category selection
                const selectedCategory = document.getElementById('selected_category_id').value;
                console.log('Selected category:', selectedCategory);
                
                if (!selectedCategory) {
                    e.preventDefault();
                    console.log('No category selected');
                    alert('Pilih kategori PR terlebih dahulu!');
                    return false;
                }
                
                // Validate items
                const itemRows = document.querySelectorAll('.item-row');
                console.log('Item rows found:', itemRows.length);
                let hasValidItem = false;
                
                itemRows.forEach(function(row, index) {
                    const description = row.querySelector('textarea[name*="[description]"]');
                    const quantity = row.querySelector('input[name*="[quantity]"]');
                    
                    console.log(`Item ${index}:`, {
                        description: description ? description.value : 'not found',
                        quantity: quantity ? quantity.value : 'not found'
                    });
                    
                    if (description && description.value.trim() !== '' && quantity && quantity.value > 0) {
                        hasValidItem = true;
                    }
                });
                
                console.log('Has valid item:', hasValidItem);
                
                if (!hasValidItem) {
                    e.preventDefault();
                    console.log('No valid items');
                    alert('Minimal harus ada satu item yang valid!');
                    return false;
                }
                
                console.log('Form validation passed, submitting...');
                console.log('Form action:', this.action);
                console.log('Form method:', this.method);
                
                // Let the form submit naturally
                return true;
            });
        });

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
                <div class="item-row bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-4" data-index="${itemIndex}">
                    <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Deskripsi Item <span class="text-red-500">*</span>
                            </label>
                            <textarea name="items[${itemIndex}][description]" 
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                      rows="2" 
                                      required></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Qty <span class="text-red-500">*</span>
                            </label>
                            <input type="number" 
                                   name="items[${itemIndex}][quantity]" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   min="1" 
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Satuan</label>
                            <input type="text" 
                                   name="items[${itemIndex}][unit]" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="pcs, kg, dll">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estimasi Harga</label>
                            <input type="number" 
                                   name="items[${itemIndex}][estimated_price]" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   step="0.01" 
                                   min="0"
                                   onchange="calculateTotal()">
                        </div>
                        <div class="flex items-end">
                            <button type="button" 
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition-colors duration-200" 
                                    onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Item</label>
                        <input type="text" 
                               name="items[${itemIndex}][notes]" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="Catatan tambahan untuk item ini">
                    </div>
                </div>
            `;
            
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemIndex++;
        }

        function removeItem(button) {
            const itemRow = button.closest('.item-row');
            const container = document.getElementById('itemsContainer');
            
            if (container.children.length > 1) {
                itemRow.remove();
                calculateTotal(); // Recalculate total after removing item
            } else {
                alert('Minimal harus ada 1 item');
            }
        }

        // Calculate total estimation and update CEO approval requirement
        function calculateTotal() {
            let total = 0;
            const estimatedPriceInputs = document.querySelectorAll('input[name*="[estimated_price]"]');
            
            estimatedPriceInputs.forEach(function(input) {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            
            // Update total display
            const totalElement = document.getElementById('totalEstimation');
            totalElement.textContent = 'Rp ' + total.toLocaleString('id-ID');
            
            // Update CEO checkbox based on total
            const ceoCheckbox = document.getElementById('approval_ceo');
            if (ceoCheckbox) {
                if (total <= 5000000) {
                    // Total <= 5 juta, disable CEO checkbox
                    ceoCheckbox.disabled = true;
                    ceoCheckbox.checked = false;
                    ceoCheckbox.closest('.flex').style.opacity = '0.5';
                    ceoCheckbox.closest('.flex').title = 'CEO approval tidak diperlukan untuk total estimasi ≤ Rp 5.000.000';
                } else {
                    // Total > 5 juta, enable CEO checkbox (opsional, tidak wajib)
                    ceoCheckbox.disabled = false;
                    ceoCheckbox.closest('.flex').style.opacity = '1';
                    ceoCheckbox.closest('.flex').title = 'CEO approval opsional untuk total estimasi > Rp 5.000.000';
                    // Don't auto-check CEO, let user decide
                }
            }
        }

        // Validate file size and type
        function validateFileSize(input) {
            const files = input.files;
            const maxSize = 2 * 1024 * 1024; // 2MB in bytes
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            const previewContainer = document.getElementById('file-preview');
            const clearContainer = document.getElementById('clear-files-container');
            
            previewContainer.innerHTML = ''; // Clear previous previews
            
            if (files.length === 0) {
                clearContainer.classList.add('hidden');
                return;
            }
            
            let validFiles = [];
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                
                // Check file type
                if (!allowedTypes.includes(file.type)) {
                    alert(`File ${file.name} tidak didukung. Hanya JPG, JPEG, PNG, dan PDF yang diperbolehkan.`);
                    continue;
                }
                
                // Check file size
                if (file.size > maxSize) {
                    alert(`File ${file.name} terlalu besar. Maksimal ukuran file adalah 2MB.`);
                    continue;
                }
                
                validFiles.push(file);
                
                // Create file preview
                const filePreview = document.createElement('div');
                filePreview.className = 'flex items-center justify-between p-3 bg-gray-100 dark:bg-gray-700 rounded-md border';
                
                const fileIcon = file.type.includes('image') ? '🖼️' : '📄';
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                
                filePreview.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <span class="text-lg">${fileIcon}</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">${file.name}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">${fileSize} MB</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-green-500 text-sm">✓ Valid</span>
                        <button type="button" 
                                class="text-red-600 hover:text-red-800 text-sm"
                                onclick="removeFile(this, ${i})"
                                title="Hapus file ini">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                
                previewContainer.appendChild(filePreview);
            }
            
            if (validFiles.length > 0) {
                clearContainer.classList.remove('hidden');
            } else {
                clearContainer.classList.add('hidden');
                input.value = ''; // Clear input if no valid files
            }
            
            return validFiles.length > 0;
        }

        // Clear all files
        function clearAllFiles() {
            const input = document.getElementById('attachments');
            const previewContainer = document.getElementById('file-preview');
            const clearContainer = document.getElementById('clear-files-container');
            
            input.value = '';
            previewContainer.innerHTML = '';
            clearContainer.classList.add('hidden');
        }

        // Remove individual file (note: this is just UI, the actual file removal needs backend handling)
        function removeFile(button, index) {
            button.closest('.flex').remove();
            
            // Check if there are still files in preview
            const previewContainer = document.getElementById('file-preview');
            const clearContainer = document.getElementById('clear-files-container');
            
            if (previewContainer.children.length === 0) {
                clearContainer.classList.add('hidden');
                document.getElementById('attachments').value = '';
            }
        }

        // Modal functions
        function openCategoryModal() {
            document.getElementById('categoryModal').classList.remove('hidden');
        }

        function closeCategoryModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function selectCategoryFromData(element) {
            const categoryId = element.getAttribute('data-category-id');
            const categoryName = element.getAttribute('data-category-name');
            const categoryDescription = element.getAttribute('data-category-description');
            const approvalRules = JSON.parse(element.getAttribute('data-approval-rules'));
            
            selectCategory(categoryId, categoryName, categoryDescription, approvalRules);
        }

        function selectCategory(categoryId, categoryName, categoryDescription, approvalRules) {
            // Set hidden input
            document.getElementById('selected_category_id').value = categoryId;
            
            // Update category details
            const detailsContainer = document.getElementById('selected-category-details');
            detailsContainer.innerHTML = `
                <h5 class="font-medium text-green-900 dark:text-green-100">${categoryName}</h5>
                ${categoryDescription ? `<p class="text-sm text-green-700 dark:text-green-300 mt-1">${categoryDescription}</p>` : ''}
            `;
            
            // Update approval flow display
            const approvalFlowContainer = document.getElementById('selected-approval-flow');
            
            let approvalFlowHtml = '<span class="text-xs text-green-600 dark:text-green-400 mr-2">Alur persetujuan:</span>';
            approvalRules.forEach(function(rule) {
                const levelName = approvalLevels[rule] || rule;
                approvalFlowHtml += `<span class="inline-block px-2 py-1 bg-green-200 dark:bg-green-700 text-green-800 dark:text-green-200 text-xs rounded mr-1 mb-1">${levelName}</span>`;
            });
            
            approvalFlowContainer.innerHTML = approvalFlowHtml;
            
            // Show/hide sections
            document.getElementById('category-not-selected').classList.add('hidden');
            document.getElementById('category-selected').classList.remove('hidden');
            
            // Close modal
            closeCategoryModal();
        }

        // Initialize calculations on page load
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal(); // Calculate initial total
            
            // Add event listeners to existing estimated price inputs
            const existingInputs = document.querySelectorAll('input[name*="[estimated_price]"]');
            existingInputs.forEach(function(input) {
                input.addEventListener('change', calculateTotal);
            });
            
            // Initialize category selection if there's an old value
            const selectedCategoryId = document.getElementById('selected_category_id').value;
            if (selectedCategoryId) {
                // If there's an old value, we need to populate the category details
                // This would typically happen on form validation errors
                // For now, just show the selected section
                document.getElementById('category-not-selected').classList.add('hidden');
                document.getElementById('category-selected').classList.remove('hidden');
            }
        });
    </script>
    
    <style>
        .notif-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            z-index: 9999;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .notif-popup.bg-red-500 {
            background-color: #ef4444;
        }
        
        .notif-popup.bg-green-500 {
            background-color: #10b981;
        }
    </style>
</x-app-layout>
