// resources/js/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

// Public pages
import LoginPage from '@/pages/LoginPage.vue'

// Admin layout
import AdminLayout from '@/pages/admin/AdminLayout.vue'

// Admin — Dashboard
import AdminDashboard from '@/pages/admin/DashboardPage.vue'

// Admin — Master Data
import DataBarang from '@/pages/admin/master/DataBarang.vue'
import Kategori   from '@/pages/admin/master/Kategori.vue'
import Lokasi     from '@/pages/admin/master/Lokasi.vue'
import Satuan     from '@/pages/admin/master/Satuan.vue'

// Admin — Transaksi
import BarangMasuk  from '@/pages/admin/transaksi/BarangMasuk.vue'
import BarangKeluar from '@/pages/admin/transaksi/BarangKeluar.vue'

// Admin — Laporan
import LaporanStok       from '@/pages/admin/laporan/LaporanStok.vue'
import LaporanMasuk      from '@/pages/admin/laporan/LaporanMasuk.vue'
import LaporanKeluar     from '@/pages/admin/laporan/LaporanKeluar.vue'
import RiwayatTransaksi  from '@/pages/admin/laporan/RiwayatTransaksi.vue'

// Admin — Pengaturan
import Pengguna from '@/pages/admin/Pengguna.vue'

const routes = [
    // ── Public ──────────────────────────────────
    { path: '/',      redirect: '/login' },
    { path: '/login', name: 'login', component: LoginPage },

    // ── Admin ────────────────────────────────────
    {
        path: '/admin',
        component: AdminLayout,
        meta: { requiresAuth: true },
        children: [
            { path: '',              redirect: { name: 'admin.dashboard' } },
            { path: 'dashboard',     name: 'admin.dashboard',     component: AdminDashboard },

            // Master Data
            { path: 'barang',        name: 'admin.barang',        component: DataBarang },
            { path: 'kategori',      name: 'admin.kategori',      component: Kategori },
            { path: 'lokasi',        name: 'admin.lokasi',        component: Lokasi },
            { path: 'satuan',        name: 'admin.satuan',        component: Satuan },

            // Transaksi
            { path: 'barang-masuk',  name: 'admin.barang-masuk',  component: BarangMasuk },
            { path: 'barang-keluar', name: 'admin.barang-keluar', component: BarangKeluar },

            // Laporan
            { path: 'laporan-stok',      name: 'admin.laporan-stok',      component: LaporanStok },
            { path: 'laporan-masuk',     name: 'admin.laporan-masuk',     component: LaporanMasuk },
            { path: 'laporan-keluar',    name: 'admin.laporan-keluar',    component: LaporanKeluar },
            { path: 'laporan-transaksi', name: 'admin.laporan-transaksi', component: RiwayatTransaksi },

            // Pengaturan
            { path: 'pengguna',       name: 'admin.pengguna',       component: Pengguna },
        ],
    },

    // ── 404 ─────────────────────────────────────
    { path: '/:pathMatch(.*)*', redirect: '/login' },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior() {
        return { top: 0 }
    },
})

router.beforeEach(async (to, from, next) => {
    const { me, isChecked, user } = useAuth()

    // Jika pengguna mengunjungi halaman login namun sudah memiliki token valid, langsung arahkan ke Dashboard
    if (to.name === 'login') {
        const valid = isChecked.value ? !!user.value : await me()
        if (valid) {
            return next({ name: 'admin.dashboard' })
        }
        return next()
    }

    if (!to.meta.requiresAuth) return next()

    const checkRoleAndRedirect = (currentUser) => {
        if (currentUser) {
            // Jika bukan superadmin, blokir akses ke menu pengguna
            if (currentUser.role !== 'superadmin' && to.path.startsWith('/admin/pengguna')) {
                return { redirect: '/admin/dashboard' }
            }

            // Jika user biasa, blokir akses ke master data & laporan
            if (currentUser.role === 'user') {
                const blockedPaths = [
                    '/admin/kategori',
                    '/admin/lokasi',
                    '/admin/satuan',
                    '/admin/laporan-masuk',
                    '/admin/laporan-keluar',
                    '/admin/laporan-transaksi'
                ]
                if (blockedPaths.some(path => to.path.startsWith(path))) {
                    return { redirect: '/admin/dashboard' }
                }
            }
        }
        return null
    }

    if (isChecked.value && user.value) {
        const redirectCheck = checkRoleAndRedirect(user.value)
        if (redirectCheck) {
            return next(redirectCheck.redirect)
        }
        return next()
    }

    const valid = await me()
    if (!valid) return next({ name: 'login' })

    const redirectCheck = checkRoleAndRedirect(user.value)
    if (redirectCheck) {
        return next(redirectCheck.redirect)
    }
    next()
})

export default router