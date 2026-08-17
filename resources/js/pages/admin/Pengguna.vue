<template>
  <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-4 sm:gap-0 bg-slate-50/50">
      <div>
        <h2 class="text-lg font-bold text-slate-800 m-0">Manajemen Pengguna</h2>
        <p class="text-sm text-slate-500 m-0 mt-1">Kelola akun dan hak akses pegawai.</p>
      </div>
      <button
        @click="openModal()"
        class="w-full sm:w-auto justify-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors border-0 cursor-pointer flex items-center gap-2"
      >
        <span>+</span> Tambah Pengguna
      </button>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse">
        <thead>
          <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider border-b border-slate-200">
            <th class="p-4 font-semibold">Nama Lengkap</th>
            <th class="p-4 font-semibold">Email</th>
            <th class="p-4 font-semibold">Role / Hak Akses</th>
            <th class="p-4 font-semibold text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <tr v-if="loading" class="animate-pulse">
            <td colspan="4" class="p-8 text-center text-slate-400">Memuat data pengguna...</td>
          </tr>
          <tr v-else-if="users.length === 0">
            <td colspan="4" class="p-8 text-center text-slate-400">Tidak ada pengguna ditemukan.</td>
          </tr>
          <tr
            v-for="user in users"
            :key="user.id"
            class="hover:bg-slate-50/50 transition-colors"
          >
            <td class="p-4 text-sm font-medium text-slate-800">
              {{ user.name }}
              <span v-if="user.id === currentUserId" class="ml-2 text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Anda</span>
            </td>
            <td class="p-4 text-sm text-slate-600">{{ user.email }}</td>
            <td class="p-4">
              <span
                class="px-2.5 py-1 text-xs font-semibold rounded-lg"
                :class="{
                  'bg-purple-100 text-purple-700': user.role === 'superadmin',
                  'bg-blue-100 text-blue-700': user.role === 'admin',
                  'bg-slate-100 text-slate-700': user.role === 'user'
                }"
              >
                {{ formatRole(user.role) }}
              </span>
            </td>
            <td class="p-4 text-right">
              <div class="flex justify-end gap-2">
                <button
                  @click="openModal(user)"
                  class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors border-0 bg-transparent cursor-pointer"
                  title="Edit"
                >
                  Edit
                </button>
                <button
                  v-if="user.id !== currentUserId"
                  @click="deleteUser(user)"
                  class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors border-0 bg-transparent cursor-pointer"
                  title="Hapus"
                >
                  Hapus
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeModal"></div>
      
      <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
          <h3 class="text-lg font-bold text-slate-800 m-0">
            {{ form.id ? 'Edit Pengguna' : 'Tambah Pengguna' }}
          </h3>
          <button @click="closeModal" class="text-slate-400 hover:text-slate-600 bg-transparent border-0 text-xl cursor-pointer">×</button>
        </div>

        <form @submit.prevent="saveUser" class="p-6">
          <div class="space-y-4">
            <!-- Nama -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
              <input
                v-model="form.name"
                type="text"
                required
                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                placeholder="Masukkan nama pengguna"
              >
            </div>

            <!-- Email -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input
                v-model="form.email"
                type="email"
                required
                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                placeholder="user@dasen.id"
              >
            </div>

            <!-- Role -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Role / Hak Akses</label>
              <select
                v-model="form.role"
                required
                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
              >
                <option value="superadmin">Super Admin (Akses Penuh + Manajemen Akun)</option>
                <option value="user">Staff Gudang (Hanya Lihat)</option>
              </select>
            </div>

            <!-- Password -->
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">
                Password <span v-if="form.id" class="text-slate-400 font-normal">(Kosongkan jika tidak ingin diubah)</span>
              </label>
              <input
                v-model="form.password"
                type="password"
                :required="!form.id"
                minlength="6"
                class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all"
                placeholder="Minimal 6 karakter"
              >
            </div>
          </div>

          <!-- Error Message -->
          <div v-if="errorMessage" class="mt-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg border border-red-100">
            {{ errorMessage }}
          </div>

          <div class="mt-6 flex justify-end gap-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors border-0 cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-xl transition-colors border-0 cursor-pointer disabled:opacity-50"
            >
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/utils/api'
import { useSwal } from '@/composables/useSwal'
import { useAuth } from '@/composables/useAuth'
import { getCache, setCache } from '@/utils/cache'

export default {
  name: 'Pengguna',
  data() {
    return {
      users: [],
      loading: true,
      showModal: false,
      saving: false,
      errorMessage: '',
      form: {
        id: null,
        name: '',
        email: '',
        password: '',
        role: 'user'
      }
    }
  },
  computed: {
    currentUserId() {
      return useAuth().user.value?.id
    }
  },
  mounted() {
    this.fetchUsers()
  },
  methods: {
    formatRole(role) {
      const roles = {
        'superadmin': 'Super Admin',
        'admin': 'Admin Inventaris',
        'user': 'Staff Gudang'
      }
      return roles[role] || role
    },
    async fetchUsers() {
      const cached = getCache('master_pengguna')
      if (cached) {
        this.users = cached
        this.loading = false
      } else {
        this.loading = true
      }
      try {
        const { data } = await api.get('/users')
        this.users = data
        setCache('master_pengguna', this.users)
      } catch (error) {
        useSwal().toastError('Gagal mengambil data pengguna')
      } finally {
        this.loading = false
      }
    },
    openModal(user = null) {
      this.errorMessage = ''
      if (user) {
        this.form = {
          id: user.id,
          name: user.name,
          email: user.email,
          password: '',
          role: user.role
        }
      } else {
        this.form = {
          id: null,
          name: '',
          email: '',
          password: '',
          role: 'user'
        }
      }
      this.showModal = true
    },
    closeModal() {
      this.showModal = false
    },
    async saveUser() {
      const swal = useSwal()
      this.saving = true
      this.errorMessage = ''
      
      const payload = { ...this.form }
      if (this.form.id && !this.form.password) {
        delete payload.password // don't send empty password if editing
      }

      try {
        if (this.form.id) {
          const isConfirm = await swal.confirmEdit('Simpan Perubahan Pengguna?')
          if (!isConfirm) {
            this.saving = false
            return
          }
          await api.put(`/users/${this.form.id}`, payload)
        } else {
          await api.post('/users', payload)
        }
        
        swal.toastSuccess('Berhasil menyimpan pengguna')
        this.closeModal()
        this.fetchUsers()
      } catch (error) {
        if (error.response?.data?.errors) {
          const firstErrorKey = Object.keys(error.response.data.errors)[0]
          this.errorMessage = error.response.data.errors[firstErrorKey][0]
        } else {
          this.errorMessage = error.response?.data?.message || 'Terjadi kesalahan'
        }
      } finally {
        this.saving = false
      }
    },
    async deleteUser(user) {
      const swal = useSwal()
      const isConfirm = await swal.confirmDelete(user.name, 'Pengguna')

      if (isConfirm) {
        try {
          await api.delete(`/users/${user.id}`)
          swal.toastSuccess('Pengguna berhasil dihapus')
          this.fetchUsers()
        } catch (error) {
          swal.toastError('Gagal menghapus pengguna')
        }
      }
    }
  }
}
</script>
