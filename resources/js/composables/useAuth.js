// resources/js/composables/useAuth.js
import { ref } from 'vue'
import api from '@/utils/api'
import router from '@/router'

/**
 * =========================================================================
 * COMPOSABLE AUTHENTICATION HELPER (useAuth)
 * =========================================================================
 * Mengelola state autentikasi pengguna secara global, seperti login,
 * logout, verifikasi token, dan menyimpan state user secara reaktif.
 */

const user = ref(null)
const token = ref(localStorage.getItem('token') || null)
const isChecked = ref(false) // Status pengecekan sesi ke server

export function useAuth() {

    // Proses autentikasi masuk (Login)
    const login = async (email, password) => {
        try {
            const res = await api.post('/auth/login', { email, password })

            token.value = res.data.token
            user.value  = res.data.user
            localStorage.setItem('token', res.data.token)

            router.push({ name: 'admin.dashboard' })
            return { success: true }
        } catch (err) {
            return {
                success: false,
                message: err.response?.data?.message || 'Login gagal'
            }
        }
    }

    // Proses keluar dari sistem (Logout)
    const logout = () => {
        // Coba hapus token di server secara aman
        api.post('/auth/logout').catch(() => {})

        token.value  = null
        user.value   = null
        isChecked.value = false
        localStorage.removeItem('token')

        router.push({ name: 'login' })
    }

    // Validasi & sinkronisasi token dengan database server (Me)
    const me = async () => {
        const savedToken = localStorage.getItem('token')

        // Jika tidak ada token lokal, tolak akses dengan cepat
        if (!savedToken) {
            isChecked.value = true
            return false
        }

        try {
            const res = await api.get('/auth/me')
            user.value  = res.data
            token.value = savedToken
            isChecked.value = true
            return true
        } catch (e) {
            // Sesi kedaluwarsa atau token tidak valid, hapus data lokal
            token.value  = null
            user.value   = null
            isChecked.value = true
            localStorage.removeItem('token')
            return false
        }
    }

    return { user, token, isChecked, login, logout, me }
}