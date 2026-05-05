/**
 * Asset Management Javascript Utils
 */

window.openLinkModal = function (assetId, bmnNumber) {
    document.getElementById('linkBmnCode').innerText = bmnNumber;
    // Set action route dynamically
    const form = document.getElementById('linkForm');
    form.action = `/asset-manager/${assetId}/link`;
    document.getElementById('linkModal').classList.remove('hidden');
}

window.openEditModal = function (button) {
    const asset = JSON.parse(button.getAttribute('data-asset'));
    document.getElementById('edit_bmn_number').value = asset.bmn_number;
    document.getElementById('edit_device_name_id').value = asset.device_name_id;
    document.getElementById('hidden_edit_device_name_id').value = asset.device_name_id;
    document.getElementById('edit_serial_number').value = asset.serial_number || '';
    document.getElementById('edit_room_id').value = asset.room_id || '';
    document.getElementById('edit_user_id').value = asset.user_id || '';
    document.getElementById('edit_allocated_at').value = asset.allocated_at ? asset.allocated_at.substring(0, 10) : '';
    document.getElementById('edit_status_kondisi').value = asset.status_kondisi;

    const form = document.getElementById('editForm');
    form.action = `/asset-manager/${asset.id}`;
    document.getElementById('editModal').classList.remove('hidden');
}

window.handleUserAllocationChange = function (select) {
    const dateInput = document.getElementById('edit_allocated_at');
    if (select.value) {
        // If a user is selected and date is empty, set it to today
        if (!dateInput.value) {
            const today = new Date();
            // Format explicitly as timezone-independent local YYYY-MM-DD
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            dateInput.value = `${yyyy}-${mm}-${dd}`;
        }
    } else {
        // If user is unassigned, clear the allocation date
        dateInput.value = '';
    }
}

window.promptConfirm = function (event, verb) {
    event.preventDefault();
    const form = event.target;
    let title = '';
    let text = '';
    let confirmButtonText = '';
    let confirmButtonColor = '#3085d6';

    if (verb === 'ambil') {
        title = 'Konfirmasi Pengambilan';
        text = 'Apakah Anda yakin ingin mengambil alih aset ini secara permanen?';
        confirmButtonText = 'Ya, Ambil Alih';
        confirmButtonColor = '#005A8C';
    } else if (verb === 'pinjam') {
        title = 'Konfirmasi Peminjaman';
        text = 'Apakah Anda yakin ingin meminjam sementara aset ini? Peminjaman membutuhkan persetujuan pemilik.';
        confirmButtonText = 'Ya, Pinjam';
        confirmButtonColor = '#2563eb'; // blue-600
    } else if (verb === 'kembali') {
        title = 'Konfirmasi Pengembalian';
        text = 'Apakah Anda yakin ingin mengembalikan aset ini ke pemilik aslinya?';
        confirmButtonText = 'Ya, Kembalikan';
        confirmButtonColor = '#16a34a'; // green-600
    } else if (verb === 'approve') {
        title = 'Setujui Peminjaman';
        text = 'Apakah Anda yakin ingin menyetujui permintaan peminjaman aset ini?';
        confirmButtonText = 'Ya, Setujui';
        confirmButtonColor = '#16a34a'; // green-600
    } else if (verb === 'reject') {
        title = 'Tolak Peminjaman';
        text = 'Apakah Anda yakin ingin menolak permintaan peminjaman aset ini?';
        confirmButtonText = 'Ya, Tolak';
        confirmButtonColor = '#ef4444'; // red-500
    }

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmButtonColor,
            cancelButtonColor: '#ef4444',
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    } else {
        if (confirm(title + '\n' + text)) {
            form.submit();
        }
    }
}

// ============================================
// User Management Utilities
// ============================================
window.openUserCreateModal = function () {
    const el = document.getElementById('createModal');
    if (el) el.classList.remove('hidden');
}

window.prepareUserEditModal = function (button) {
    const user = JSON.parse(button.getAttribute('data-user'));
    const roles = JSON.parse(button.getAttribute('data-roles'));

    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;

    const checks = document.querySelectorAll('input[id^="edit_role_"]');
    checks.forEach(cb => {
        const rid = parseInt(cb.value);
        if (rid === 6) {
            cb.checked = true;
            cb.onclick = () => false; // Lock user role
        } else {
            cb.checked = roles.includes(rid);
            cb.onclick = null;
        }
    });

    document.getElementById('editForm').action = `/user-management/${user.id}`;
    const el = document.getElementById('editModal');
    if (el) el.classList.remove('hidden');
}

window.closeModal = function (id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// ============================================
// QR Modal (Dashboard) Utilities
// ============================================
window.openQrModal = function (url, deviceName, type = 'loan') {
    const nameEl = document.getElementById('qrDeviceName');
    if (nameEl) nameEl.textContent = deviceName;

    const title = document.getElementById('qrTitleText');
    const headerBg = document.getElementById('qrHeaderBg');

    if (title && headerBg) {
        if (type === 'transfer') {
            title.innerText = 'Scan untuk Serah Terima (Permanen)';
            headerBg.style.background = 'linear-gradient(to right, #4f46e5, #6b21a8)';
        } else {
            title.innerText = 'Scan untuk Meminjam (Sementara)';
            headerBg.style.background = 'linear-gradient(to right, #2563eb, #1e40af)';
        }
    }

    const qrContainer = document.getElementById('modalQrRender');
    if (qrContainer) {
        qrContainer.innerHTML = '';
        if (typeof QRCode !== 'undefined') {
            new QRCode(qrContainer, {
                text: url,
                width: 220,
                height: 220,
                colorDark: type === 'transfer' ? "#4f46e5" : "#2563eb",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        }
    }

    const el = document.getElementById('qrModal');
    if (el) el.classList.remove('hidden');
}

window.closeQrModal = function () {
    window.closeModal('qrModal');
}

// Global Escape Key Handler for Modals
window.onkeydown = function (e) {
    if (e.key === "Escape") {
        window.closeModal('createModal');
        window.closeModal('editModal');
        window.closeModal('qrModal');
        window.closeModal('linkModal');
    }
};

// ============================================
// Tickets Show Utilities (Chatbox Setup)
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chatBoxContainer');
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    const chatInput = document.getElementById('chatInput');
    if (chatInput) {
        chatInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim() !== '') {
                    this.closest('form').submit();
                }
            }
        });
    }
});
