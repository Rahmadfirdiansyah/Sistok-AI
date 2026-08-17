<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Lokasi Barang</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Kelola lokasi penyimpanan barang inventaris.</p>
      </div>
      <button v-if="!isStaff" @click="openModal()"
        class="w-full sm:w-auto flex justify-center items-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Tambah Lokasi
      </button>
    </div>

    <!-- Search -->
    <div class="relative max-w-sm">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
        stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
      </svg>
      <input v-model="search" type="text" placeholder="Cari lokasi..."
        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
    </div>

    <!-- Grid kartu lokasi -->
    <div class="relative min-h-[200px]">
      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-slate-50/80 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
        <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <div v-if="filtered.length === 0 && !loading" class="col-span-full text-center py-16 text-gray-400 text-sm">
          Tidak ada lokasi ditemukan.
        </div>
      <div v-for="l in filtered" :key="l.id"
        class="bg-white rounded-2xl border border-gray-100 p-5 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/5 transition-all">
        <div class="flex items-start justify-between mb-4">
          <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" :class="l.bg">
            <span v-html="l.svg" :class="l.color"></span>
          </div>
          <div v-if="!isStaff" class="flex items-center gap-1">
            <button @click="openModal(l)"
              class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
            </button>
            <button @click="hapus(l)"
              class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all border-0 bg-transparent cursor-pointer">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M9 6V4h6v2" />
              </svg>
            </button>
          </div>
        </div>
        <p class="font-semibold text-gray-800 text-[14px] m-0">{{ l.nama }}</p>
        <p class="text-gray-400 text-xs mt-1 m-0">{{ l.deskripsi || 'Tidak ada deskripsi' }}</p>
        <div class="mt-4 pt-4 border-t border-gray-100 flex items-center justify-between">
          <span class="text-[11.5px] text-gray-400">Total barang</span>
          <span class="text-sm font-bold text-indigo-500">{{ l.jumlah }} item</span>
        </div>
      </div>
      </div>
    </div>

    <!-- MODAL -->
    <Modal :show="modal" :title="form.id ? 'Edit Lokasi' : 'Tambah Lokasi'" @close="modal = false">
      <div class="space-y-4">
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Nama Lokasi</label>
          <input v-model="form.nama" type="text" placeholder="cth: Gudang Utama, Lemari DCO"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Deskripsi <span
              class="font-normal text-gray-300">(opsional)</span></label>
          <textarea v-model="form.deskripsi" rows="3" placeholder="Deskripsi singkat lokasi..."
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-none"></textarea>
        </div>
      </div>
      <template #footer>
        <button @click="modal = false"
          class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">Batal</button>
        <button @click="simpan"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-500 hover:bg-indigo-600 text-white border-0 cursor-pointer transition-all">
          {{ form.id ? 'Simpan Perubahan' : 'Tambah Lokasi' }}
        </button>
      </template>
    </Modal>

  </div>
</template>

<script>
import api from '@/utils/api'
import Modal from '../../../components/Modal.vue'
import { useAuth } from '@/composables/useAuth'
import { useSwal } from '@/composables/useSwal'
import { getCache, setCache } from '@/utils/cache'

const icons = [
  { bg: 'bg-indigo-50', color: 'text-indigo-500', svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>` }, // Map Pin
  { bg: 'bg-blue-50', color: 'text-blue-500', svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18"/><path d="M9 21V9H5v12"/><path d="M19 21V5h-4v16"/><path d="M14 21V1h-4v20"/></svg>` }, // Building
  { bg: 'bg-green-50', color: 'text-green-500', svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="3" y1="15" x2="21" y2="15"/></svg>` }, // Grid / Shelves
  { bg: 'bg-orange-50', color: 'text-orange-500', svg: `<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>` }, // Cabinet / Storage
]

export default {
  name: 'Lokasi',
  components: {
    Modal
  },
  data: () => ({
    loading: false,
    search: '',
    modal: false,
    form: {},
    lokasis: [],
  }),
  computed: {
    isStaff() {
      return useAuth().user.value?.role === 'user'
    },
    filtered() {
      const q = this.search.toLowerCase()
      return this.lokasis.filter(l =>
        !q || l.nama.toLowerCase().includes(q) || (l.deskripsi || '').toLowerCase().includes(q)
      )
    },
  },
  mounted() {
    this.fetchLokasis()
  },
  methods: {
    async fetchLokasis() {
      const cached = getCache('master_lokasi')
      if (cached) {
        this.lokasis = cached
      } else {
        this.loading = true
      }
      try {
        const response = await api.get('/lokasis')
        this.lokasis = response.data.map((l, index) => ({
          ...l,
          ...icons[index % icons.length]
        }))
        setCache('master_lokasi', this.lokasis)
      } catch (error) {
        console.error('Error fetching lokasis:', error)
      } finally {
        this.loading = false
      }
    },
    openModal(l = null) {
      this.form = l ? { ...l } : { id: null, nama: '', deskripsi: '' }
      this.modal = true
    },
    async simpan() {
      const { toastSuccess, toastError, confirmEdit } = useSwal()
      if (!this.form.nama.trim()) return
      try {
        if (this.form.id) {
          const isConfirm = await confirmEdit('Simpan Perubahan Lokasi?')
          if (!isConfirm) return
          
          const response = await api.put(`/lokasis/${this.form.id}`, {
            nama: this.form.nama,
            deskripsi: this.form.deskripsi,
          })
          const idx = this.lokasis.findIndex(l => l.id === this.form.id)
          if (idx !== -1) {
            this.lokasis[idx] = {
              ...this.lokasis[idx],
              ...response.data
            }
          }
        } else {
          const response = await api.post('/lokasis', {
            nama: this.form.nama,
            deskripsi: this.form.deskripsi,
          })
          const icon = icons[this.lokasis.length % icons.length]
          this.lokasis.unshift({
            ...response.data,
            ...icon,
          })
        }
        this.modal = false
        toastSuccess('Lokasi berhasil disimpan!')
      } catch (error) {
        console.error('Error saving lokasi:', error)
        toastError(error.response?.data?.message || 'Gagal menyimpan lokasi')
      }
    },
    async hapus(l) {
      const { confirmDelete, toastSuccess, toastError } = useSwal()
      const confirmed = await confirmDelete(l.nama, 'Lokasi', l.jumlah)
      if (!confirmed) return
      try {
        await api.delete(`/lokasis/${l.id}`)
        this.lokasis = this.lokasis.filter(x => x.id !== l.id)
        toastSuccess('Lokasi berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting lokasi:', error)
        toastError(error.response?.data?.message || 'Gagal menghapus lokasi')
      }
    },
  },
}
</script>