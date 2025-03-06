import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// menambahkan fungsi untuk tombol "add item"
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addItem').addEventListener('click', function() {
        const itemContainer = document.getElementById('itemContainer');
        
        const itemGroup = document.createElement('div');
        itemGroup.className = 'item-group';
        
        const namaBarangGroup = document.createElement('div');
        namaBarangGroup.className = 'form-group';
        const namaBarangLabel = document.createElement('label');
        namaBarangLabel.textContent = 'Nama Barang:';
        const namaBarangInput = document.createElement('input');
        namaBarangInput.type = 'text';
        namaBarangInput.name = 'nama_barang[]';
        namaBarangInput.required = true;
        
        namaBarangGroup.appendChild(namaBarangLabel);
        namaBarangGroup.appendChild(namaBarangInput);
        
        const qtyGroup = document.createElement('div');
        qtyGroup.className = 'form-group';
        const qtyLabel = document.createElement('label');
        qtyLabel.textContent = 'Quantity:';
        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.name = 'qty[]';
        qtyInput.required = true;
        
        qtyGroup.appendChild(qtyLabel);
        qtyGroup.appendChild(qtyInput);
        
        itemGroup.appendChild(namaBarangGroup);
        itemGroup.appendChild(qtyGroup);
        
        itemContainer.appendChild(itemGroup);
    });
});

