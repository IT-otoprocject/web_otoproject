import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    const addItemButton = document.getElementById('addItem');
    const itemContainer = document.getElementById('itemContainer');

    addItemButton.addEventListener('click', function () {
        const newRow = document.createElement('tr');

        newRow.innerHTML = `
            <td class="border px-4 py-2">
                <input type="text" name="nama_barang[]" class="form-control w-full" required>
            </td>
            <td class="border px-4 py-2">
                <input type="number" name="qty[]" class="form-control w-full" required>
            </td>
            <td class="border px-4 py-2 text-center">
                <button type="button" class="btn btn-outline-danger removeItem">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        itemContainer.appendChild(newRow);
    });

    itemContainer.addEventListener('click', function (event) {
        if (event.target.closest('.removeItem')) {
            event.target.closest('tr').remove();
        }
    });
});