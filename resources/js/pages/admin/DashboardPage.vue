<template>
  <div class="space-y-4 md:space-y-6">

    <!-- Header -->
    <div>
      <h1 class="text-xl font-bold text-gray-800 m-0 capitalize">Selamat datang, {{ currentUser?.name || 'Admin' }} 👋</h1>
      <p class="text-gray-400 text-sm mt-1 m-0">Berikut ringkasan data inventaris hari ini.</p>
    </div>

    <!-- ── Stat Cards ── -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
      <div v-for="s in stats" :key="s.label"
        class="bg-white rounded-2xl p-4 border border-gray-100 cursor-pointer transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-black/5"
        @click="$router.push(s.route)">
        <div class="flex items-start justify-between mb-3">
          <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="s.iconBg">
            <svg class="w-5 h-5" :class="s.iconColor" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" v-html="s.path"></svg>
          </div>
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full" :class="s.badgeCls">{{ s.change }}</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800 m-0">{{ s.value }}</p>
        <p class="text-xs text-gray-400 mt-0.5 m-0">{{ s.label }}</p>
      </div>
    </div>

    <!-- ── Row 2: Stok Menipis + Aktivitas ── -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

      <!-- Tabel stok menipis -->
      <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <p class="font-semibold text-gray-800 text-sm m-0">Stok Menipis</p>
          <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-500">⚠ Perlu Perhatian</span>
        </div>
        <div class="hidden md:block overflow-x-auto">
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50">
                <th class="text-left px-5 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Barang</th>
                <th class="text-left px-4 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Kategori
                </th>
                <th class="text-left px-4 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Lokasi</th>
                <th class="text-left px-4 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Stok</th>
                <th class="text-left px-4 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wide">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
              <tr v-if="lowStock.length === 0">
                <td colspan="5" class="text-center py-8 text-gray-400 text-[13px]">Stok semua barang aman.</td>
              </tr>
              <tr v-for="r in lowStock" :key="r.name" class="hover:bg-gray-50 transition-all">
                <td class="px-5 py-3 font-medium text-gray-700 text-[13px]">{{ r.name }}</td>
                <td class="px-4 py-3 text-gray-400 text-[12.5px]">{{ r.cat }}</td>
                <td class="px-4 py-3 text-gray-400 text-[12.5px]">{{ r.lokasi }}</td>
                <td class="px-4 py-3 font-bold text-[13px]" :class="r.stok <= 0 ? 'text-red-500' : 'text-yellow-500'">{{
                  r.stok }} {{ r.unit }}</td>
                <td class="px-4 py-3">
                  <span class="text-[11px] font-bold px-2.5 py-1 rounded-full"
                    :class="r.stok <= 0 ? 'bg-red-50 text-red-500' : 'bg-yellow-50 text-yellow-600'">
                    {{ r.stok <= 0 ? 'Habis' : 'Rendah' }} </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Mobile/Tablet Card View for Low Stock -->
        <div class="block md:hidden divide-y divide-gray-100">
          <div v-if="lowStock.length === 0" class="text-center py-8 text-gray-400 text-[13px]">
            Stok semua barang aman.
          </div>
          <div v-for="r in lowStock" :key="r.name" class="p-4 space-y-2">
            <div class="flex justify-between items-start">
              <h3 class="font-medium text-gray-700 text-[13.5px] m-0">{{ r.name }}</h3>
              <span class="text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0"
                :class="r.stok <= 0 ? 'bg-red-50 text-red-500' : 'bg-yellow-50 text-yellow-600'">
                {{ r.stok <= 0 ? 'Habis' : 'Rendah' }} 
              </span>
            </div>
            <div class="flex justify-between items-end">
              <p class="text-gray-400 text-[11.5px] m-0">{{ r.cat }} • {{ r.lokasi }}</p>
              <p class="font-bold text-[13px] m-0" :class="r.stok <= 0 ? 'text-red-500' : 'text-yellow-500'">
                {{ r.stok }} {{ r.unit }}
              </p>
            </div>
          </div>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">
          <button @click="$router.push('/admin/laporan-stok')"
            class="text-[12.5px] font-semibold text-indigo-500 hover:text-indigo-700 border-0 bg-transparent cursor-pointer transition-all">
            Lihat laporan stok lengkap →
          </button>
        </div>
      </div>

      <!-- Aktivitas -->
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
          <p class="font-semibold text-gray-800 text-sm m-0">Aktivitas Terbaru</p>
        </div>
        <div class="divide-y divide-gray-50">
          <div v-if="activity.length === 0" class="text-center py-12 text-gray-400 text-xs">
            Belum ada aktivitas tercatat.
          </div>
          <div v-for="a in activity" :key="a.id"
            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-all">
            <div class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5" :class="a.bg">
              <svg class="w-3.5 h-3.5" :class="a.color" fill="none" stroke="currentColor" stroke-width="2.2"
                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" v-html="a.path"></svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[12.5px] font-medium text-gray-700 m-0 leading-snug">{{ a.text }}</p>
              <p class="text-[11px] text-gray-300 m-0 mt-0.5">{{ a.time }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── Row 3: Transaksi Terakhir ── -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

      <!-- Barang Masuk -->
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <p class="font-semibold text-gray-800 text-sm m-0">Barang Masuk Terbaru</p>
          <button @click="$router.push('/admin/barang-masuk')"
            class="text-[11.5px] font-semibold text-indigo-500 hover:text-indigo-700 border-0 bg-transparent cursor-pointer">Lihat
            semua →</button>
        </div>
        <div class="divide-y divide-gray-50">
          <div v-if="masuk.length === 0" class="text-center py-8 text-gray-400 text-xs">
            Belum ada barang masuk.
          </div>
          <div v-for="t in masuk" :key="t.id" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-all">
            <div class="w-8 h-8 rounded-xl bg-green-50 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19" />
                <polyline points="19 12 12 19 5 12" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] font-medium text-gray-700 m-0 truncate">{{ t.barang }}</p>
              <p class="text-[11.5px] text-gray-400 m-0">{{ t.supplier }} · {{ t.tanggal }}</p>
            </div>
            <span class="text-[13px] font-bold text-green-600 shrink-0">+{{ t.qty }}</span>
          </div>
        </div>
      </div>

      <!-- Barang Keluar -->
      <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
          <p class="font-semibold text-gray-800 text-sm m-0">Barang Keluar Terbaru</p>
          <button @click="$router.push('/admin/barang-keluar')"
            class="text-[11.5px] font-semibold text-indigo-500 hover:text-indigo-700 border-0 bg-transparent cursor-pointer">Lihat
            semua →</button>
        </div>
        <div class="divide-y divide-gray-50">
          <div v-if="keluar.length === 0" class="text-center py-8 text-gray-400 text-xs">
            Belum ada barang keluar.
          </div>
          <div v-for="t in keluar" :key="t.id"
            class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50 transition-all">
            <div class="w-8 h-8 rounded-xl bg-orange-50 flex items-center justify-center shrink-0">
              <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <line x1="12" y1="19" x2="12" y2="5" />
                <polyline points="5 12 12 5 19 12" />
              </svg>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-[13px] font-medium text-gray-700 m-0 truncate">{{ t.barang }}</p>
              <p class="text-[11.5px] text-gray-400 m-0">{{ t.tujuan }} · {{ t.tanggal }}</p>
            </div>
            <span class="text-[13px] font-bold text-orange-500 shrink-0">-{{ t.qty }}</span>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import api from '@/utils/api'
import { useAuth } from '@/composables/useAuth'
import { getCache, setCache } from '@/utils/cache'

export default {
  name: 'Dashboard',
  computed: {
    currentUser() {
      return useAuth().user.value
    }
  },
  data: () => ({
    stats: [
      {
        label: 'Total Barang', value: '-', change: 'Bulan ini', route: '/admin/barang',
        iconBg: 'bg-indigo-50', iconColor: 'text-indigo-500',
        badgeCls: 'bg-indigo-50 text-indigo-500',
        path: `<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>`,
      },
      {
        label: 'Barang Masuk', value: '-', change: 'Hari ini', route: '/admin/barang-masuk',
        iconBg: 'bg-green-50', iconColor: 'text-green-500',
        badgeCls: 'bg-green-50 text-green-600',
        path: `<line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>`,
      },
      {
        label: 'Barang Keluar', value: '-', change: 'Hari ini', route: '/admin/barang-keluar',
        iconBg: 'bg-orange-50', iconColor: 'text-orange-500',
        badgeCls: 'bg-orange-50 text-orange-500',
        path: `<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>`,
      },
      {
        label: 'Stok Habis / Rendah', value: '-', change: 'Perlu restock', route: '/admin/laporan-stok',
        iconBg: 'bg-red-50', iconColor: 'text-red-500',
        badgeCls: 'bg-red-50 text-red-500',
        path: `<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>`,
      },
    ],
    lowStock: [],
    activity: [],
    masuk: [],
    keluar: [],
  }),
  mounted() {
    this.fetchStats()
  },
  methods: {
    async fetchStats() {
      const cached = getCache('dashboard_stats')
      if (cached) {
        this.stats = cached.stats
        this.lowStock = cached.lowStock
        this.activity = cached.activity
        this.masuk = cached.masuk
        this.keluar = cached.keluar
      }
      try {
        const response = await api.get('/dashboard-stats')
        const data = response.data

        this.stats[0].value = data.totalBarang.toString()
        this.stats[0].change = `+${data.newBarangsThisMonth} baru`

        this.stats[1].value = data.totalMasukToday.toString()
        this.stats[1].change = `+${data.masukThisMonth} bln ini`

        this.stats[2].value = data.totalKeluarToday.toString()
        this.stats[2].change = `-${data.keluarThisMonth} bln ini`

        this.stats[3].value = data.stokKritisCount.toString()
        this.stats[3].change = data.stokKritisCount > 0 ? 'Perlu perhatian' : 'Stok aman'

        this.lowStock = data.lowStock
        this.activity = data.activity
        this.masuk = data.masuk
        this.keluar = data.keluar
        
        setCache('dashboard_stats', {
          stats: this.stats,
          lowStock: this.lowStock,
          activity: this.activity,
          masuk: this.masuk,
          keluar: this.keluar
        })
      } catch (error) {
        console.error('Error fetching dashboard stats:', error)
      }
    }
  }
}
</script>