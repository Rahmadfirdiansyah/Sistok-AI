// resources/js/app.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import api from '@/utils/api'

/**
 * =========================================================================
 * VUE SPA FRONTEND ENTRYPOINT
 * =========================================================================
 * Menginisialisasi aplikasi Vue 3, memasang Vue Router, dan meregistrasikan
 * api helper (Axios Custom Client) sebagai $axios global helper.
 */

const app = createApp(App)
app.use(router)

// Mendaftarkan API Helper secara global agar dapat diakses menggunakan this.$axios di Vue Options API
app.config.globalProperties.$axios = api

app.mount('#app')