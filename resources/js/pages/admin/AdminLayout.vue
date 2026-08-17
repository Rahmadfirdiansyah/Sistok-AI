<template>
  <div class="flex w-full min-h-screen bg-slate-100">

    <!-- Sidebar -->
    <aside
      class="fixed left-0 top-0 z-50 flex flex-col w-60 h-screen bg-[#13142b] overflow-y-auto transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
    >

      <!-- Brand -->
      <router-link
        to="/admin/dashboard"
        class="flex items-center gap-3 px-4 py-4 border-b border-white/10 no-underline shrink-0"
      >
        <div
          class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 overflow-hidden p-1"
        >
          <img :src="'/img/logo.webp'" alt="DASEN" class="w-full h-full object-contain" />
        </div>

        <div class="leading-tight">
          <p class="text-white font-bold text-sm m-0">SiStok Dasen</p>
          <p class="text-white/35 text-[10.5px] m-0">Manajemen Stok Dasen</p>
        </div>
      </router-link>

      <!-- Menu -->
      <div class="flex flex-col gap-0.5 px-2 py-3 flex-1">

        <!-- Dashboard -->
        <router-link
          to="/admin/dashboard"
          class="flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-[13.5px] font-medium no-underline transition-all"
          :class="isActive('/admin/dashboard')
            ? 'bg-indigo-500/20 text-indigo-300'
            : 'text-white/50 hover:bg-white/5 hover:text-white/90'"
        >
          Dashboard
        </router-link>

        <!-- MASTER -->
        <p class="text-[9.5px] font-bold uppercase tracking-widest text-white/25 px-3 mt-4 mb-1">
          Master Data
        </p>

        <button
          @click="toggle('master')"
          class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] text-white/60 hover:bg-white/5"
        >
          <span>Master Data</span>
          <span>{{ open.master ? '−' : '+' }}</span>
        </button>

        <transition name="slide">
          <div v-show="open.master" class="flex flex-col gap-1 pl-4">

            <router-link
              v-for="m in filteredMasterMenu"
              :key="m.to"
              :to="m.to"
              class="px-3 py-2 rounded-lg text-[12.5px] no-underline transition-all"
              :class="isActive(m.to)
                ? 'bg-indigo-500/15 text-indigo-300'
                : 'text-white/40 hover:bg-white/5 hover:text-white/75'"
            >
              {{ m.label }}
            </router-link>

          </div>
        </transition>

        <!-- TRANSAKSI -->
        <p class="text-[9.5px] font-bold uppercase tracking-widest text-white/25 px-3 mt-4 mb-1">
          Transaksi
        </p>

        <button
          @click="toggle('transaksi')"
          class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] text-white/60 hover:bg-white/5"
        >
          <span>Transaksi</span>
          <span>{{ open.transaksi ? '−' : '+' }}</span>
        </button>

        <transition name="slide">
          <div v-show="open.transaksi" class="flex flex-col gap-1 pl-4">

            <router-link
              v-for="m in transaksiMenu"
              :key="m.to"
              :to="m.to"
              class="px-3 py-2 rounded-lg text-[12.5px] no-underline transition-all"
              :class="isActive(m.to)
                ? 'bg-indigo-500/15 text-indigo-300'
                : 'text-white/40 hover:bg-white/5 hover:text-white/75'"
            >
              {{ m.label }}
            </router-link>

          </div>
        </transition>

        <!-- LAPORAN -->
        <template v-if="filteredLaporanMenu.length > 0">
          <p class="text-[9.5px] font-bold uppercase tracking-widest text-white/25 px-3 mt-4 mb-1">
            Laporan
          </p>

          <button
            @click="toggle('laporan')"
            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] text-white/60 hover:bg-white/5"
          >
            <span>Laporan</span>
            <span>{{ open.laporan ? '−' : '+' }}</span>
          </button>

          <transition name="slide">
            <div v-show="open.laporan" class="flex flex-col gap-1 pl-4">

              <router-link
                v-for="m in filteredLaporanMenu"
                :key="m.to"
                :to="m.to"
                class="px-3 py-2 rounded-lg text-[12.5px] no-underline transition-all"
                :class="isActive(m.to)
                  ? 'bg-indigo-500/15 text-indigo-300'
                  : 'text-white/40 hover:bg-white/5 hover:text-white/75'"
              >
                {{ m.label }}
              </router-link>

            </div>
          </transition>
        </template>

        <!-- PENGATURAN (Only Superadmin) -->
        <template v-if="isSuperAdmin">
          <p class="text-[9.5px] font-bold uppercase tracking-widest text-white/25 px-3 mt-4 mb-1">
            Pengaturan
          </p>

          <button
            @click="toggle('pengaturan')"
            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[13.5px] text-white/60 hover:bg-white/5"
          >
            <span>Pengaturan</span>
            <span>{{ open.pengaturan ? '−' : '+' }}</span>
          </button>

          <transition name="slide">
            <div v-show="open.pengaturan" class="flex flex-col gap-1 pl-4">

              <router-link
                v-for="m in pengaturanMenu"
                :key="m.to"
                :to="m.to"
                class="px-3 py-2 rounded-lg text-[12.5px] no-underline transition-all"
                :class="isActive(m.to)
                  ? 'bg-indigo-500/15 text-indigo-300'
                  : 'text-white/40 hover:bg-white/5 hover:text-white/75'"
              >
                {{ m.label }}
              </router-link>

            </div>
          </transition>
        </template>

        <!-- Logout Button -->
        <button
          @click="logout"
          class="flex items-center gap-2.5 px-3 py-2.5 mt-auto mb-2 rounded-xl text-[13.5px] font-semibold text-red-400 hover:bg-red-500/10 transition-all border-0 bg-transparent text-left cursor-pointer"
        >
          <svg class="w-4.5 h-4.5 text-red-400" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
          Keluar / Logout
        </button>

      </div>
    </aside>

    <!-- Overlay -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/50 z-40 md:hidden"
      @click="sidebarOpen = false"
    ></div>

    <!-- Content -->
    <div class="flex-1 min-w-0 md:ml-60 flex flex-col min-h-screen">

      <!-- Header -->
      <header
        class="sticky top-0 z-30 bg-white border-b border-slate-200 px-4 md:px-6 py-4 flex items-center gap-4 shadow-sm"
      >

        <button
          @click="sidebarOpen = !sidebarOpen"
          class="md:hidden text-slate-500 hover:text-slate-800 border-0 bg-transparent cursor-pointer p-1 rounded-lg hover:bg-slate-50 transition-colors"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <div class="flex-1">
          <h2 class="text-sm font-semibold text-slate-800 capitalize m-0">
            Halo, {{ currentUser?.name || 'Pengguna' }} 👋
          </h2>

          <p class="text-[11px] md:text-xs text-slate-400 m-0 mt-0.5">
            {{ currentDate }}
          </p>
        </div>

        <!-- Transparent Overlay to close dropdown -->
        <div v-if="notifOpen" @click="notifOpen = false" class="fixed inset-0 z-40"></div>

        <!-- Notification Bell -->
        <div class="relative z-50">
          <button @click="notifOpen = !notifOpen" class="relative p-2 text-slate-400 hover:text-indigo-500 transition-colors cursor-pointer bg-slate-50 hover:bg-slate-100 rounded-full border-0" title="Notifikasi Stok Menipis">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
              <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <span v-if="notifCount > 0" class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 bg-red-500 border-2 border-white text-white text-[9px] font-bold flex items-center justify-center rounded-full">
              {{ notifCount }}
            </span>
          </button>

          <!-- Dropdown -->
          <transition name="slide-fade">
            <div v-if="notifOpen" class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
              <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-700 text-[13px] m-0">Notifikasi untuk SPB</h3>
                <span v-if="notifCount > 0" class="text-[10px] font-bold bg-red-50 text-red-500 px-2.5 py-0.5 rounded-full">{{ notifCount }} Peringatan</span>
              </div>
              
              <div class="max-h-64 overflow-y-auto divide-y divide-slate-50">
                <div v-if="lowStockItems.length === 0" class="px-4 py-6 text-center text-slate-400 text-xs">
                  Semua stok barang aman.
                </div>
                <div v-for="(item, idx) in lowStockItems" :key="idx" class="px-4 py-3 hover:bg-slate-50 transition-colors cursor-default">
                  <div class="flex items-start justify-between">
                    <div>
                      <p class="font-medium text-slate-700 text-[12.5px] m-0">{{ item.name }}</p>
                      <p class="text-[11px] text-slate-400 m-0">{{ item.cat }} • {{ item.lokasi }}</p>
                    </div>
                    <div class="text-right">
                      <p class="font-bold text-red-500 text-[13px] m-0">{{ item.stok }}</p>
                      <p class="text-[10px] text-slate-400 m-0">{{ item.unit }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="px-4 py-3 border-t border-slate-100 text-center" v-if="notifCount > 5">
                <router-link to="/admin/laporan-stok" @click="notifOpen = false" class="text-[12px] font-semibold text-indigo-500 hover:text-indigo-600 no-underline transition-colors block">
                  Lihat semua ({{ notifCount }}) &rarr;
                </router-link>
              </div>
              <div class="px-4 py-3 border-t border-slate-100 text-center" v-else-if="notifCount > 0">
                <router-link to="/admin/laporan-stok" @click="notifOpen = false" class="text-[12px] font-semibold text-indigo-500 hover:text-indigo-600 no-underline transition-colors block">
                  Lihat laporan stok &rarr;
                </router-link>
              </div>
            </div>
          </transition>
        </div>

      </header>

      <!-- Page -->
      <main class="flex-1 p-4 md:p-6">
        <RouterView />
      </main>

    </div>
    
    <!-- Smart AI Chat Input -->
    <AiChatBox />

  </div>
</template>

<script>
import { useAuth } from '@/composables/useAuth'
import { useSwal } from '@/composables/useSwal'
import api from '@/utils/api'
import AiChatBox from '@/components/admin/AiChatBox.vue'

export default {
  name: 'AdminLayout',
  components: {
    AiChatBox
  },

  data() {
    return {
      sidebarOpen: false,
      notifCount: 0,
      notifOpen: false,
      lowStockItems: [],

      open: {
        master: false,
        transaksi: false,
        laporan: false,
        pengaturan: false,
      },

      masterMenu: [
        { to: '/admin/barang', label: 'Data Barang' },
        { to: '/admin/kategori', label: 'Kategori' },
        { to: '/admin/lokasi', label: 'Lokasi' },
        { to: '/admin/satuan', label: 'Satuan' },
      ],

      transaksiMenu: [
        { to: '/admin/barang-masuk', label: 'Barang Masuk' },
        { to: '/admin/barang-keluar', label: 'Barang Keluar' },
      ],

      laporanMenu: [
        { to: '/admin/laporan-stok', label: 'Laporan Stok' },
        { to: '/admin/laporan-masuk', label: 'Laporan Masuk' },
        { to: '/admin/laporan-keluar', label: 'Laporan Keluar' },
        { to: '/admin/laporan-transaksi', label: 'Riwayat Transaksi' },
      ],

      pengaturanMenu: [
        { to: '/admin/pengguna', label: 'Manajemen Akun' },
      ],
    }
  },

  computed: {

    currentDate() {
      return new Date().toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      })
    },

    currentUser() {
      return useAuth().user.value
    },

    isStaff() {
      return this.currentUser?.role === 'user'
    },

    isSuperAdmin() {
      return this.currentUser?.role === 'superadmin'
    },

    filteredMasterMenu() {
      if (this.isStaff) {
        return this.masterMenu.filter(m => m.to === '/admin/barang')
      }
      return this.masterMenu
    },

    filteredLaporanMenu() {
      if (this.isStaff) {
        return this.laporanMenu.filter(m => m.to === '/admin/laporan-stok')
      }
      return this.laporanMenu
    },
  },

  watch: {
    $route(to) {
      this.autoOpen(to.path)
      this.sidebarOpen = false
      this.notifOpen = false
      this.fetchNotifCount()
    },
  },

  mounted() {
    this.autoOpen(this.$route.path)
    this.fetchNotifCount()
  },

  methods: {
    async fetchNotifCount() {
      try {
        const response = await api.get('/dashboard-stats')
        this.notifCount = response.data.stokKritisCount || 0
        this.lowStockItems = response.data.lowStock || []
      } catch (error) {
        console.error('Error fetching notification count:', error)
      }
    },

    toggle(key) {
      this.open[key] = !this.open[key]
    },

    isActive(path) {
      return this.$route.path === path || this.$route.path.startsWith(path + '/')
    },

    isActivePath(currentPath, targetPath) {
      return currentPath === targetPath || currentPath.startsWith(targetPath + '/')
    },

    autoOpen(path) {
      if (this.masterMenu.some(m => this.isActivePath(path, m.to))) {
        this.open.master = true
      }
      if (this.transaksiMenu.some(m => this.isActivePath(path, m.to))) {
        this.open.transaksi = true
      }
      if (this.filteredLaporanMenu.some(m => this.isActivePath(path, m.to))) {
        this.open.laporan = true
      }
      if (this.pengaturanMenu.some(m => this.isActivePath(path, m.to))) {
        this.open.pengaturan = true
      }
    },

    async logout() {
      const { confirmLogout } = useSwal()
      const confirmed = await confirmLogout()
      if (confirmed) {
        const { logout } = useAuth()
        logout()
      }
    },
  },
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  max-height: 0;
}

.slide-enter-to,
.slide-leave-from {
  opacity: 1;
  max-height: 300px;
}

.slide-fade-enter-active {
  transition: all 0.2s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.15s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-10px);
  opacity: 0;
}
</style>