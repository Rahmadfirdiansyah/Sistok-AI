<template>
  <div class="space-y-4 md:space-y-5">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0">
      <div>
        <h1 class="text-xl font-bold text-gray-800 m-0">Barang Keluar</h1>
        <p class="text-gray-400 text-sm mt-1 m-0">Catat setiap pengeluaran barang dari gudang.</p>
      </div>
      <button v-if="!isStaff" @click="openModal()"
        class="w-full sm:w-auto flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-500 hover:bg-indigo-600 text-white text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <line x1="12" y1="5" x2="12" y2="19" />
          <line x1="5" y1="12" x2="19" y2="12" />
        </svg>
        Catat Keluar
      </button>
    </div>

    <!-- Stat mini -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div v-for="s in stats" :key="s.label" class="bg-white rounded-2xl border border-gray-100 px-4 py-3.5">
        <p class="text-xs text-gray-400 m-0">{{ s.label }}</p>
        <p class="text-xl font-extrabold m-0 mt-0.5" :class="s.color">{{ s.value }}</p>
      </div>
    </div>

    <!-- Filter & Search -->
    <div class="grid grid-cols-12 sm:flex sm:flex-row gap-3">
      <div class="relative col-span-10 sm:flex-1">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="8" />
          <line x1="21" y1="21" x2="16.65" y2="16.65" />
        </svg>
        <input v-model="searchInput" type="text" placeholder="Cari barang, pemakai atau tujuan..."
          class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all" />
      </div>
      <button @click="resetFilter" class="col-span-2 sm:col-auto flex items-center justify-center px-3 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-xl transition-all border-0 cursor-pointer" title="Refresh / Reset Filter">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
      </button>
      <input v-model="filterTanggal" type="date"
        class="col-span-6 sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white" />
      <select v-model="filterJenis"
        class="col-span-6 sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
        <option value="">Semua Jenis</option>
        <option value="pemakaian">🛠️ Pemakaian</option>
        <option value="limbah">🗑️ Limbah</option>
      </select>
      <select v-model="filterLokasi"
        class="col-span-12 sm:w-auto px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-600 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white">
        <option value="">Semua Lokasi</option>
        <option v-for="l in lokasis" :key="l" :value="l">{{ l }}</option>
      </select>
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
              <th class="text-left px-5 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">#</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Barang</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Dipakai Oleh</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Tujuan</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Jenis</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Lokasi Asal</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Qty Keluar</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Tanggal</th>
              <th class="text-left px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Keterangan</th>
              <th class="text-center px-4 py-3.5 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-if="paginated.length === 0 && !loading">
              <td colspan="10" class="text-center py-12 text-gray-400 text-sm">Tidak ada data ditemukan.</td>
            </tr>
            <tr v-for="(b, idx) in paginated" :key="b.id" class="hover:bg-gray-50 transition-all">
              <td class="px-5 py-3.5 text-gray-400 text-[12.5px]">{{ (page - 1) * perPage + idx + 1 }}</td>
              <td class="px-4 py-3.5">
                <p class="font-medium text-gray-700 m-0">{{ b.barang }}</p>
                <p class="text-[11.5px] text-gray-400 m-0">{{ b.kodeBarang }}</p>
              </td>
              <td class="px-4 py-3.5 text-gray-600 font-medium text-[12.5px]">{{ b.dipakaiOleh }}</td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.tujuan }}</td>
              <td class="px-4 py-3.5 text-center">
                <span v-if="b.jenisKeluar === 'limbah'" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded-full border border-red-100">
                  🗑️ Limbah
                </span>
                <span v-else class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-full">
                  🛠️ Pemakaian
                </span>
              </td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.lokasi }}</td>
              <td class="px-4 py-3.5 text-center">
                <span class="inline-flex items-center gap-1 font-bold text-orange-500 bg-orange-50 px-3 py-1 rounded-full text-[12.5px]">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="19" x2="12" y2="5" />
                    <polyline points="5 12 12 5 19 12" />
                  </svg>
                  -{{ b.qty }} {{ b.satuan }}
                </span>
              </td>
              <td class="px-4 py-3.5 text-gray-500 text-[12.5px]">{{ b.tanggal }}</td>
              <td class="px-4 py-3.5 text-gray-400 text-[12.5px] max-w-[120px] truncate">{{ b.keterangan || '-' }}</td>
              <td class="px-4 py-3.5">
                <div class="flex items-center justify-center gap-1.5">
                  <button v-if="isStaff" @click="openModal(b)" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer" title="Lihat Detail">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                      <circle cx="12" cy="12" r="10"/>
                      <circle cx="12" cy="12" r="3"/>
                    </svg>
                  </button>
                  <template v-else>
                    <button @click="openModal(b)"
                      class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-indigo-50 text-gray-400 hover:text-indigo-500 transition-all border-0 bg-transparent cursor-pointer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                      </svg>
                    </button>
                    <button @click="hapus(b)"
                      class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-red-50 text-gray-400 hover:text-red-500 transition-all border-0 bg-transparent cursor-pointer">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                        <path d="M10 11v6" />
                        <path d="M14 11v6" />
                        <path d="M9 6V4h6v2" />
                      </svg>
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
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <h3 class="font-bold text-gray-800 text-[14.5px] m-0">{{ b.barang }}</h3>
              <p class="text-gray-400 text-[12px] m-0">{{ b.kodeBarang }}</p>
            </div>
            <span class="inline-flex items-center gap-1 font-bold text-orange-500 bg-orange-50 px-2 py-0.5 rounded-full text-[12px]">
              -{{ b.qty }} {{ b.satuan }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-2 text-[12px] pt-2 border-t border-gray-50 text-gray-500">
            <div>
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Dipakai Oleh</span>
              <span class="font-medium text-gray-700">{{ b.dipakaiOleh }}</span>
            </div>
            <div class="text-right">
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tujuan</span>
              <span class="font-medium text-gray-700">{{ b.tujuan }}</span>
            </div>
            <div>
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Lokasi Asal</span>
              <span class="font-medium text-gray-700">{{ b.lokasi }}</span>
            </div>
            <div class="text-right">
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Tanggal</span>
              <span class="font-medium text-gray-700">{{ b.tanggal }}</span>
            </div>
            <div class="col-span-2 mt-1">
              <span class="text-gray-400 block text-[10px] uppercase font-semibold">Keterangan</span>
              <span class="font-medium text-gray-700 truncate max-w-full inline-block">{{ b.keterangan || '-' }}</span>
            </div>
          </div>

          <div class="flex items-center justify-between pt-2.5 border-t border-gray-50 text-[12px]">
            <span class="text-gray-400">Ket: <span class="font-medium text-gray-600 truncate max-w-[120px] inline-block align-middle">{{ b.keterangan || '-' }}</span></span>
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
    <Modal :show="modal" :title="isStaff ? 'Detail Barang Keluar' : (form.id ? 'Edit Barang Keluar' : 'Catat Barang Keluar')" maxWidth="max-w-lg" @close="modal = false">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Barang</label>
            <SearchableSelect
              v-model="form.barang_id"
              :options="sortedDaftarBarang"
              labelKey="nama"
              valueKey="id"
              placeholder="Pilih barang..."
              :disabled="isStaff && !!form.id"
              inputClass="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500 cursor-text"
            />
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Lokasi Asal</label>
            <select v-model="form.lokasi" :disabled="(isStaff && !!form.id) || !form.barang_id"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white disabled:bg-gray-50 disabled:text-gray-500">
              <option value="">Pilih lokasi</option>
              <option v-for="l in availableLokasis" :key="l" :value="l">{{ l }}</option>
            </select>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Dipakai Oleh</label>
            <input v-model="form.dipakaiOleh" :disabled="(isStaff && !!form.id) || !form.barang_id" type="text" placeholder="Nama teknisi / staf"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all disabled:bg-gray-50 disabled:text-gray-500" />
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Tujuan Penggunaan</label>
            <input v-model="form.tujuan" :disabled="(isStaff && !!form.id) || !form.barang_id" type="text" placeholder="cth: Maintenance BTS"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all disabled:bg-gray-50 disabled:text-gray-500" />
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Qty Keluar</label>
            <input v-model.number="form.qty" :disabled="(isStaff && !!form.id) || !form.barang_id" type="number" min="1" :max="maxQty" placeholder="0"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all disabled:bg-gray-50 disabled:text-gray-500" />
          </div>
          <div>
            <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Tanggal Keluar</label>
            <input v-model="form.tanggal" :disabled="(isStaff && !!form.id) || !form.barang_id" type="date"
              class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all disabled:bg-gray-50 disabled:text-gray-500" />
          </div>
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Jenis Pengeluaran</label>
          <div class="grid grid-cols-2 gap-2.5">
            <button type="button"
              @click="!((isStaff && !!form.id) || !form.barang_id) && (form.jenisKeluar = 'pemakaian')"
              :disabled="(isStaff && !!form.id) || !form.barang_id"
              class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold transition-all border-0 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
              :class="form.jenisKeluar === 'pemakaian'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-600/20'
                : 'bg-gray-100 hover:bg-gray-200 text-gray-600'">
              <span>🛠️</span>
              <span>Pemakaian / Operasional</span>
            </button>

            <button type="button"
              @click="!((isStaff && !!form.id) || !form.barang_id) && (form.jenisKeluar = 'limbah')"
              :disabled="(isStaff && !!form.id) || !form.barang_id"
              class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-xs font-semibold transition-all border-0 cursor-pointer disabled:cursor-not-allowed disabled:opacity-50"
              :class="form.jenisKeluar === 'limbah'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-500/20 ring-2 ring-indigo-600/20'
                : 'bg-gray-100 hover:bg-gray-200 text-gray-600'">
              <span>🗑️</span>
              <span>Limbah / Rusak / Afkir</span>
            </button>
          </div>
        </div>
        <div>
          <label class="block text-[11.5px] font-semibold text-gray-500 mb-1.5">Keterangan <span
              class="font-normal text-gray-300">(opsional)</span></label>
          <textarea v-model="form.keterangan" :disabled="(isStaff && !!form.id) || !form.barang_id" rows="2" placeholder="Catatan tambahan..."
            class="w-full px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all resize-none disabled:bg-gray-50 disabled:text-gray-500"></textarea>
        </div>
      </div>
      <template #footer>
        <button @click="modal = false"
          class="px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-gray-100 border-0 bg-transparent cursor-pointer transition-all">
          {{ (isStaff && form.id) ? 'Tutup' : 'Batal' }}
        </button>
        <button v-if="!isStaff || !form.id" @click="simpan" :disabled="!form.barang_id"
          class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-indigo-500 hover:bg-indigo-600 text-white border-0 cursor-pointer transition-all disabled:opacity-50 disabled:cursor-not-allowed">
          {{ form.id ? 'Simpan Perubahan' : 'Catat Keluar' }}
        </button>
      </template>
    </Modal>

  </div>
</template>

<script>
import api from '@/utils/api'
import Modal from '../../../components/Modal.vue'
import SearchableSelect from '@/components/SearchableSelect.vue'
import { useAuth } from '@/composables/useAuth'
import { useSwal } from '@/composables/useSwal'
import { getCache, setCache } from '@/utils/cache'

export default {
  name: 'BarangKeluar',
  components: {
    Modal,
    SearchableSelect
  },
  data: () => ({
    loading: false,
    searchInput: '',
    search: '',
    filterTanggal: '',
    filterJenis: '',
    filterLokasi: '',
    page: 1,
    perPage: 10,
    modal: false,
    form: {},
    lokasis: [],
    daftarBarang: [],
    transaksis: [],
  }),
  computed: {
    currentUser() {
      return useAuth().user.value
    },
    isStaff() {
      return this.currentUser?.role === 'user'
    },
    maxQty() {
      if (!this.form.barang_id) return 9999
      const selected = this.daftarBarang.find(b => b.id === this.form.barang_id)
      return selected ? selected.stok : 0
    },
    availableLokasis() {
      if (!this.form.barang_id) return this.lokasis
      const selected = this.daftarBarang.find(b => b.id === this.form.barang_id)
      if (selected && selected.lokasi) {
        return [selected.lokasi]
      }
      return this.lokasis
    },
    sortedDaftarBarang() {
      return [...this.daftarBarang].sort((a, b) => a.nama.localeCompare(b.nama))
    },
    filtered() {
      return this.transaksis.filter(t => {
        const q = this.search.toLowerCase()
        const matchQ = !q || t.barang.toLowerCase().includes(q) || t.dipakaiOleh.toLowerCase().includes(q) || t.tujuan.toLowerCase().includes(q) || t.kodeBarang.toLowerCase().includes(q)
        const matchT = !this.filterTanggal || t.tanggal === this.filterTanggal
        const matchJ = !this.filterJenis || (t.jenisKeluar || 'pemakaian') === this.filterJenis
        const matchL = !this.filterLokasi || t.lokasi === this.filterLokasi
        return matchQ && matchT && matchJ && matchL
      })
    },
    totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)) },
    paginated() { return this.filtered.slice((this.page - 1) * this.perPage, this.page * this.perPage) },
    from() { return this.filtered.length ? (this.page - 1) * this.perPage + 1 : 0 },
    to() { return Math.min(this.page * this.perPage, this.filtered.length) },
    stats() {
      const total = this.transaksis.length
      const hari = this.transaksis.filter(t => t.tanggal === new Date().toISOString().slice(0, 10)).length
      const totalQty = this.transaksis.reduce((s, t) => s + t.qty, 0)
      const pemakaiCount = [...new Set(this.transaksis.map(t => t.dipakaiOleh))].length
      return [
        { label: 'Total Transaksi', value: total, color: 'text-indigo-600' },
        { label: 'Transaksi Hari Ini', value: hari, color: 'text-green-600' },
        { label: 'Total Qty Keluar', value: totalQty, color: 'text-blue-600' },
        { label: 'Pemakai Aktif', value: pemakaiCount, color: 'text-orange-500' },
      ]
    },
  },
  watch: {
    searchInput(val) {
      clearTimeout(this._searchTimer)
      this._searchTimer = setTimeout(() => {
        this.search = val
      }, 300)
    },
    search() { this.page = 1 },
    filterTanggal() { this.page = 1 },
    filterJenis() { this.page = 1 },
    filterLokasi() { this.page = 1 },
    perPage() { this.page = 1 },
    'form.barang_id'(newVal) {
      if (newVal) {
        const selected = this.daftarBarang.find(b => b.id === newVal)
        if (selected && selected.lokasi) {
          this.form.lokasi = selected.lokasi
        }
      }
    },
  },
  mounted() {
    this.fetchData()
  },
  methods: {
    async fetchData() {
      const cached = getCache('transaksi_keluar')
      if (cached) {
        this.transaksis = cached.transaksis
        this.daftarBarang = cached.daftarBarang
        this.lokasis = cached.lokasis
      } else {
        this.loading = true
      }
      try {
        const [resT, resB, resL] = await Promise.all([
          api.get('/barang-keluars'),
          api.get('/barangs'),
          api.get('/lokasis'),
        ])
        this.transaksis = resT.data
        this.daftarBarang = resB.data
        this.lokasis = resL.data.map(item => item.nama)
        
        setCache('transaksi_keluar', {
          transaksis: this.transaksis,
          daftarBarang: this.daftarBarang,
          lokasis: this.lokasis
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
      this.filterTanggal = ''
      this.filterJenis = ''
      this.filterLokasi = ''
      this.fetchData()
    },
    openModal(b = null) {
      this.form = b
        ? { ...b, jenisKeluar: b.jenisKeluar || 'pemakaian' }
        : { id: null, barang_id: '', lokasi: '', qty: '', dipakaiOleh: '', tujuan: '', jenisKeluar: 'pemakaian', tanggal: new Date().toISOString().slice(0, 10), keterangan: '' }
      this.modal = true
    },
    async simpan() {
      if (!this.form.barang_id || !this.form.qty) return
      const { toastSuccess, toastError, confirmEdit, confirmCriticalStock } = useSwal()
      
      const item = this.daftarBarang.find(b => b.id === this.form.barang_id)
      if (item) {
        let oldQty = 0
        if (this.form.id) {
          const oldTx = this.transaksis.find(t => t.id === this.form.id)
          if (oldTx && oldTx.barang_id === this.form.barang_id) {
            oldQty = oldTx.qty
          }
        }
        
        const availableStock = item.stok + oldQty
        const resultStock = availableStock - this.form.qty
        
        if (resultStock <= item.minStok) {
          const confirmed = await confirmCriticalStock(item.nama)
          if (!confirmed) return
        }
      }

      try {
        const payload = {
          ...this.form,
          jenis_keluar: this.form.jenisKeluar || 'pemakaian'
        }
        if (this.form.id) {
          if (this.isStaff) return
          const isConfirm = await confirmEdit('Simpan Perubahan Barang Keluar?')
          if (!isConfirm) return
          const response = await api.put(`/barang-keluars/${this.form.id}`, payload)
          const idx = this.transaksis.findIndex(t => t.id === this.form.id)
          if (idx !== -1) {
            this.transaksis[idx] = response.data
          }
        } else {
          const response = await api.post('/barang-keluars', payload)
          this.transaksis.unshift(response.data)
        }
        this.modal = false
        toastSuccess('Transaksi keluar berhasil disimpan!')
      } catch (error) {
        console.error('Error saving barang keluar:', error)
        toastError(error.response?.data?.message || 'Gagal mencatat barang keluar')
      }
    },
    async hapus(b) {
      if (this.isStaff) return
      const { confirmDelete, toastSuccess, toastError } = useSwal()
      const confirmed = await confirmDelete(b.barang, 'Transaksi keluar')
      if (!confirmed) return
      try {
        await api.delete(`/barang-keluars/${b.id}`)
        this.transaksis = this.transaksis.filter(x => x.id !== b.id)
        toastSuccess('Transaksi keluar berhasil dihapus!')
      } catch (error) {
        console.error('Error deleting barang keluar:', error)
        toastError(error.response?.data?.message || 'Gagal menghapus transaksi')
      }
    },
  },
}
</script>