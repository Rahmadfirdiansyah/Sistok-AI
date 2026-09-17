<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Laporan Barang Keluar</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Riwayat transaksi pengeluaran barang dari gudang.</p>
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
    <div class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-grow">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="searchInput" type="text" placeholder="Cari nama barang, tujuan, atau penerima..."
          class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"/>
      </div>
      
      <div class="grid grid-cols-12 md:flex md:flex-nowrap items-center gap-2">
        <div class="col-span-12 flex items-center gap-2">
          <input v-model="startDate" type="date"
            class="flex-1 w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white" />
          <span class="text-xs text-gray-400 shrink-0">s/d</span>
          <input v-model="endDate" type="date"
            class="flex-1 w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white" />
        </div>
        
        <select v-model="filterLokasi" class="col-span-10 md:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
          <option value="">Semua Lokasi</option>
          <option v-for="l in lokasis" :key="l" :value="l">{{ l }}</option>
        </select>
        
        <button @click="resetFilter" class="col-span-2 md:w-auto flex items-center justify-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer" title="Refresh / Reset Filter">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        </button>
      </div>
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
          Tidak ada transaksi ditemukan.
        </div>
        <div v-for="t in paginated" :key="t.id + '_mobile'" class="p-4 hover:bg-gray-50 transition-all">
          <div class="flex items-center justify-between mb-2">
            <span class="text-[11.5px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">TRX-OUT-{{ String(t.id).padStart(3, '0') }}</span>
          </div>
          
          <div class="flex items-center justify-between mb-1">
            <div>
              <p class="font-medium text-gray-800 m-0 text-sm">{{ t.barang }}</p>
              <p class="text-[11px] text-gray-400 m-0">{{ t.kodeBarang }}</p>
            </div>
            <span class="font-bold text-[13px] text-orange-500 whitespace-nowrap">
              -{{ t.qty }} {{ t.satuan }}
            </span>
          </div>

          <div class="text-xs text-gray-500 space-y-1 mb-3">
            <div class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              {{ t.tanggal }}
            </div>
            <div class="flex items-center gap-1.5">
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
              {{ t.lokasi }}
            </div>
            <div class="flex items-center gap-1.5 truncate">
              <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <span class="truncate">{{ t.dipakaiOleh }} • {{ t.tujuan }}</span>
            </div>
          </div>
          
          <button @click="showDetail(t)" class="w-full flex items-center justify-center gap-2 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 text-xs font-semibold rounded-xl transition-all border-0 cursor-pointer">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
            Lihat Detail
          </button>
        </div>
      </div>

      <!-- Desktop Table -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left px-5 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">ID Transaksi</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Tanggal</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Barang</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Dipakai Oleh</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Tujuan Penggunaan</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Lokasi Asal</th>
              <th class="text-right px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Jumlah</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Keterangan</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="filtered.length === 0 && !loading">
              <td colspan="9" class="text-center py-12 text-gray-400 text-sm">Tidak ada transaksi ditemukan.</td>
            </tr>
            <tr v-for="t in paginated" :key="t.id" class="hover:bg-gray-50 transition-all">
              <td class="px-5 py-3.5">
                <span class="text-[11.5px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">TRX-OUT-{{ String(t.id).padStart(3, '0') }}</span>
              </td>
              <td class="px-4 py-3.5 text-gray-600 text-[12.5px]">{{ t.tanggal }}</td>
              <td class="px-4 py-3.5">
                <p class="font-medium text-gray-700 m-0">{{ t.barang }}</p>
                <p class="text-[11px] text-gray-400 m-0">{{ t.kodeBarang }}</p>
              </td>
              <td class="px-4 py-3.5 text-gray-600 font-medium text-[12.5px]">{{ t.dipakaiOleh }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ t.tujuan }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ t.lokasi }}</td>
              <td class="px-4 py-3.5 text-right font-bold text-orange-500 text-[13px]">
                -{{ t.qty }} {{ t.satuan }}
              </td>
              <td class="px-4 py-3.5 text-gray-400 text-[12.5px] max-w-xs truncate">{{ t.keterangan || '-' }}</td>
              <td class="px-4 py-3.5 text-center">
                <button @click="showDetail(t)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer mx-auto" title="Lihat Detail">
                  <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination & info -->
      <div class="flex items-center justify-between px-5 py-3.5 border-t border-gray-100">
        <select v-model.number="perPage" class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 text-gray-600 outline-none focus:border-indigo-400 bg-white cursor-pointer">
          <option :value="10">10</option>
          <option :value="25">25</option>
          <option :value="50">50</option>
          <option :value="100">100</option>
        </select>
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
import Swal from 'sweetalert2'
import { getCache, setCache } from '@/utils/cache'

export default {
  name: 'LaporanKeluar',
  data: () => ({
    loading: false,
    searchInput: '',
    search: '',
    startDate: '',
    endDate: '',
    filterLokasi: '',
    page: 1,
    perPage: 10,
    lokasis: [],
    transaksis: [],
  }),
  computed: {
    filtered() {
      return this.transaksis.filter(t => {
        const q = this.search.toLowerCase()
        const matchQ = !q || t.barang.toLowerCase().includes(q) || t.dipakaiOleh.toLowerCase().includes(q) || t.tujuan.toLowerCase().includes(q) || t.kodeBarang.toLowerCase().includes(q)
        
        let matchDate = true
        if (this.startDate) {
          matchDate = matchDate && t.tanggal >= this.startDate
        }
        if (this.endDate) {
          matchDate = matchDate && t.tanggal <= this.endDate
        }

        const matchL = !this.filterLokasi || t.lokasi === this.filterLokasi
        return matchQ && matchDate && matchL
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
    search()       { this.page = 1 },
    startDate()    { this.page = 1 },
    endDate()      { this.page = 1 },
    filterLokasi() { this.page = 1 },
    perPage()      { this.page = 1 },
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      const cached = getCache('laporan_keluar')
      if (cached) {
        this.transaksis = cached.transaksis
        this.lokasis = cached.lokasis
      } else {
        this.loading = true
      }
      try {
        const [resT, resL] = await Promise.all([
          api.get('/barang-keluars'),
          api.get('/lokasis'),
        ])
        this.transaksis = resT.data
        this.lokasis = resL.data.map(l => l.nama)
        setCache('laporan_keluar', { transaksis: this.transaksis, lokasis: this.lokasis })
      } catch (error) {
        console.error('Error fetching data:', error)
      } finally {
        this.loading = false
      }
    },
    resetFilter() {
      this.searchInput = ''
      this.search = ''
      this.startDate = ''
      this.endDate = ''
      this.filterLokasi = ''
      this.fetchData()
    },
    printLaporan() {
      const params = new URLSearchParams();
      if (this.startDate) params.append('start_date', this.startDate);
      if (this.endDate) params.append('end_date', this.endDate);
      if (this.filterLokasi) params.append('lokasi', this.filterLokasi);
      const token = localStorage.getItem('token');
      if (token) params.append('token', token);
      const url = `/api/laporan/keluar/pdf?${params.toString()}`;
      window.open(url, '_blank');
    },
    exportExcel() {
      const params = new URLSearchParams();
      if (this.startDate) params.append('start_date', this.startDate);
      if (this.endDate) params.append('end_date', this.endDate);
      if (this.filterLokasi) params.append('lokasi', this.filterLokasi);
      const token = localStorage.getItem('token');
      if (token) params.append('token', token);
      const url = `/api/laporan/keluar/excel?${params.toString()}`;
      window.location.href = url;
    },
    showDetail(t) {
      let detailHtml = `
        <div class="text-left space-y-3 mt-4 text-sm text-gray-700">
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">ID Transaksi</span>
            <span class="font-mono text-indigo-600">TRX-OUT-${String(t.id).padStart(3, '0')}</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Tanggal</span>
            <span>${t.tanggal}</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Barang</span>
            <span>${t.barang} (${t.kodeBarang})</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Dipakai Oleh</span>
            <span>${t.dipakaiOleh}</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Tujuan</span>
            <span>${t.tujuan}</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Lokasi Asal</span>
            <span>${t.lokasi}</span>
          </div>
          <div class="flex justify-between border-b pb-2">
            <span class="font-semibold text-gray-500">Jumlah</span>
            <span class="text-orange-500 font-bold">-${t.qty} ${t.satuan}</span>
          </div>
          <div class="pt-2">
            <span class="font-semibold text-gray-500 block mb-1">Keterangan:</span>
            <div class="bg-gray-50 p-3 rounded-lg text-gray-600 max-h-48 overflow-y-auto whitespace-pre-wrap">${t.keterangan || 'Tidak ada keterangan'}</div>
          </div>
        </div>
      `;
      Swal.fire({
        title: 'Detail Pengeluaran',
        html: detailHtml,
        confirmButtonColor: '#6366f1',
        confirmButtonText: 'Tutup',
        width: '400px'
      })
    }
  },
}
</script>