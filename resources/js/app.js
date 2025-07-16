import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

// Flash message auto-dismiss handler
document.addEventListener('DOMContentLoaded', function() {
    const notifPopups = document.querySelectorAll('.notif-popup');
    notifPopups.forEach(popup => {
        setTimeout(() => {
            popup.style.opacity = '0';
            setTimeout(() => {
                popup.remove();
            }, 300);
        }, 3000);
    });
});

// Tambah Barang & Hapus Barang (nama, sku, qty, satu form saja)
document.addEventListener('DOMContentLoaded', function () {
    const addItemButton = document.getElementById('addItem');
    const itemContainer = document.getElementById('itemContainer');

    if (addItemButton && itemContainer) {
        addItemButton.addEventListener('click', function () {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td class="custom-td text-gray-900 dark:text-white">
                    <div class="product-input-container">
                        <textarea name="nama_barang[]" class="form-control w-full dark:text-white dark:bg-gray-700 product-input" placeholder="Nama Product" required autocomplete="off" style="min-height:38px;resize:vertical;overflow-y:auto;"></textarea>
                        <div class="product-dropdown hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </td>
                <td class="custom-td text-gray-900 dark:text-white">
                    <input type="text" name="sku[]" class="form-control w-full dark:text-white dark:bg-gray-700 sku-input" placeholder="SKU" readonly>
                </td>
                <td class="custom-td text-gray-900 dark:text-white">
                    <input type="number" name="qty[]" class="form-control dark:text-white dark:bg-gray-700" placeholder="Qty" style="width: 80px;" required>
                </td>
                <td class="custom-td text-center">
                    <button type="button" class="btn btn-outline-danger removeItem">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            itemContainer.appendChild(newRow);

            // Attach autocomplete jika ada fungsi attachProductAutocomplete
            if (typeof attachProductAutocomplete === "function") {
                const input = newRow.querySelector('.product-input');
                if (input) attachProductAutocomplete(input);
            }
        });

        itemContainer.addEventListener('click', function (event) {
            if (event.target.closest('.removeItem')) {
                // Jangan hapus jika hanya tersisa satu baris
                if (itemContainer.children.length > 1) {
                    event.target.closest('tr').remove();
                }
            }
        });
    }
});

// Untuk menampilkan Konfirmasi dan popup beberapa detik setelah tombol diklik
document.addEventListener('DOMContentLoaded', function () {
    function startWorkConfirmation(event) {
        console.log("Fungsi startWorkConfirmation dipanggil!");

        // Tampilkan konfirmasi
        if (!confirm('Apakah Anda yakin ingin memulai pekerjaan untuk SPK ini?')) {
            event.preventDefault();
            console.log("Konfirmasi dibatalkan.");
            return false; // Batalkan jika pengguna memilih "Batal"
        }

        console.log("Konfirmasi diterima.");

        // Tampilkan popup jika navigasi berhasil
        const notifPopup = document.getElementById('notifPopup');
        if (notifPopup) {
            notifPopup.classList.remove('d-none');
            notifPopup.classList.add('show');

            // Sembunyikan popup setelah 5 detik
            setTimeout(() => {
                notifPopup.classList.remove('show');
                notifPopup.classList.add('hide');
            }, 5000); // Popup menghilang setelah 5 detik
        }

        return true; // Lanjutkan ke URL
    }

    window.startWorkConfirmation = startWorkConfirmation; // Buat fungsi global agar bisa dipanggil dari HTML
});

// Untuk menampilkan Popup Alasan Cancel
document.addEventListener('DOMContentLoaded', function () {
    function showCancelPopup() {
        const popup = document.getElementById('cancelPopup');
        if (popup) {
            popup.classList.remove('d-none');
            popup.classList.add('show');
            console.log('Popup Alasan Cancel ditampilkan.');
        } else {
            console.error('Popup element tidak ditemukan!');
        }
    }

    function closeCancelPopup() {
        const popup = document.getElementById('cancelPopup');
        if (popup) {
            popup.classList.remove('show');
            popup.classList.add('d-none');
            console.log('Popup Alasan Cancel ditutup.');
        } else {
            console.error('Popup element tidak ditemukan!');
        }
    }

    // Membuat fungsi global agar bisa dipanggil dari tombol HTML
    window.showCancelPopup = showCancelPopup;
    window.closeCancelPopup = closeCancelPopup;
});

// pop up filter 
document.addEventListener('DOMContentLoaded', function() {
    const openFilterBtn = document.getElementById('openFilter');
    const closePopupBtn = document.getElementById('closePopup');
    const filterPopup = document.getElementById('filterPopup');

    if (openFilterBtn && filterPopup) {
        openFilterBtn.addEventListener('click', function() {
            filterPopup.classList.remove('d-none');
        });
    }

    if (closePopupBtn && filterPopup) {
        closePopupBtn.addEventListener('click', function() {
            filterPopup.classList.add('d-none');
        });
    }
});