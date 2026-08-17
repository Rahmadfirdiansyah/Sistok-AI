<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Laporan Stok Barang</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Pantau ketersediaan stok barang dan status kritis.</p>
      </div>
      <div class="flex items-center gap-2 w-full sm:w-auto">
        <button @click="printLaporan()"
          class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold rounded-xl transition-all cursor-pointer">
          <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8" rx="1"/></svg>
          Cetak PDF
        </button>
        <button @click="exportExcel()"
          class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Export Excel
        </button>
      </div>
    </div>

    <!-- Filter & Search -->
    <div class="grid grid-cols-12 sm:flex sm:flex-row gap-3">
      <div class="relative col-span-10 sm:flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="searchInput" type="text" placeholder="Cari nama atau kode barang..."
          class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"/>
      </div>
      <button @click="resetFilter" class="col-span-2 sm:col-auto flex items-center justify-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer" title="Refresh / Reset Filter">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
      </button>
      <select v-model="filterKategori" class="col-span-6 sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
        <option value="">Semua Kategori</option>
        <option v-for="k in kategoris" :key="k" :value="k">{{ k }}</option>
      </select>
      <select v-model="filterStatus" class="col-span-6 sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
        <option value="">Semua Status</option>
        <option value="aman">Aman</option>
        <option value="rendah">Rendah</option>
        <option value="kritis">Kritis</option>
      </select>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden relative min-h-[300px]">
      
      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
        <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>
      <!-- Mobile Cards (Visible only on small screens) -->
      <div class="md:hidden divide-y divide-gray-100">
        <div v-if="filtered.length === 0 && !loading" class="text-center py-12 text-gray-400 text-sm">
          Tidak ada data ditemukan.
        </div>
        <div v-for="b in paginated" :key="b.id + '_mobile'" class="p-4 hover:bg-gray-50 transition-all">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11.5px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ b.kode }}</span>
            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" :class="statusBadge(b)">{{ statusLabel(b) }}</span>
          </div>
          
          <div class="mb-2">
            <p class="font-medium text-gray-800 m-0 text-sm">{{ b.nama }}</p>
            <p class="text-[11px] text-gray-400 m-0">{{ b.kategori }}</p>
          </div>

          <div class="flex items-center justify-between text-xs mb-1">
            <span class="text-gray-500">Stok Saat Ini:</span>
            <span class="font-bold text-[13px]" :class="statusColor(b)">{{ b.stok }} {{ b.satuan }}</span>
          </div>
          
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-500">Min Stok:</span>
            <span class="font-semibold text-gray-400">{{ b.minStok }} {{ b.satuan }}</span>
          </div>
          
          <div class="mt-2 text-xs text-gray-500 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            {{ b.lokasi }}
          </div>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left px-5 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Kode</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Nama Barang</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Kategori</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Lokasi</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Satuan</th>
              <th class="text-right px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Min. Stok</th>
              <th class="text-right px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Stok Saat Ini</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="filtered.length === 0 && !loading">
              <td colspan="8" class="text-center py-12 text-gray-400 text-sm">Tidak ada data ditemukan.</td>
            </tr>
            <tr v-for="b in paginated" :key="b.id" class="hover:bg-gray-50 transition-all">
              <td class="px-5 py-3.5">
                <span class="text-[11.5px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ b.kode }}</span>
              </td>
              <td class="px-4 py-3.5 font-medium text-gray-700">{{ b.nama }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.kategori }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.lokasi }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.satuan }}</td>
              <td class="px-4 py-3.5 text-right text-gray-400 font-semibold">{{ b.minStok }}</td>
              <td class="px-4 py-3.5 text-right font-bold" :class="statusColor(b)">{{ b.stok }}</td>
              <td class="px-4 py-3.5 text-center">
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" :class="statusBadge(b)">{{ statusLabel(b) }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination & info -->
      <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100">
        <p class="text-xs text-gray-400 m-0">Menampilkan {{ from }}–{{ to }} dari {{ filtered.length }} data</p>
        <div class="flex items-center gap-1">
          <button @click="page--" :disabled="page === 1"
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer bg-transparent transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
          </button>
          <button v-for="p in totalPages" :key="p" @click="page = p"
            class="w-8 h-8 flex items-center justify-center rounded-lg text-[12.5px] font-medium cursor-pointer border transition-all"
            :class="page === p ? 'bg-indigo-500 text-white border-indigo-500' : 'border-gray-200 text-gray-500 hover:bg-gray-50 bg-transparent'">
            {{ p }}
          </button>
          <button @click="page++" :disabled="page === totalPages"
            class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer bg-transparent transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
          </button>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import api from '@/utils/api'
import { getCache, setCache } from '@/utils/cache'

export default {
  name: 'LaporanStok',
  data: () => ({
    loading: false,
    searchInput: '',
    search: '',
    filterKategori: '',
    filterStatus: '',
    page: 1,
    perPage: 10,
    kategoris: [],
    barangs: [],
  }),
  computed: {
    filtered() {
      return this.barangs.filter(b => {
        const q = this.search.toLowerCase()
        const matchQ = !q || b.nama.toLowerCase().includes(q) || b.kode.toLowerCase().includes(q)
        const matchK = !this.filterKategori || b.kategori === this.filterKategori
        const matchS = !this.filterStatus  || this.statusKey(b) === this.filterStatus
        return matchQ && matchK && matchS
      })
    },
    totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)) },
    paginated()  { return this.filtered.slice((this.page - 1) * this.perPage, this.page * this.perPage) },
    from()       { return this.filtered.length ? (this.page - 1) * this.perPage + 1 : 0 },
    to()         { return Math.min(this.page * this.perPage, this.filtered.length) },
  },
  watch: {
    searchInput(val) {
      clearTimeout(this._searchTimer)
      this._searchTimer = setTimeout(() => {
        this.search = val
      }, 300)
    },
    search()        { this.page = 1 },
    filterKategori(){ this.page = 1 },
    filterStatus()  { this.page = 1 },
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      const cached = getCache('laporan_stok')
      if (cached) {
        this.barangs = cached.barangs
        this.kategoris = cached.kategoris
      } else {
        this.loading = true
      }
      try {
        const [resB, resK] = await Promise.all([
          api.get('/barangs'),
          api.get('/kategoris'),
        ])
        this.barangs = resB.data
        this.kategoris = resK.data.map(k => k.nama)
        setCache('laporan_stok', { barangs: this.barangs, kategoris: this.kategoris })
      } catch (error) {
        console.error('Error fetching data:', error)
      } finally {
        this.loading = false
      }
    },
    resetFilter() {
      this.searchInput = ''
      this.search = ''
      this.filterKategori = ''
      this.filterStatus = ''
      this.fetchData()
    },
    statusKey(b)   { return b.stok < b.minStok / 2 ? 'kritis' : b.stok < b.minStok ? 'rendah' : 'aman' },
    statusLabel(b) { return { kritis:'Kritis', rendah:'Rendah', aman:'Aman' }[this.statusKey(b)] },
    statusBadge(b) { return { kritis:'bg-red-50 text-red-500', rendah:'bg-yellow-50 text-yellow-600', aman:'bg-green-50 text-green-600' }[this.statusKey(b)] },
    statusColor(b) { return { kritis:'text-red-500', rendah:'text-yellow-500', aman:'text-gray-700' }[this.statusKey(b)] },
    printLaporan() {
      const params = new URLSearchParams();
      if (this.filterKategori) params.append('kategori', this.filterKategori);
      if (this.filterStatus) params.append('status', this.filterStatus);
      const token = localStorage.getItem('token');
      if (token) params.append('token', token);
      const url = `/api/laporan/stok/pdf?${params.toString()}`;
      window.open(url, '_blank');
    },
    exportExcel() {
      const params = new URLSearchParams();
      if (this.filterKategori) params.append('kategori', this.filterKategori);
      if (this.filterStatus) params.append('status', this.filterStatus);
      const token = localStorage.getItem('token');
      if (token) params.append('token', token);
      const url = `/api/laporan/stok/excel?${params.toString()}`;
      window.location.href = url;
    }
  },
}
</script>