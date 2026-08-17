// resources/js/utils/api.js
import axios from 'axios'
import router from '../router'
import { clearCache } from './cache'

/**
 * =========================================================================
 * HELPER API CLIENT (AXIOS INSTANCE)
 * =========================================================================
 * File ini berfungsi sebagai klien HTTP terpusat untuk berkomunikasi dengan
 * Laravel Backend API. Mengatur Base URL '/api', menginjeksi token secara
 * otomatis pada setiap request, dan menangani error autentikasi global.
 */

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
})

// Request Interceptor: Otomatis mencari dan menyisipkan token Bearer terbaru sebelum request dikirim
api.interceptors.request.use(config => {
  // Pastikan URL selalu diawali dengan /api jika belum ada
  if (config.url && !config.url.startsWith('http')) {
    const cleanUrl = config.url.replace(/^\/+/, '')
    if (!cleanUrl.startsWith('api/')) {
      config.url = `/api/${cleanUrl}`
    } else {
      config.url = `/${cleanUrl}`
    }
    // Kosongkan baseURL untuk mencegah double-prefixing oleh Axios
    config.baseURL = ''
  }

  const token = localStorage.getItem('token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, error => {
  return Promise.reject(error)
})

// Response Interceptor: Menangani respon global (cth: otomatis logout & redirect ke login jika token expired / 401)
api.interceptors.response.use(response => {
  // Clear frontend cache on any data mutation to ensure fresh data on next navigation
  if (['post', 'put', 'patch', 'delete'].includes(response.config.method?.toLowerCase())) {
    clearCache()
  }
  return response
}, error => {
  if (error.response && error.response.status === 401) {
    // Bersihkan sesi di penyimpanan lokal
    localStorage.removeItem('token')
    
    // Redirect ke halaman login jika token sudah kadaluarsa dan user tidak di halaman login
    if (router.currentRoute.value.name !== 'login') {
      router.push({ name: 'login' })
    }
  }
  return Promise.reject(error)
})

export default api
