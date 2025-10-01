<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Purchase Request') }} #{{ $purchaseRequest->pr_number }}
        </h2>
    </x-slot>

    <!-- Popup Notification -->
    @if (session('error'))
    <div id="notifPopup" class="notif-popup bg-red-500">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <div class="py-12">
        <div class="max-w-[90%] lg:max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form action="{{ route('purchase-request.update', $purchaseRequest) }}" method="POST" id="prForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Informasi Pemohon -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Informasi Pemohon</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="form-label">Nama</label>
                                            <input type="text" class="form-control" value="{{ $purchaseRequest->user->name }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" value="{{ $purchaseRequest->user->email }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Divisi</label>
                                            <input type="text" class="form-control" value="{{ ucfirst($purchaseRequest->user->level) }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Tanggal Request</label>
                                            <input type="text" class="form-control" value="{{ $purchaseRequest->request_date->format('d/m/Y') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Request -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Detail Request</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="location_id" class="form-label">Lokasi <span class="text-danger">*</span></label>
                                            <select name="location_id" id="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                                <option value="">Pilih Lokasi</option>
                                                @foreach($masterLocations as $location)
                                                    <option value="{{ $location->id }}" {{ (old('location_id', $purchaseRequest->location_id) == $location->id) ? 'selected' : '' }}>
                                                        {{ $location->name }} ({{ $location->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('location_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Kategori PR <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                                <option value="">Pilih Kategori</option>
                                                @foreach($prCategories as $category)
                                                    <option value="{{ $category->id }}" {{ (old('category_id', $purchaseRequest->category_id) == $category->id) ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="due_date" class="form-label">Jatuh Tempo (untuk PR pembayaran)</label>
                                            <input type="date" 
                                                   name="due_date" 
                                                   id="due_date" 
                                                   class="form-control @error('due_date') is-invalid @enderror"
                                                   value="{{ old('due_date', $purchaseRequest->due_date?->format('Y-m-d')) }}"
                                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                            @error('due_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="description" class="form-label">Keterangan Kebutuhan <span class="text-danger">*</span></label>
                                            <textarea name="description" 
                                                      id="description" 
                                                      class="form-control @error('description') is-invalid @enderror" 
                                                      rows="3" 
                                                      required
                                                      placeholder="Contoh: biaya konsultan sosmed OG periode Juli 2025">{{ old('description', $purchaseRequest->description) }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="notes" class="form-label">Catatan Tambahan</label>
                                            <textarea name="notes" 
                                                      id="notes" 
                                                      class="form-control @error('notes') is-invalid @enderror" 
                                                      rows="2" 
                                                      placeholder="Catatan tambahan (opsional)">{{ old('notes', $purchaseRequest->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Flow -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Alur Persetujuan <span class="text-danger">*</span></h6>
                                <small class="text-muted">Pilih siapa saja yang harus approve PR ini (urutan akan sesuai pilihan)</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @foreach($approvalLevels as $key => $label)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="form-check">
                                                <input type="checkbox" 
                                                       name="approval_flow[]" 
                                                       value="{{ $key }}" 
                                                       id="approval_{{ $key }}"
                                                       class="form-check-input"
                                                       {{ in_array($key, old('approval_flow', $purchaseRequest->approval_flow)) ? 'checked' : '' }}>
                                                <label for="approval_{{ $key }}" class="form-check-label">{{ $label }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('approval_flow')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Items -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Item yang Diminta <span class="text-danger">*</span></h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="addItem()">
                                    <i class="fas fa-plus me-1"></i>
                                    Tambah Item
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="itemsContainer">
                                    @if(old('items'))
                                        @foreach(old('items') as $index => $item)
                                            <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Deskripsi Item <span class="text-danger">*</span></label>
                                                        <textarea name="items[{{ $index }}][description]" 
                                                                  class="form-control @error('items.'.$index.'.description') is-invalid @enderror" 
                                                                  rows="2" 
                                                                  required>{{ $item['description'] ?? '' }}</textarea>
                                                        @error('items.'.$index.'.description')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Qty <span class="text-danger">*</span></label>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][quantity]" 
                                                               class="form-control @error('items.'.$index.'.quantity') is-invalid @enderror" 
                                                               min="1" 
                                                               required
                                                               value="{{ $item['quantity'] ?? '' }}">
                                                        @error('items.'.$index.'.quantity')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Satuan</label>
                                                        <input type="text" 
                                                               name="items[{{ $index }}][unit]" 
                                                               class="form-control @error('items.'.$index.'.unit') is-invalid @enderror" 
                                                               placeholder="pcs, kg, dll"
                                                               value="{{ $item['unit'] ?? '' }}">
                                                        @error('items.'.$index.'.unit')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Estimasi Harga</label>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][estimated_price]" 
                                                               class="form-control @error('items.'.$index.'.estimated_price') is-invalid @enderror" 
                                                               step="0.01" 
                                                               min="0"
                                                               value="{{ $item['estimated_price'] ?? '' }}">
                                                        @error('items.'.$index.'.estimated_price')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <label class="form-label">Catatan Item</label>
                                                        <input type="text" 
                                                               name="items[{{ $index }}][notes]" 
                                                               class="form-control @error('items.'.$index.'.notes') is-invalid @enderror"
                                                               placeholder="Catatan tambahan untuk item ini"
                                                               value="{{ $item['notes'] ?? '' }}">
                                                        @error('items.'.$index.'.notes')
                                                            <div class="invalid-feedback">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($purchaseRequest->items as $index => $item)
                                            <div class="item-row border rounded p-3 mb-3" data-index="{{ $index }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Deskripsi Item <span class="text-danger">*</span></label>
                                                        <textarea name="items[{{ $index }}][description]" 
                                                                  class="form-control" 
                                                                  rows="2" 
                                                                  required>{{ $item->description }}</textarea>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Qty <span class="text-danger">*</span></label>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][quantity]" 
                                                               class="form-control" 
                                                               min="1" 
                                                               required
                                                               value="{{ $item->quantity }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label">Satuan</label>
                                                        <input type="text" 
                                                               name="items[{{ $index }}][unit]" 
                                                               class="form-control" 
                                                               placeholder="pcs, kg, dll"
                                                               value="{{ $item->unit }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Estimasi Harga</label>
                                                        <input type="number" 
                                                               name="items[{{ $index }}][estimated_price]" 
                                                               class="form-control" 
                                                               step="0.01" 
                                                               min="0"
                                                               value="{{ $item->estimated_price }}">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <label class="form-label">&nbsp;</label>
                                                        <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <label class="form-label">Catatan Item</label>
                                                        <input type="text" 
                                                               name="items[{{ $index }}][notes]" 
                                                               class="form-control"
                                                               placeholder="Catatan tambahan untuk item ini"
                                                               value="{{ $item->notes }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                @error('items')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('purchase-request.show', $purchaseRequest) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>
                                        Kembali
                                    </a>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            Update PR
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let itemIndex = 100; // Menggunakan nilai tetap yang tinggi untuk menghindari konflik

        function addItem() {
            const container = document.getElementById('itemsContainer');
            const itemHtml = `
                <div class="item-row border rounded p-3 mb-3" data-index="${itemIndex}">
                    <div class="row">
                        <div class="col-md-4">
                            <label class="form-label">Deskripsi Item <span class="text-danger">*</span></label>
                            <textarea name="items[${itemIndex}][description]" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Qty <span class="text-danger">*</span></label>
                            <input type="number" name="items[${itemIndex}][quantity]" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Satuan</label>
                            <input type="text" name="items[${itemIndex}][unit]" class="form-control" placeholder="pcs, kg, dll">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Estimasi Harga</label>
                            <input type="number" name="items[${itemIndex}][estimated_price]" class="form-control" step="0.01" min="0">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeItem(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="form-label">Catatan Item</label>
                            <input type="text" name="items[${itemIndex}][notes]" class="form-control" placeholder="Catatan tambahan untuk item ini">
                        </div>
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
</x-app-layout>