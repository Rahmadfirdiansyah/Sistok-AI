<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0 flex items-center gap-2">
          <span>Laporan Limbah & Barang Rusak</span>
          <span class="text-lg">🗑️</span>
        </h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Pantau akumulasi limbah, barang rusak, dan afkir dari pengeluaran gudang.</p>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button @click="printLaporan()"
          class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold rounded-xl transition-all cursor-pointer">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
          Cetak PDF
        </button>
        <button @click="exportExcel()"
          class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer shadow-lg shadow-indigo-500/20">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Export Excel
        </button>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
      <!-- Total Jenis Limbah -->
      <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-6">
          <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
          </div>
          <span class="text-xs font-semibold text-red-500 bg-red-50 px-2.5 py-1 rounded-lg">Limbah</span>
        </div>
        <p class="text-3xl font-black text-gray-800 m-0 leading-none">{{ summary.total_item || 0 }}</p>
        <p class="text-sm text-gray-400 font-medium m-0 mt-1.5">Total Jenis Limbah</p>
      </div>

      <!-- Total Volume Limbah -->
      <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <div class="flex items-start justify-between mb-6">
          <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
            <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
          </div>
          <span class="text-xs font-semibold text-amber-500 bg-amber-50 px-2.5 py-1 rounded-lg">Akumulasi</span>
        </div>
        <p class="text-3xl font-black text-gray-800 m-0 leading-none">{{ summary.total_volume || 0 }}</p>
        <p class="text-sm text-gray-400 font-medium m-0 mt-1.5">Total Volume Limbah</p>
      </div>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input v-model="searchInput" type="text" placeholder="Cari nama atau kode barang limbah..."
          class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all" />
      </div>
      <button @click="resetFilter" class="flex items-center justify-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer" title="Refresh / Reset Filter">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
      </button>
      <select v-model="filterKategori"
        class="sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-red-400 focus:ring-2 focus:ring-red-100 transition-all bg-white">
        <option value="">Semua Kategori</option>
        <option v-for="k in kategoris" :key="k" :value="k">{{ k }}</option>
      </select>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden relative min-h-[300px]">
      
      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
        <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>

      <!-- Table View -->
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-red-50/50 border-b border-gray-100">
              <th class="text-left px-5 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Kode</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Nama Barang</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Kategori</th>
              <th class="text-right px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Total Akumulasi Limbah</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Frekuensi</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Terakhir Dibuang</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-red-800 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="limbahList.length === 0 && !loading">
              <td colspan="7" class="text-center py-16 text-gray-400 text-sm">
                <div class="max-w-xs mx-auto text-center space-y-2">
                  <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-xl mx-auto text-gray-400">🗑️</div>
                  <p class="font-semibold text-gray-600 m-0">Belum Ada Limbah Dicatat</p>
                  <p class="text-xs text-gray-400 m-0">Transaksi pengeluaran berjenis limbah akan otomatis terkumpul secara terakumulasi di sini.</p>
                </div>
              </td>
            </tr>
            <tr v-for="b in limbahList" :key="b.barang_id" class="hover:bg-red-50/20 transition-all">
              <td class="px-5 py-3.5">
                <span class="text-[11.5px] font-mono font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-lg border border-red-100">{{ b.kode }}</span>
              </td>
              <td class="px-4 py-3.5 font-bold text-gray-800">{{ b.nama }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.kategori }}</td>
              <td class="px-4 py-3.5 text-right font-black text-red-600 text-base">
                {{ b.total_limbah }} <span class="text-xs font-normal text-gray-400">{{ b.satuan }}</span>
              </td>
              <td class="px-4 py-3.5 text-center text-gray-600 text-xs font-semibold">
                <span class="bg-gray-100 px-2.5 py-1 rounded-full">{{ b.frekuensi }} kali</span>
              </td>
              <td class="px-4 py-3.5 text-center text-gray-500 text-[12.5px]">{{ b.terakhir_dibuang }}</td>
              <td class="px-4 py-3.5 text-center">
                <button @click="openRiwayatModal(b)"
                  class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 text-xs font-semibold rounded-xl border-0 cursor-pointer transition-all inline-flex items-center gap-1.5">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                  <span>Riwayat</span>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <!-- MODAL RIWAYAT LIMBAH PER BARANG -->
    <Modal :show="riwayatModal" :title="riwayatTitle" maxWidth="max-w-2xl" @close="riwayatModal = false">
      <div class="space-y-4">
        <div class="p-3 bg-red-50 rounded-xl border border-red-100 flex items-center justify-between text-xs text-red-800 font-semibold">
          <span>Total Akumulasi Limbah</span>
          <span class="text-sm font-black text-red-600">{{ selectedBarang?.total_limbah }} {{ selectedBarang?.satuan }}</span>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 text-gray-500 font-semibold uppercase text-[10px]">
              <tr>
                <th class="px-3 py-2.5">ID TRX</th>
                <th class="px-3 py-2.5">Tanggal</th>
                <th class="px-3 py-2.5 text-right">Jumlah</th>
                <th class="px-3 py-2.5">Pemakai</th>
                <th class="px-3 py-2.5">Keterangan</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-gray-700">
              <tr v-for="r in selectedBarang?.riwayat || []" :key="r.id" class="hover:bg-gray-50">
                <td class="px-3 py-2.5 font-mono text-indigo-600 font-medium">{{ r.id }}</td>
                <td class="px-3 py-2.5 font-medium">{{ r.tanggal }}</td>
                <td class="px-3 py-2.5 text-right font-bold text-red-600">{{ r.jumlah }} {{ selectedBarang?.satuan }}</td>
                <td class="px-3 py-2.5 text-gray-600">{{ r.dipakai_oleh }}</td>
                <td class="px-3 py-2.5 text-gray-400 max-w-[150px] truncate">{{ r.keterangan }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <template #footer>
        <button @click="riwayatModal = false"
          class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">
          Tutup
        </button>
      </template>
    </Modal>

  </div>
</template>

<script>
import api from '@/utils/api'
import Modal from '@/components/Modal.vue'

export default {
  name: 'LaporanLimbah',
  components: {
    Modal
  },
  data: () => ({
    loading: false,
    searchInput: '',
    search: '',
    filterKategori: '',
    kategoris: [],
    limbahList: [],
    summary: {
      total_item: 0,
      total_volume: 0,
    },
    riwayatModal: false,
    selectedBarang: null,
  }),
  computed: {
    riwayatTitle() {
      if (!this.selectedBarang) return 'Riwayat Pengeluaran Limbah'
      return `🗑️ Riwayat Limbah — ${this.selectedBarang.nama} (${this.selectedBarang.kode})`
    }
  },
  watch: {
    searchInput(val) {
      clearTimeout(this._searchTimer)
      this._searchTimer = setTimeout(() => {
        this.search = val
        this.fetchData()
      }, 300)
    },
    filterKategori() {
      this.fetchData()
    },
  },
  mounted() {
    this.fetchCategories()
    this.fetchData()
  },
  methods: {
    async fetchCategories() {
      try {
        const res = await api.get('/kategoris')
        this.kategoris = res.data.map(k => k.nama)
      } catch (err) {
        console.error('Error fetching categories:', err)
      }
    },
    async fetchData() {
      this.loading = true
      try {
        const params = new URLSearchParams()
        if (this.search) params.append('search', this.search)
        if (this.filterKategori) params.append('kategori', this.filterKategori)

        const res = await api.get(`/laporan/limbah?${params.toString()}`)
        this.limbahList = res.data.data
        this.summary = res.data.summary
      } catch (err) {
        console.error('Error fetching laporan limbah:', err)
      } finally {
        this.loading = false
      }
    },
    resetFilter() {
      this.searchInput = ''
      this.search = ''
      this.filterKategori = ''
      this.fetchData()
    },
    openRiwayatModal(b) {
      this.selectedBarang = b
      this.riwayatModal = true
    },
    printLaporan() {
      const params = new URLSearchParams()
      if (this.filterKategori) params.append('kategori', this.filterKategori)
      const token = localStorage.getItem('token')
      if (token) params.append('token', token)
      const url = `/api/laporan/limbah/pdf?${params.toString()}`
      window.open(url, '_blank')
    },
    exportExcel() {
      const params = new URLSearchParams()
      if (this.filterKategori) params.append('kategori', this.filterKategori)
      const token = localStorage.getItem('token')
      if (token) params.append('token', token)
      const url = `/api/laporan/limbah/excel?${params.toString()}`
      window.location.href = url
    }
  },
}
</script>
