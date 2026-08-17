<!-- resources/js/pages/LoginPage.vue -->
<template>
  <div class="login-wrapper">

    <!-- Top Right Actions -->
    <div class="top-right-actions">
      <!-- Tombol WhatsApp CS -->
      <a href="https://wa.me/6288235896651" target="_blank" class="wa-cs-btn" title="Hubungi CS (WhatsApp)">
        <svg class="w-4 h-4 icon-wa" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
        </svg>
        <span>Bantuan CS</span>
      </a>

      <!-- Tombol Close Server -->
      <button @click="shutdownServer" class="power-off-btn" title="Matikan Server">
        <svg class="w-4 h-4 icon-power" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
          <path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path>
          <line x1="12" y1="2" x2="12" y2="12"></line>
        </svg>
        <span>Close Server</span>
      </button>
    </div>

    <!-- Animated Background -->
    <div class="bg-pattern">
      <div class="floating-shape shape-1"></div>
      <div class="floating-shape shape-2"></div>
      <div class="floating-shape shape-3"></div>
      <div class="floating-shape shape-4"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">

      <!-- Left Side - Branding -->
      <div class="brand-side">
        <div class="brand-content">
          <div class="brand-logo">
            <img :src="'/img/logo.webp'" alt="DASEN" class="brand-logo-img" />
          </div>
          <h2 class="brand-title">SiStok Dasen</h2>
          <p class="brand-tagline">Sistem Manajemen Stok Dasen</p>
          <div class="brand-divider"></div>
          <p class="brand-desc">Kelola stok barang data center Anda dengan mudah, cepat, dan terpercaya.</p>
          <div class="brand-features">
            <div class="feature-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              <span>Monitoring stok real-time</span>
            </div>
            <div class="feature-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              <span>Laporan PDF otomatis</span>
            </div>
            <div class="feature-item">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              <span>Notifikasi stok menipis</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Side - Form -->
      <div class="form-side">
        <div class="form-content">

          <div class="form-header">
            <h1 class="form-title">Selamat Datang 👋</h1>
            <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
          </div>

          <!-- Alert Error -->
          <transition name="shake">
            <div v-if="errorMsg" class="error-alert">
              <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12.01" y2="16" />
              </svg>
              <span>{{ errorMsg }}</span>
            </div>
          </transition>

          <!-- Email -->
          <div class="input-group">
            <label class="input-label">Email</label>
            <div class="input-wrapper" :class="{ 'input-focus': emailFocus }">
              <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                <polyline points="22,6 12,13 2,6" />
              </svg>
              <input v-model="form.email" type="email" placeholder="admin@dasen.id" @keyup.enter="handleLogin"
                @focus="emailFocus = true" @blur="emailFocus = false" />
            </div>
          </div>

          <!-- Password -->
          <div class="input-group">
            <label class="input-label">Password</label>
            <div class="input-wrapper" :class="{ 'input-focus': passFocus }">
              <svg class="input-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <input v-model="form.password" :type="showPassword ? 'text' : 'password'" placeholder="••••••••"
                @keyup.enter="handleLogin" @focus="passFocus = true" @blur="passFocus = false" />
              <button type="button" class="toggle-pass" @click="showPassword = !showPassword">
                <svg v-if="!showPassword" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                  viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path
                    d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Login Button -->
          <button @click="handleLogin" :disabled="loading" class="login-btn">
            <svg v-if="loading" class="spinner" viewBox="0 0 24 24" fill="none">
              <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                stroke-dasharray="31.4 31.4" />
            </svg>
            <span>{{ loading ? 'Memproses...' : 'Masuk' }}</span>
            <svg v-if="!loading" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
              viewBox="0 0 24 24">
              <line x1="5" y1="12" x2="19" y2="12" />
              <polyline points="12 5 19 12 12 19" />
            </svg>
          </button>

          <!-- Guest Staff Button (Lihat Stok) -->
          <button type="button" @click="handleGuestStaffLogin" :disabled="loading" class="view-stock-btn">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            <span>Lihat Stok</span>
          </button>

          <p class="form-footer">
            &copy; {{ new Date().getFullYear() }} DASEN Data Center — Every Connection Start Here
          </p>

        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { useAuth } from '@/composables/useAuth'
import axios from 'axios'

export default {
  name: 'LoginPage',
  data() {
    return {
      form: { email: '', password: '' },
      loading: false,
      errorMsg: '',
      showPassword: false,
      emailFocus: false,
      passFocus: false,
    }
  },
  methods: {
    async handleLogin() {
      this.errorMsg = ''

      if (!this.form.email || !this.form.password) {
        this.errorMsg = 'Email dan password wajib diisi!'
        return
      }

      this.loading = true
      const { login } = useAuth()
      const result = await login(this.form.email, this.form.password)

      if (!result.success) {
        this.errorMsg = result.message
      }

      this.loading = false
    },
    async handleGuestStaffLogin() {
      this.errorMsg = ''
      this.loading = true
      try {
        const res = await axios.post('/api/auth/guest-staff')
        if (res.data.token) {
          localStorage.setItem('token', res.data.token)
          useAuth().token.value = res.data.token
          useAuth().user.value = res.data.user
          this.$router.push({ name: 'admin.dashboard' })
        }
      } catch (err) {
        this.errorMsg = err.response?.data?.message || 'Gagal masuk sebagai Staff'
      }
      this.loading = false
    },

    shutdownServer() {
      if (confirm('Apakah Anda yakin ingin mematikan server aplikasi Sistok? Jendela akan otomatis ditutup setelahnya.')) {
        // Tembak perintah mati ke server tanpa menunggunya membalas (fire and forget)
        axios.post('/shutdown').catch(() => {});
        
        // Beri waktu 200 milidetik agar sinyal terkirim, lalu tutup paksa jendela aplikasinya
        setTimeout(() => {
          window.close();
          // Fallback jika browser memblokir penutupan window otomatis
          document.body.innerHTML = '<div style="display:flex;height:100vh;align-items:center;justify-content:center;background:#0f1023;color:white;font-family:sans-serif;"><h1>Server telah dimatikan. Silakan tutup jendela ini (Tanda X).</h1></div>';
        }, 200);
      }
    },
  },
}
</script>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #0f1023;
  padding: 20px;
  position: relative;
  overflow: hidden;
}

/* Top Right Actions */
.top-right-actions {
  position: absolute;
  top: 24px;
  right: 32px;
  z-index: 50;
  display: flex;
  gap: 12px;
}

.top-right-actions a,
.top-right-actions button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 12px;
  font-family: inherit;
  font-weight: 600;
  font-size: 13px;
  cursor: pointer;
  transition: all 0.3s ease;
  backdrop-filter: blur(8px);
  text-decoration: none;
}

/* WhatsApp CS Button */
.wa-cs-btn {
  background: rgba(34, 197, 94, 0.1);
  border: 1px solid rgba(34, 197, 94, 0.2);
  color: #22c55e;
}

.wa-cs-btn:hover {
  background: rgba(34, 197, 94, 0.15);
  border-color: rgba(34, 197, 94, 0.4);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(34, 197, 94, 0.2);
}

.wa-cs-btn .icon-wa {
  width: 17px;
  height: 17px;
}

/* Power Off Button */
.power-off-btn {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #ef4444;
}

.power-off-btn:hover {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.4);
  transform: translateY(-2px);
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
}

.power-off-btn .icon-power {
  width: 16px;
  height: 16px;
}

/* Animated Background */
.bg-pattern {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.floating-shape {
  position: absolute;
  border-radius: 50%;
  opacity: 0.08;
  animation: float 20s infinite ease-in-out;
}

.shape-1 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  top: -100px;
  right: -100px;
  animation-delay: 0s;
}

.shape-2 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, #c5a34d, #d4af37);
  bottom: -80px;
  left: -80px;
  animation-delay: -5s;
}

.shape-3 {
  width: 200px;
  height: 200px;
  background: linear-gradient(135deg, #06b6d4, #3b82f6);
  top: 50%;
  left: 30%;
  animation-delay: -10s;
}

.shape-4 {
  width: 150px;
  height: 150px;
  background: linear-gradient(135deg, #8b5cf6, #ec4899);
  bottom: 20%;
  right: 20%;
  animation-delay: -15s;
}

@keyframes float {

  0%,
  100% {
    transform: translate(0, 0) scale(1);
  }

  25% {
    transform: translate(30px, -30px) scale(1.1);
  }

  50% {
    transform: translate(-20px, 20px) scale(0.95);
  }

  75% {
    transform: translate(15px, 15px) scale(1.05);
  }
}

/* Login Card */
.login-card {
  display: flex;
  width: 100%;
  max-width: 900px;
  background: #ffffff;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
  position: relative;
  z-index: 1;
  animation: cardEntry 0.6s ease-out;
}

@keyframes cardEntry {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.97);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

/* Brand Side */
.brand-side {
  width: 45%;
  background: linear-gradient(160deg, #13142b 0%, #1e1f3f 50%, #2a1f4e 100%);
  padding: 48px 36px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  position: relative;
  overflow: hidden;
}

.brand-side::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -50%;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.brand-side::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -30%;
  width: 80%;
  height: 80%;
  background: radial-gradient(circle, rgba(197, 163, 77, 0.1) 0%, transparent 70%);
  pointer-events: none;
}

.brand-content {
  position: relative;
  z-index: 1;
}

.brand-logo {
  width: 72px;
  height: 72px;
  border-radius: 18px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 24px;
  padding: 8px;
  backdrop-filter: blur(10px);
}

.brand-logo-img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.brand-title {
  font-size: 28px;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 4px 0;
  letter-spacing: 3px;
}

.brand-tagline {
  font-size: 13px;
  color: #c5a34d;
  margin: 0;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.brand-divider {
  width: 40px;
  height: 3px;
  background: linear-gradient(90deg, #c5a34d, transparent);
  margin: 20px 0;
  border-radius: 2px;
}

.brand-desc {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.5);
  line-height: 1.7;
  margin: 0 0 24px 0;
}

.brand-features {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 10px;
  color: rgba(255, 255, 255, 0.6);
  font-size: 12.5px;
}

.feature-item svg {
  color: #c5a34d;
  flex-shrink: 0;
}

/* Form Side */
.form-side {
  flex: 1;
  padding: 48px 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #ffffff;
}

.form-content {
  width: 100%;
  max-width: 340px;
}

.form-header {
  margin-bottom: 28px;
}

.form-title {
  font-size: 24px;
  font-weight: 800;
  color: #1a1a2e;
  margin: 0 0 6px 0;
}

.form-subtitle {
  font-size: 13.5px;
  color: #9ca3af;
  margin: 0;
}

/* Error Alert */
.error-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 14px;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 12px;
  color: #dc2626;
  font-size: 13px;
  margin-bottom: 20px;
  animation: shakeAnim 0.4s ease;
}

@keyframes shakeAnim {

  0%,
  100% {
    transform: translateX(0);
  }

  20% {
    transform: translateX(-8px);
  }

  40% {
    transform: translateX(8px);
  }

  60% {
    transform: translateX(-4px);
  }

  80% {
    transform: translateX(4px);
  }
}

/* Input */
.input-group {
  margin-bottom: 18px;
}

.input-label {
  display: block;
  font-size: 12.5px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.input-wrapper {
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  padding: 0 14px;
  transition: all 0.25s ease;
  background: #f9fafb;
}

.input-wrapper.input-focus {
  border-color: #6366f1;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.input-icon {
  width: 18px;
  height: 18px;
  color: #9ca3af;
  flex-shrink: 0;
}

.input-wrapper input {
  flex: 1;
  border: none;
  outline: none;
  background: transparent;
  padding: 12px 0;
  font-size: 14px;
  color: #1f2937;
  font-family: inherit;
}

.input-wrapper input::placeholder {
  color: #d1d5db;
}

.toggle-pass {
  border: none;
  background: transparent;
  cursor: pointer;
  color: #9ca3af;
  padding: 4px;
  display: flex;
  align-items: center;
  transition: color 0.2s;
}

.toggle-pass:hover {
  color: #6366f1;
}

/* Login Button */
.login-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 20px;
  margin-top: 6px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: inherit;
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.35);
}

.login-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 6px 25px rgba(99, 102, 241, 0.45);
}

.login-btn:active:not(:disabled) {
  transform: translateY(0);
}

.login-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Guest Staff Button (Lihat Stok - Grey) */
.view-stock-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 20px;
  margin-top: 12px;
  background: #6b7280;
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-family: inherit;
  box-shadow: 0 4px 15px rgba(107, 114, 128, 0.25);
}

.view-stock-btn:hover:not(:disabled) {
  background: #4b5563;
  transform: translateY(-1px);
  box-shadow: 0 6px 20px rgba(107, 114, 128, 0.35);
}

.view-stock-btn:active:not(:disabled) {
  transform: translateY(0);
}

.view-stock-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}


.spinner {
  width: 18px;
  height: 18px;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }

  to {
    transform: rotate(360deg);
  }
}

/* Footer */
.form-footer {
  text-align: center;
  font-size: 11px;
  color: #d1d5db;
  margin: 28px 0 0 0;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .login-wrapper {
    padding: 16px;
    align-items: flex-start;
    padding-top: 40px;
  }

  .login-card {
    flex-direction: column;
    border-radius: 20px;
  }

  .brand-side {
    width: 100%;
    padding: 28px 24px;
  }

  .brand-desc,
  .brand-features,
  .brand-divider {
    display: none;
  }

  .brand-logo {
    width: 44px;
    height: 44px;
    margin-bottom: 16px;
  }

  .brand-logo svg {
    width: 24px;
    height: 24px;
  }

  .brand-title {
    font-size: 22px;
  }

  .brand-tagline {
    font-size: 12px;
  }

  .form-side {
    padding: 28px 24px;
  }

  .form-title {
    font-size: 20px;
  }
}
</style>