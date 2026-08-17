/**
 * =========================================================================
 * SWEETALERT2 COMPOSABLE HELPER
 * =========================================================================
 * Menyediakan fungsi-fungsi SweetAlert2 yang sudah dikustomisasi
 * untuk dipakai di seluruh halaman admin Stok Dasen.
 *
 * Penggunaan:
 *   import { useSwal } from '@/composables/useSwal'
 *   const { confirmDelete, toastSuccess, toastError, alertError } = useSwal()
 */

import Swal from 'sweetalert2'

// Toast instance (notifikasi kecil di pojok kanan atas)
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.onmouseenter = Swal.stopTimer
    toast.onmouseleave = Swal.resumeTimer
  },
})

export function useSwal() {

  /**
   * Konfirmasi hapus data
   * @param {string} namaItem - Nama item yang akan dihapus (misal: "ATK")
   * @param {string} jenisItem - Jenis item (misal: "kategori", "barang")
   * @returns {Promise<boolean>} - true jika user konfirmasi hapus
   */
  async function confirmDelete(namaItem, jenisItem = 'data', relatedCount = 0) {
    const jenis = jenisItem.toLowerCase();
    const skipVerification = jenis === 'barang' || jenis.includes('transaksi');
    
    let htmlContent = `${jenisItem} <strong>"${namaItem}"</strong> akan dihapus permanen.<br>Tindakan ini tidak bisa dibatalkan.`;
    
    if (!skipVerification) {
      htmlContent += `<br><br>Ketik <strong>${namaItem}</strong> untuk mengonfirmasi.`;
    }
    
    if (relatedCount > 0) {
      htmlContent = `<div style="background-color: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; text-align: left; border: 1px solid #f87171;">
        <strong>⚠️ Peringatan:</strong> ${jenisItem} ini memiliki ${relatedCount} data terkait. Sistem akan menolak penghapusan ini untuk menjaga integritas data riwayat.
      </div>` + htmlContent;
    }

    const swalOptions = {
      title: 'Hapus Data?',
      html: htmlContent,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Hapus!',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      focusCancel: true,
    };

    if (!skipVerification) {
      swalOptions.input = 'text';
      swalOptions.inputPlaceholder = `Ketik: ${namaItem}`;
      swalOptions.inputValidator = (value) => {
        if (value !== namaItem) {
          return 'Nama yang diketik tidak cocok!'
        }
      }
    }

    const result = await Swal.fire(swalOptions)
    return result.isConfirmed
  }

  /**
   * Konfirmasi Logout
   * @returns {Promise<boolean>} - true jika user konfirmasi logout
   */
  async function confirmLogout() {
    const result = await Swal.fire({
      title: 'Keluar Sistem?',
      text: 'Apakah Anda yakin ingin keluar dari aplikasi?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Keluar',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      focusCancel: true,
    })
    return result.isConfirmed
  }

  /**
   * Toast sukses (pojok kanan atas)
   * @param {string} message - Pesan sukses
   */
  function toastSuccess(message = 'Berhasil!') {
    Toast.fire({
      icon: 'success',
      title: message,
    })
  }

  /**
   * Toast error (pojok kanan atas)
   * @param {string} message - Pesan error
   */
  function toastError(message = 'Terjadi kesalahan!') {
    Toast.fire({
      icon: 'error',
      title: message,
    })
  }

  /**
   * Alert error (modal tengah) — untuk error yang lebih serius
   * @param {string} message - Pesan error
   * @param {string} title - Judul error
   */
  function alertError(message = 'Terjadi kesalahan!', title = 'Gagal!') {
    Swal.fire({
      title: title,
      text: message,
      icon: 'error',
      confirmButtonColor: '#6366f1',
      confirmButtonText: 'OK',
    })
  }

  /**
   * Alert sukses (modal tengah) — untuk aksi penting seperti simpan
   * @param {string} message - Pesan sukses
   * @param {string} title - Judul sukses
   */
  function alertSuccess(message = 'Data berhasil disimpan!', title = 'Berhasil!') {
    Swal.fire({
      title: title,
      text: message,
      icon: 'success',
      confirmButtonColor: '#6366f1',
      confirmButtonText: 'OK',
      timer: 2000,
      timerProgressBar: true,
    })
  }

  /**
   * Konfirmasi Edit Data
   * @param {string} title - Judul konfirmasi
   * @param {string} text - Teks penjelasan
   * @returns {Promise<boolean>}
   */
  async function confirmEdit(title = 'Simpan Perubahan?', text = 'Pastikan data yang diubah sudah benar.') {
    const result = await Swal.fire({
      title: title,
      text: text,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#6366f1',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Simpan',
      cancelButtonText: 'Batal',
      reverseButtons: true,
    })
    return result.isConfirmed
  }

  /**
   * Konfirmasi ketika stok akan menyentuh batas kritis/minimum
   * @param {string} itemName - Nama barang
   * @returns {Promise<boolean>}
   */
  async function confirmCriticalStock(itemName) {
    const result = await Swal.fire({
      title: 'Peringatan Stok Kritis!',
      html: `Transaksi ini akan membuat stok <strong>"${itemName}"</strong> menyentuh batas minimum atau habis.<br><br>Ketik <strong>${itemName}</strong> untuk melanjutkan.`,
      icon: 'warning',
      input: 'text',
      inputPlaceholder: `Ketik: ${itemName}`,
      showCancelButton: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Tetap Lanjutkan',
      cancelButtonText: 'Batal',
      reverseButtons: true,
      focusCancel: true,
      inputValidator: (value) => {
        if (value !== itemName) {
          return 'Nama barang yang diketik tidak cocok!'
        }
      }
    })
    return result.isConfirmed
  }

  /**
   * Lihat / Zoom Gambar
   * @param {string} url - URL gambar
   * @param {string} title - Judul modal
   */
  function viewImage(url, title = 'Pratinjau Gambar') {
    if (!url) return;
    Swal.fire({
      imageUrl: url,
      imageAlt: title,
      showConfirmButton: false,
      showCloseButton: true,
      customClass: {
        image: 'max-w-full max-h-[85vh] object-contain rounded-xl m-0',
        popup: 'rounded-2xl bg-transparent shadow-none',
        closeButton: 'bg-white/80 hover:bg-white text-gray-800 rounded-full w-8 h-8 flex items-center justify-center'
      },
      backdrop: 'rgba(0,0,0,0.85)'
    })
  }

  return {
    confirmDelete,
    confirmLogout,
    confirmEdit,
    confirmCriticalStock,
    toastSuccess,
    toastError,
    alertError,
    alertSuccess,
    viewImage,
  }
}
