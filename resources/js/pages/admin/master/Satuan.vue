<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Satuan</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Kelola satuan ukuran barang inventaris.</p>
      </div>
      <button v-if="!isStaff" @click="openModal()"
        class="w-full sm:w-auto flex justify-center items-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Tambah Satuan
      </button>
    </div>

    <!-- Search -->
    <div class="relative max-w-sm">
      <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
        stroke-width="2" viewBox="0 0 24 24">
        <circle cx="11" cy="11" r="8" />
        <line x1="21" y1="21" x2="16.65" y2="16.65" />
      </svg>
      <input v-model="search" type="text" placeholder="Cari satuan..."
        class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
    </div>

    <!-- Grid kartu satuan -->
    <div class="relative min-h-[200px]">
      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-slate-50/80 backdrop-blur-sm z-10 flex items-center justify-center rounded-2xl">
        <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>
      
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        <div v-if="filtered.length === 0 && !loading" class="col-span-full text-center py-16 text-gray-400 text-sm">
          Tidak ada satuan ditemukan.
        </div>
      <div v-for="s in filtered" :key="s.id"
        @click="openItemsModal(s)"
        class="bg-white rounded-2xl border border-gray-100 p-5 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/5 transition-all cursor-pointer">
        <!-- Icon & aksi -->
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
              <line x1="19" y1="5" x2="5" y2="19" />
              <circle cx="6.5" cy="6.5" r="2.5" />
              <circle cx="17.5" cy="17.5" r="2.5" />
            </svg>
          </div>
          <div v-if="!isStaff" class="flex gap-1">
            <button @click.stop="openModal(s)"
              class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer"
              title="Edit Satuan">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
              </svg>
            </button>
            <button @click.stop="hapus(s)"
              class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all border-0 bg-transparent cursor-pointer"
              title="Hapus Satuan">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="3 6 5 6 21 6" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M9 6V4h6v2" />
              </svg>
            </button>
          </div>
        </div>

        <!-- Nama & keterangan -->
        <p class="font-bold text-gray-800 text-lg m-0 leading-none">{{ s.nama }}</p>
        <p class="text-gray-400 text-xs mt-1 m-0">{{ s.keterangan || 'Tidak ada keterangan' }}</p>

        <!-- Footer -->
        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
          <span class="text-[11px] text-gray-400">Digunakan</span>
          <span class="text-xs font-bold text-indigo-500">{{ s.digunakan || 0 }} barang</span>
        </div>
      </div>
      </div>
    </div>

    <!-- MODAL EDIT/TAMBAH -->
    <Modal :show="modal" :title="form.id ? 'Edit Satuan' : 'Tambah Satuan'" @close="modal = false">
      <div class="space-y-4">
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Nama Satuan</label>
          <input v-model="form.nama" type="text" placeholder="cth: pcs, rim, botol"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Keterangan <span
              class="font-normal text-gray-300">(opsional)</span></label>
          <input v-model="form.keterangan" type="text" placeholder="cth: Pieces / Satuan buah"
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
        </div>
      </div>
      <template #footer>
        <button @click="modal = false"
          class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">Batal</button>
        <button @click="simpan"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-500 hover:bg-indigo-600 text-white border-0 cursor-pointer transition-all">
          {{ form.id ? 'Simpan Perubahan' : 'Tambah Satuan' }}
        </button>
      </template>
    </Modal>

    <!-- MODAL DAFTAR BARANG PER SATUAN -->
    <Modal :show="itemsModal" :title="itemsModalTitle" maxWidth="max-w-3xl" @close="itemsModal = false">
      <div class="space-y-4">
        <!-- Top Bar: Mini Search & Shortcut -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
          <div class="relative w-full sm:w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="11" cy="11" r="8" />
              <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            <input v-model="itemSearch" type="text" placeholder="Cari barang dengan satuan ini..."
              class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
          </div>
          <button @click="goToDataBarang(selectedSatuan?.nama)"
            class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-xl transition-all border-0 cursor-pointer">
            <span>Lihat di Data Barang</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
              <polyline points="15 3 21 3 21 9"/>
              <line x1="10" y1="14" x2="21" y2="3"/>
            </svg>
          </button>
        </div>

        <!-- Table / Empty State -->
        <div class="relative min-h-[150px]">
          <div v-if="itemsLoading" class="py-12 text-center">
            <div class="w-7 h-7 border-3 border-indigo-200 border-t-indigo-500 rounded-full animate-spin mx-auto mb-2"></div>
            <span class="text-xs text-gray-400">Memuat data barang...</span>
          </div>
          <div v-else-if="filteredModalItems.length === 0" class="py-12 text-center text-gray-400 text-xs">
            Tidak ada barang yang ditemukan.
          </div>
          <div v-else class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="w-full text-left text-xs">
              <thead class="bg-gray-50 text-gray-500 font-semibold uppercase tracking-wider text-[10.5px]">
                <tr>
                  <th class="px-4 py-3">Kode</th>
                  <th class="px-4 py-3">Nama Barang</th>
                  <th class="px-4 py-3">Kategori</th>
                  <th class="px-4 py-3 text-right">Stok</th>
                  <th class="px-4 py-3 text-center">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100 text-gray-700">
                <tr v-for="b in filteredModalItems" :key="b.id" class="hover:bg-gray-50/80 transition-colors">
                  <td class="px-4 py-3 font-mono font-medium text-indigo-600">{{ b.kode }}</td>
                  <td class="px-4 py-3 font-semibold text-gray-800">{{ b.nama }}</td>
                  <td class="px-4 py-3 text-gray-500">{{ b.kategori || '-' }}</td>
                  <td class="px-4 py-3 text-right font-bold" :class="statusColor(b)">
                    {{ b.stok }} <span class="text-[10px] font-normal text-gray-400">{{ b.satuan }}</span>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <span class="px-2 py-0.5 rounded-full text-[10.5px] font-semibold inline-block" :class="statusBadge(b)">
                      {{ statusLabel(b) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <template #footer>
        <button @click="itemsModal = false"
          class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">
          Tutup
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

export default {
  name: 'Satuan',
  components: {
    Modal
  },
  data: () => ({
    loading: false,
    search: '',
    modal: false,
    form: {},
    satuans: [],
    itemsModal: false,
    selectedSatuan: null,
    allBarangs: [],
    itemsLoading: false,
    itemSearch: '',
  }),
  computed: {
    isStaff() {
      return useAuth().user.value?.role === 'user'
    },
    filtered() {
      const q = this.search.toLowerCase()
      return this.satuans.filter(s =>
        !q || s.nama.toLowerCase().includes(q) || (s.keterangan || '').toLowerCase().includes(q)
      )
    },
    itemsModalTitle() {
      if (!this.selectedSatuan) return 'Daftar Barang'
      const total = this.satuanBarangs.length
      return `🏷️ Daftar Barang — Satuan: ${this.selectedSatuan.nama} (${total} Item)`
    },
    satuanBarangs() {
      if (!this.selectedSatuan) return []
      return this.allBarangs.filter(b => b.satuan === this.selectedSatuan.nama || b.satuan_id === this.selectedSatuan.id)
    },
    filteredModalItems() {
      const q = this.itemSearch.toLowerCase().trim()
      return this.satuanBarangs.filter(b => !q || b.nama.toLowerCase().includes(q) || b.kode.toLowerCase().includes(q) || (b.kategori || '').toLowerCase().includes(q))
    },
  },
  mounted() {
    this.fetchSatuans()
  },
  methods: {
    async fetchSatuans() {
      const cached = getCache('master_satuan')
      if (cached) {
        this.satuans = cached
      } else {
        this.loading = true
      }
      try {
        const response = await api.get('/satuans')
        this.satuans = response.data
        setCache('master_satuan', this.satuans)
      } catch (error) {
        console.error('Error fetching satuans:', error)
      } finally {
        this.loading = false
      }
    },
    openModal(s = null) {
      this.form = s ? { ...s } : { id: null, nama: '', keterangan: '' }
      this.modal = true
    },
    async openItemsModal(s) {
      this.selectedSatuan = s
      this.itemSearch = ''
      this.itemsModal = true
      if (this.allBarangs.length === 0) {
        this.itemsLoading = true
        try {
          const res = await api.get('/barangs')
          this.allBarangs = res.data
        } catch (err) {
          console.error('Error fetching barangs for modal:', err)
        } finally {
          this.itemsLoading = false
        }
      }
    },
    goToDataBarang(name) {
      this.itemsModal = false
      this.$router.push({ name: 'admin.barang', query: { satuan: name } })
    },
    statusKey(b)   { return b.stok <= 0 ? 'habis' : (b.stok < b.minStok ? 'rendah' : 'aman') },
    statusLabel(b) { return { habis:'Habis', rendah:'Rendah', aman:'Aman' }[this.statusKey(b)] },
    statusBadge(b) { return { habis:'bg-red-50 text-red-500', rendah:'bg-yellow-50 text-yellow-600', aman:'bg-green-50 text-green-600' }[this.statusKey(b)] },
    statusColor(b) { return { habis:'text-red-500', rendah:'text-yellow-600', aman:'text-gray-700' }[this.statusKey(b)] },
    async simpan() {
      const { toastSuccess, toastError, confirmEdit } = useSwal()
      if (!this.form.nama.trim()) return
      try {
        if (this.form.id) {
          const isConfirm = await confirmEdit('Simpan Perubahan Satuan?')
          if (!isConfirm) return
          
          const response = await api.put(`/satuans/${this.form.id}`, {
            nama: this.form.nama,
            keterangan: this.form.keterangan,
          })
          const idx = this.satuans.findIndex(s => s.id === this.form.id)
          if (idx !== -1) {
            this.satuans[idx] = {
              ...this.satuans[idx],
              ...response.data
            }
          }
        } else {
          const response = await api.post('/satuans', {
            nama: this.form.nama,
            keterangan: this.form.keterangan,
          })
          this.satuans.unshift({
            ...response.data,
            digunakan: 0,
          })
        }
        this.modal = false
        toastSuccess('Satuan berhasil disimpan!')
      } catch (error) {
        console.error('Error saving satuan:', error)
        toastError(error.response?.data?.message || 'Gagal menyimpan satuan')
      }
    },
    async hapus(s) {
      const { confirmDelete, toastSuccess, toastError } = useSwal()
      const confirmed = await confirmDelete(s.nama, 'Satuan', s.digunakan)
      if (!confirmed) return
      try {
        await api.delete(`/satuans/${s.id}`)
        this.satuans = this.satuans.filter(x => x.id !== s.id)
        toastSuccess('Satuan berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting satuan:', error)
        toastError(error.response?.data?.message || 'Gagal menghapus satuan')
      }
    },
  },
}
</script>