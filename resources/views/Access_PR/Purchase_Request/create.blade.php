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

                    <form action="{{ route('purchase-request.store') }}" method="POST" id="prForm" class="space-y-6">
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

                        <!-- Approval Flow -->
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4 flex items-center">
                                <i class="fas fa-route mr-2 text-purple-500"></i>
                                Alur Persetujuan <span class="text-red-500 ml-1">*</span>
                            </h3>
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-blue-900 dark:text-blue-100 mb-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Default untuk divisi {{ auth()->user()->divisi ?? 'N/A' }}:
                                </h4>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($defaultApprovalFlow as $level)
                                        <span class="inline-block px-2 py-1 bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200 text-xs rounded">
                                            {{ $approvalLevels[$level] ?? $level }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih siapa saja yang harus approve PR ini (Silahkan konfirmasi kepada Purchasing siapa saja yang harus Approve)</p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($approvalLevels as $key => $label)
                                    <div class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <input type="checkbox" 
                                               name="approval_flow[]" 
                                               value="{{ $key }}" 
                                               id="approval_{{ $key }}"
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600 rounded"
                                               {{ in_array($key, old('approval_flow', $defaultApprovalFlow)) ? 'checked' : '' }}>
                                        <label for="approval_{{ $key }}" class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('approval_flow')
                                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                            @enderror
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
                                                           value="{{ $item['estimated_price'] ?? '' }}">
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
                                                       min="0">
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

    <!-- Scripts -->
    <script>
        let itemIndex = 1;

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
                
                // Validate approval flow
                const checkedApprovals = document.querySelectorAll('input[name="approval_flow[]"]:checked');
                console.log('Checked approvals:', checkedApprovals.length);
                
                if (checkedApprovals.length === 0) {
                    e.preventDefault();
                    console.log('No approval flow selected');
                    alert('Pilih minimal satu approval flow!');
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
                                   min="0">
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
            } else {
                alert('Minimal harus ada 1 item');
            }
        }
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
