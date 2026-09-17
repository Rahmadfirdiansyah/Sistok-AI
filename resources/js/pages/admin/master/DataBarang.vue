<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Data Barang</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Kelola seluruh data barang inventaris.</p>
      </div>
      <button v-if="!isStaff" @click="openModal()" class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Barang
      </button>
    </div>

    <!-- Filter & Search -->
    <div class="flex flex-col lg:flex-row gap-2.5 items-stretch lg:items-center">
      <div class="relative flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input v-model="searchInput" type="text" placeholder="Cari nama atau kode barang..."
          class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all"/>
      </div>
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-2 lg:flex lg:items-center lg:w-auto">
        <button @click="resetFilter" class="col-span-2 sm:col-span-1 flex items-center justify-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer" title="Refresh / Reset Filter">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
        </button>
        <select v-model="filterKategori" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
          <option value="">Semua Kategori</option>
          <option v-for="k in kategoris" :key="k" :value="k">{{ k }}</option>
        </select>
        <select v-model="filterLokasi" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
          <option value="">Semua Lokasi</option>
          <option v-for="l in lokasis" :key="l" :value="l">{{ l }}</option>
        </select>
        <select v-model="filterSatuan" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
          <option value="">Semua Satuan</option>
          <option v-for="s in satuans" :key="s" :value="s">{{ s }}</option>
        </select>
        <select v-model="filterStatus" class="px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
          <option value="">Semua Status</option>
          <option value="aman">Aman</option>
          <option value="rendah">Rendah</option>
          <option value="habis">Habis</option>
        </select>
      </div>
    </div>

    <!-- Main Content Container -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden relative min-h-[300px]">
      
      <!-- Loading Overlay -->
      <div v-if="loading" class="absolute inset-0 bg-white/80 z-10 flex items-center justify-center">
        <div class="w-8 h-8 border-4 border-indigo-200 border-t-indigo-500 rounded-full animate-spin"></div>
      </div>
      
      <!-- Desktop Table View (Hidden on mobile/tablet) -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
              <th class="text-left px-5 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Kode</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Gambar</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Nama Barang</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Kategori</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Lokasi</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Satuan</th>
              <th class="text-right px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Stok</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Status</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="filtered.length === 0 && !loading">
              <td colspan="9" class="text-center py-12 text-gray-400 text-sm">Tidak ada data ditemukan.</td>
            </tr>
            <tr v-for="b in paginated" :key="b.id" class="hover:bg-gray-50 transition-all">
              <td class="px-5 py-3.5">
                <span class="text-[11.5px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ b.kode }}</span>
              </td>
              <td class="px-4 py-3.5 text-center">
                <img v-if="b.gambar" :src="b.gambar" @click="zoomImage(b.gambar, b.nama)" alt="Gambar" class="w-10 h-10 object-cover rounded-lg border border-gray-100 mx-auto cursor-pointer hover:opacity-80 transition-opacity" title="Klik untuk perbesar" />
                <div v-else class="w-10 h-10 mx-auto rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
              </td>
              <td class="px-4 py-3.5 font-medium text-gray-700">{{ b.nama }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.kategori }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.lokasi }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.satuan }}</td>
              <td class="px-4 py-3.5 text-right font-bold" :class="statusColor(b)">{{ b.stok }}</td>
              <td class="px-4 py-3.5 text-center">
                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full" :class="statusBadge(b)">{{ statusLabel(b) }}</span>
              </td>
              <td class="px-4 py-3.5">
                <div class="flex items-center justify-center gap-1.5">
                  <button v-if="isStaff" @click="openModal(b)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer" title="Lihat Detail">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <template v-else>
                    <button @click="openModal(b)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer" title="Edit">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button @click="hapus(b)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all border-0 bg-transparent cursor-pointer" title="Hapus">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                    </button>
                  </template>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Mobile/Tablet Card View (Hidden on desktop) -->
      <div class="block md:hidden divide-y divide-gray-100">
        <div v-if="filtered.length === 0 && !loading" class="text-center py-12 text-gray-400 text-sm">
          Tidak ada data ditemukan.
        </div>
        <div v-for="b in paginated" :key="b.id" class="p-4 space-y-3">
          <div class="flex items-start gap-3">
            <div class="shrink-0 mt-1">
                <img v-if="b.gambar" :src="b.gambar" alt="Gambar" class="w-12 h-12 object-cover rounded-lg border border-gray-100" />
                <div v-else class="w-12 h-12 rounded-lg bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-300">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
            </div>
            <div class="space-y-1 flex-1">
              <div class="flex justify-between items-start">
                  <span class="text-[10px] font-mono font-semibold text-indigo-500 bg-indigo-50 px-2 py-0.5 rounded-lg">{{ b.kode }}</span>
                  <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0" :class="statusBadge(b)">
                    {{ statusLabel(b) }}
                  </span>
              </div>
              <h3 class="font-bold text-gray-800 text-[14.5px] m-0 mt-1">{{ b.nama }}</h3>
              <p class="text-gray-400 text-[12px] m-0">{{ b.kategori }} • {{ b.lokasi }}</p>
            </div>
          </div>

          <div class="pt-2 border-t border-gray-50 text-[12px] text-gray-500">
            <div>
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Stok</span>
              <span class="font-bold text-[13px]" :class="statusColor(b)">{{ b.stok }} {{ b.satuan }}</span>
            </div>
          </div>

          <div class="flex justify-end pt-2.5 border-t border-gray-50 text-[12px]">
            <div class="flex gap-1.5">
              <button v-if="isStaff" @click="openModal(b)" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-semibold border-0 cursor-pointer transition-all">
                Detail
              </button>
              <template v-else>
                <button @click="openModal(b)" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-semibold border-0 cursor-pointer transition-all">
                  Edit
                </button>
                <button @click="hapus(b)" class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-xs font-semibold border-0 cursor-pointer transition-all">
                  Hapus
                </button>
              </template>
            </div>
          </div>
        </div>
      </div>

      <!-- Shared Responsive Pagination -->
      <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3.5 border-t border-gray-100 bg-white">
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

    <!-- MODAL -->
    <Modal :show="modal" :title="(isStaff && form.id) ? 'Detail Barang' : (form.id ? 'Edit Barang' : 'Tambah Barang')" maxWidth="max-w-lg" @close="modal = false">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Kode Barang</label>
            <input v-model="form.kode" disabled type="text" placeholder="BRG-0001" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none transition-all bg-gray-50 text-gray-500 cursor-not-allowed"/>
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Nama Barang</label>
            <input v-model="form.nama" :disabled="isStaff && !!form.id" type="text" placeholder="Nama barang" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500"/>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Kategori</label>
            <select v-model="form.kategori" :disabled="isStaff && !!form.id" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500">
              <option value="">Pilih kategori</option>
              <option v-for="k in kategoris" :key="k" :value="k">{{ k }}</option>
            </select>
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Lokasi</label>
            <select v-model="form.lokasi" :disabled="isStaff && !!form.id" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500">
              <option value="">Pilih lokasi</option>
              <option v-for="l in lokasis" :key="l" :value="l">{{ l }}</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Satuan</label>
            <select v-model="form.satuan" :disabled="isStaff && !!form.id" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500">
              <option value="">Pilih satuan</option>
              <option v-for="s in satuans" :key="s" :value="s">{{ s }}</option>
            </select>
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Stok</label>
            <input v-model.number="form.stok" :disabled="isStaff && !!form.id" type="number" min="0" placeholder="0" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500"/>
          </div>
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Stok Minimum <span class="text-red-500">*</span></label>
          <input v-model.number="form.minStok" :disabled="isStaff && !!form.id" type="number" min="1" required placeholder="1" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500"/>
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Gambar Barang <span class="text-gray-400 font-normal">(opsional)</span></label>
          <div v-if="form.gambarPreview || form.gambar" class="mb-3">
            <img :src="form.gambarPreview || form.gambar" @click="zoomImage(form.gambarPreview || form.gambar, form.nama)" class="h-32 rounded-xl object-cover border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity" title="Klik untuk perbesar" />
          </div>
          <input type="file" accept="image/*" @change="handleFileChange" :disabled="isStaff && !!form.id" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-sm text-gray-700 bg-white disabled:bg-gray-50" />
          <p class="text-[11px] text-gray-400 mt-1">Gambar akan otomatis dikompres sebelum diunggah.</p>
        </div>
      </div>
      <template #footer>
        <button @click="modal = false" class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">
          {{ (isStaff && form.id) ? 'Tutup' : 'Batal' }}
        </button>
        <button v-if="!isStaff || !form.id" @click="simpan" class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-500 hover:bg-indigo-600 text-white border-0 cursor-pointer transition-all">
          {{ form.id ? 'Simpan Perubahan' : 'Tambah Barang' }}
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
  name: 'DataBarang',
  components: {
    Modal
  },
  data: () => ({
    loading: false,
    searchInput: '',
    search: '',
    filterKategori: '',
    filterLokasi: '',
    filterSatuan: '',
    filterStatus: '',
    page: 1,
    perPage: 10,
    modal: false,
    form: {},
    kategoris: [],
    lokasis:   [],
    satuans:   [],
    barangs:   [],
  }),
  computed: {
    currentUser() {
      return useAuth().user.value
    },
    isStaff() {
      return this.currentUser?.role === 'user'
    },
    filtered() {
      return this.barangs.filter(b => {
        const q = this.search.toLowerCase()
        const matchQ = !q || b.nama.toLowerCase().includes(q) || b.kode.toLowerCase().includes(q)
        const matchK = !this.filterKategori || b.kategori === this.filterKategori
        const matchL = !this.filterLokasi   || b.lokasi === this.filterLokasi
        const matchSat = !this.filterSatuan || b.satuan === this.filterSatuan
        const matchS = !this.filterStatus  || this.statusKey(b) === this.filterStatus || (this.filterStatus === 'kritis' && b.stok <= 0)
        return matchQ && matchK && matchL && matchSat && matchS
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
    filterLokasi()  { this.page = 1 },
    filterSatuan()  { this.page = 1 },
    filterStatus()  { this.page = 1 },
    perPage()       { this.page = 1 },
    '$route.query'() {
      this.applyRouteQuery()
    },
  },
  mounted() {
    this.applyRouteQuery()
    this.fetchData()
  },
  methods: {
    applyRouteQuery() {
      if (this.$route.query.search !== undefined) {
        this.searchInput = this.$route.query.search || ''
        this.search = this.$route.query.search || ''
      }
      if (this.$route.query.kategori !== undefined) {
        this.filterKategori = this.$route.query.kategori || ''
      }
      if (this.$route.query.lokasi !== undefined) {
        this.filterLokasi = this.$route.query.lokasi || ''
      }
      if (this.$route.query.satuan !== undefined) {
        this.filterSatuan = this.$route.query.satuan || ''
      }
    },
    async fetchData() {
      const cached = getCache('master_data_barang')
      if (cached) {
        this.barangs = cached.barangs
        this.kategoris = cached.kategoris
        this.lokasis = cached.lokasis
        this.satuans = cached.satuans
      } else {
        this.loading = true
      }
      try {
        const [resB, resK, resL, resSat] = await Promise.all([
          api.get('/barangs'),
          api.get('/kategoris'),
          api.get('/lokasis'),
          api.get('/satuans'),
        ])
        this.barangs = resB.data
        this.kategoris = resK.data.map(item => item.nama)
        this.lokasis = resL.data.map(item => item.nama)
        this.satuans = resSat.data.map(item => item.nama)
        
        setCache('master_data_barang', {
          barangs: this.barangs,
          kategoris: this.kategoris,
          lokasis: this.lokasis,
          satuans: this.satuans
        })
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
      this.filterLokasi = ''
      this.filterSatuan = ''
      this.filterStatus = ''
      if (Object.keys(this.$route.query).length > 0) {
        this.$router.replace({ query: {} })
      }
      this.fetchData()
    },
    statusKey(b)   { return b.stok <= 0 ? 'habis' : (b.stok < b.minStok ? 'rendah' : 'aman') },
    statusLabel(b) { return { habis:'Habis', rendah:'Rendah', aman:'Aman' }[this.statusKey(b)] },
    statusBadge(b) { return { habis:'bg-red-50 text-red-500', rendah:'bg-yellow-50 text-yellow-600', aman:'bg-green-50 text-green-600' }[this.statusKey(b)] },
    statusColor(b) { return { habis:'text-red-500', rendah:'text-yellow-500', aman:'text-gray-700' }[this.statusKey(b)] },
    formatRupiah(n){ return 'Rp ' + Number(n).toLocaleString('id-ID') },
    async openModal(b = null) {
      if (b) {
        this.form = { ...b, gambarFile: null, gambarPreview: null }
      } else {
        this.form = { id:null, kode:'Sedang memuat...', nama:'', kategori:'', lokasi:'', satuan:'', stok:0, minStok:1, gambarFile: null, gambarPreview: null }
        try {
          const response = await api.get('/barangs/next-kode')
          this.form.kode = response.data.kode
        } catch (error) {
          console.error('Error fetching next code:', error)
          this.form.kode = ''
        }
      }
      this.modal = true
    },
    async simpan() {
      if (this.isStaff && this.form.id) return
      
      if (!this.form.kode || !this.form.nama || this.form.minStok === '' || this.form.minStok === null || this.form.minStok === undefined) {
        const { toastError } = useSwal()
        toastError('Nama Barang dan Stok Minimum wajib diisi!')
        return
      }

      const formData = new FormData()
      formData.append('kode', this.form.kode)
      formData.append('nama', this.form.nama)
      formData.append('kategori', this.form.kategori)
      formData.append('lokasi', this.form.lokasi)
      formData.append('satuan', this.form.satuan)
      formData.append('stok', this.form.stok)
      formData.append('minStok', this.form.minStok)
      
      if (this.form.gambarFile) {
        formData.append('gambar', this.form.gambarFile)
      }

      const { toastSuccess, toastError, confirmEdit } = useSwal()

      try {
        if (this.form.id) {
          const isConfirm = await confirmEdit('Simpan Perubahan Barang?', 'Pastikan data yang diubah sudah benar.')
          if (!isConfirm) return
          
          formData.append('_method', 'PUT')
          const response = await api.post(`/barangs/${this.form.id}`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          const idx = this.barangs.findIndex(b => b.id === this.form.id)
          if (idx !== -1) {
            this.barangs[idx] = response.data
          }
        } else {
          const response = await api.post('/barangs', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
          })
          this.barangs.unshift(response.data)
        }
        this.modal = false
        toastSuccess('Barang berhasil disimpan!')
      } catch (error) {
        console.error('Error saving barang:', error)
        toastError(error.response?.data?.message || 'Gagal menyimpan data barang')
      }
    },
    handleFileChange(e) {
      const file = e.target.files[0]
      if (!file) return
      
      this.compressImage(file, 800, 800, 0.8).then(compressedFile => {
        this.form.gambarFile = compressedFile
        this.form.gambarPreview = URL.createObjectURL(compressedFile)
      }).catch(err => {
        console.error('Error compressing image:', err)
        const { toastError } = useSwal()
        toastError('Gagal memproses gambar.')
      })
    },
    compressImage(file, maxWidth, maxHeight, quality) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = event => {
          const img = new Image()
          img.src = event.target.result
          img.onload = () => {
            let width = img.width
            let height = img.height
            if (width > height) {
              if (width > maxWidth) {
                height = Math.round(height *= maxWidth / width)
                width = maxWidth
              }
            } else {
              if (height > maxHeight) {
                width = Math.round(width *= maxHeight / height)
                height = maxHeight
              }
            }
            const canvas = document.createElement('canvas')
            canvas.width = width
            canvas.height = height
            const ctx = canvas.getContext('2d')
            ctx.drawImage(img, 0, 0, width, height)
            canvas.toBlob(blob => {
              if (!blob) return reject(new Error('Canvas toBlob failed'))
              resolve(new File([blob], file.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
              }))
            }, 'image/jpeg', quality)
          }
          img.onerror = error => reject(error)
        }
        reader.onerror = error => reject(error)
      })
    },
    zoomImage(url, nama) {
      const { viewImage } = useSwal()
      viewImage(url, nama || 'Pratinjau Gambar')
    },
    async hapus(b) {
      if (this.isStaff) return
      const { confirmDelete, toastSuccess, toastError } = useSwal()
      const confirmed = await confirmDelete(b.nama, 'Barang')
      if (!confirmed) return
      try {
        await api.delete(`/barangs/${b.id}`)
        this.barangs = this.barangs.filter(x => x.id !== b.id)
        toastSuccess('Barang berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting barang:', error)
        toastError(error.response?.data?.message || 'Gagal menghapus barang')
      }
    },
  },
}
</script>