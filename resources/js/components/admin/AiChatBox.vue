<template>
  <div class="ai-chat-container">
    <!-- Floating Button -->
    <button @click="toggleChat" class="ai-fab" :class="{ 'ai-fab-active': isOpen }" title="Smart Input">
      <svg v-if="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
      </svg>
      <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Chat Window -->
    <transition name="slide-up">
      <div v-if="isOpen" class="ai-chat-window">
        <!-- Header -->
        <div class="ai-chat-header">
          <div class="flex items-center gap-3">
            <div class="ai-avatar">🤖</div>
            <div>
              <h3 class="font-bold text-sm text-white m-0">Sistok AI (Gemini Studio)</h3>
              <p class="text-xs text-indigo-200 m-0">Powered by Google AI Studio</p>
            </div>
          </div>
        </div>

        <!-- Messages Area -->
        <div class="ai-chat-messages" ref="messagesContainer">
          <!-- Welcome Message -->
          <div class="msg-bot">
            Halo! Ketik instruksi stok atau pertanyaan dalam bahasa sehari-hari. ✨<br>
            <span class="text-xs text-gray-500 mt-2 block font-semibold">Contoh:</span>
            <ul class="text-xs text-gray-500 mt-1 pl-3 m-0" style="list-style-type: disc; line-height: 1.4;">
              <li>"Tolong catat masuk 10 pcs kabel UTP"</li>
              <li>"Pak Budi ngambil 2 unit router buat kantor"</li>
              <li>"Berapa sisa stok Tang Crimp sekarang?"</li>
              <li>"Tampilkan rekap transaksi hari ini"</li>
              <li>"Barang apa yang stoknya menipis?"</li>
              <li>"Daftarkan barang baru bernama Swtich Hub TP-Link"</li>
            </ul>
          </div>

          <!-- Message History -->
          <div v-for="(msg, i) in messages" :key="i" :class="msg.isUser ? 'msg-user-wrap' : 'msg-bot-wrap'">
            <div :class="msg.isUser ? 'msg-user' : 'msg-bot'" v-html="msg.text"></div>
            
            <!-- Confirmation Buttons -->
            <div v-if="msg.pendingAction && !msg.actionResolved" class="flex gap-2 mt-2">
            <template v-if="!isStaff">
              <button @click="confirmAction(msg)" class="btn-confirm">✅ Ya, Simpan</button>
              <button @click="cancelAction(msg)" class="btn-cancel">❌ Batal</button>
            </template>
            <template v-else>
              <div style="font-size: 11px; color: #b45309; background: #fffbeb; padding: 6px 10px; border-radius: 8px; border: 1px solid #fcd34d;">
                🔒 Akun Staff hanya memiliki akses lihat. Eksekusi transaksi memerlukan izin Super Admin.
              </div>
            </template>
          </div>
          </div>
          
          <div v-if="loading" class="msg-bot-wrap">
            <div class="msg-bot flex items-center gap-1" style="min-height: 40px; min-width: 60px;">
              <div class="dot-typing"></div>
            </div>
          </div>
          <div style="height: 10px; flex-shrink: 0; width: 100%;"></div>
        </div>

        <div class="ai-chat-input">
          <input 
            ref="chatInput"
            v-model="inputText" 
            @keyup.enter="sendMessage" 
            type="text" 
            placeholder="Ketik transaksi... (Alt + C)" 
            :disabled="loading"
          />
          <button @click="sendMessage" :disabled="!inputText.trim() || loading">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
import api from '@/utils/api';
import { useAuth } from '@/composables/useAuth';

export default {
  name: 'AiChatBox',
  data() {
    return {
      isOpen: false,
      inputText: '',
      messages: [],
      loading: false
    }
  },
  computed: {
    isStaff() {
      return useAuth().user.value?.role === 'user';
    }
  },
  mounted() {
    window.addEventListener('keydown', this.handleKeydown);
  },
  beforeUnmount() {
    window.removeEventListener('keydown', this.handleKeydown);
  },
  methods: {
    handleKeydown(e) {
      if (e.altKey && e.code === 'KeyC') {
        e.preventDefault();
        if (!this.isOpen) {
          this.isOpen = true;
          this.focusInput();
        } else {
          this.focusInput();
        }
      }
      if (e.code === 'Escape' && this.isOpen) {
        this.isOpen = false;
      }
    },
    toggleChat() {
      this.isOpen = !this.isOpen;
      if (this.isOpen) {
        this.focusInput();
      }
    },
    scrollToBottom() {
      if (this.$refs.messagesContainer) {
        this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
      }
    },
    focusInput() {
      this.$nextTick(() => {
        this.scrollToBottom();
        this.$refs.chatInput?.focus();
      });
    },
    async sendMessage() {
      if (!this.inputText.trim()) return;
      
      const text = this.inputText.trim();
      this.messages.push({ text: text, isUser: true });
      this.inputText = '';
      this.loading = true;
      this.focusInput();

      try {
        const response = await api.post('/ai/parse-chat', { text });
        
        if (response.data.success) {
          const data = response.data.data;
          if (data.action === 'info') {
            this.messages.push({
              isUser: false,
              text: `📦 <b>Info Stok Barang</b><br>
                     Barang: <b>${data.nama_barang}</b><br>
                     Sisa Stok: <b style="color: #0284c7; font-size: 16px;">${data.stok} ${data.satuan}</b>`
            });
          } else if (data.action === 'new_item') {
            this.messages.push({
              isUser: false,
              text: `Pendaftaran Barang Baru:<br>
                     Nama Barang: <b style="color: #6366f1;">${data.nama_barang}</b><br>
                     Lokasi: <b>${data.lokasi_nama}</b><br>
                     Kategori: <b>${data.kategori_nama}</b><br><br>
                     <i>Apakah Anda yakin ingin mendaftarkan barang ini ke sistem?</i>`,
              pendingAction: true,
              actionResolved: false,
              payload: data
            });
          } else if (data.action === 'new_kategori') {
            this.messages.push({
              isUser: false,
              text: `📂 Pendaftaran Kategori Baru:<br>
                     Nama Kategori: <b style="color: #6366f1;">${data.nama}</b><br>
                     Deskripsi: <i>${data.deskripsi || '-'}</i><br><br>
                     <i>Apakah Anda yakin ingin menambahkan kategori ini ke sistem?</i>`,
              pendingAction: true,
              actionResolved: false,
              payload: data
            });
          } else if (data.action === 'new_lokasi') {
            this.messages.push({
              isUser: false,
              text: `📍 Pendaftaran Lokasi Baru:<br>
                     Nama Lokasi: <b style="color: #6366f1;">${data.nama}</b><br>
                     Deskripsi: <i>${data.deskripsi || '-'}</i><br><br>
                     <i>Apakah Anda yakin ingin menambahkan lokasi ini ke sistem?</i>`,
              pendingAction: true,
              actionResolved: false,
              payload: data
            });
          } else if (data.action === 'new_satuan') {
            this.messages.push({
              isUser: false,
              text: `📏 Pendaftaran Satuan Baru:<br>
                     Nama Satuan: <b style="color: #6366f1;">${data.nama}</b><br>
                     Keterangan: <i>${data.keterangan || '-'}</i><br><br>
                     <i>Apakah Anda yakin ingin menambahkan satuan ini ke sistem?</i>`,
              pendingAction: true,
              actionResolved: false,
              payload: data
            });
          } else {
            const actionText = data.action === 'masuk' ? 'Barang Masuk 📦' : 'Barang Keluar 📤';
            const colorClass = data.action === 'masuk' ? 'color: #16a34a;' : 'color: #dc2626;';
            
            this.messages.push({
              isUser: false,
              text: `Apakah maksud Anda:<br>
                     <b style="${colorClass}">${actionText}</b><br>
                     Barang: <b>${data.nama_barang}</b><br>
                     Jumlah: <b>${data.qty} ${data.satuan}</b><br>` +
                     (data.dipakai_oleh !== '-' ? `Dipakai Oleh: <b>${data.dipakai_oleh}</b><br>` : '') +
                     (data.tujuan !== '-' ? `Tujuan: <b>${data.tujuan}</b><br>` : '') +
                     `Keterangan: <i style="color: #6b7280; font-size: 12px;">${data.keterangan || '-'}</i>`,
              pendingAction: true,
              actionResolved: false,
              payload: data
            });
          }
        } else {
          this.messages.push({
            isUser: false,
            text: response.data.is_easter_egg 
              ? `<b style="color: #8b5cf6;">Rahasia AI:</b><br>${response.data.message}` 
              : (response.data.is_html ? response.data.message : `Maaf, saya tidak mengerti. ${response.data.message}`)
          });
        }
      } catch (error) {
        this.messages.push({
          isUser: false,
          text: 'Terjadi kesalahan saat memproses pesan.'
        });
      }
      
      this.loading = false;
      this.focusInput();
    },
    async confirmAction(msg) {
      msg.actionResolved = true;
      this.loading = true;
      
      try {
        const payload = {
          action: msg.payload.action,
        };
        
        if (msg.payload.action === 'new_item') {
            payload.nama_barang = msg.payload.nama_barang;
            payload.lokasi_id = msg.payload.lokasi_id;
            payload.kategori_id = msg.payload.kategori_id;
        } else if (['new_kategori', 'new_lokasi', 'new_satuan'].includes(msg.payload.action)) {
            payload.nama = msg.payload.nama;
            payload.deskripsi = msg.payload.deskripsi;
            payload.keterangan = msg.payload.keterangan;
        } else {
            payload.barang_id = msg.payload.barang_id;
            payload.qty = msg.payload.qty;
            payload.keterangan = msg.payload.keterangan;
            payload.dipakai_oleh = msg.payload.dipakai_oleh;
            payload.tujuan = msg.payload.tujuan;
        }

        const response = await api.post('/ai/confirm-transaction', payload);
        
        if (response.data.success) {
          this.messages.push({
            isUser: false,
            text: `✅ ${response.data.message}`
          });
        } else {
          this.messages.push({
            isUser: false,
            text: `❌ Gagal menyimpan: ${response.data.message}`
          });
        }
      } catch (error) {
        const errMessage = error.response?.data?.message || 'Terjadi kesalahan server.';
        this.messages.push({
          isUser: false,
          text: `❌ Gagal menyimpan: ${errMessage}`
        });
      }
      
      this.loading = false;
      this.focusInput();
    },
    cancelAction(msg) {
      msg.actionResolved = true;
      this.messages.push({
        isUser: false,
        text: 'Dibatalkan. Ada transaksi lain?'
      });
      this.focusInput();
    }
  }
}
</script>

<style scoped>
.ai-chat-container {
  position: fixed;
  bottom: 90px; /* Dinaikkan agar tidak menutupi paginasi */
  right: 0;
  z-index: 9999;
  display: flex;
  align-items: flex-end;
}

.ai-fab {
  width: 36px;
  height: 60px;
  border-radius: 12px 0 0 12px;
  background: linear-gradient(180deg, #6366f1, #8b5cf6);
  color: white;
  border: none;
  box-shadow: -3px 5px 15px rgba(99, 102, 241, 0.4);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  padding-right: 4px; 
}

.ai-fab:hover {
  width: 44px;
  background: linear-gradient(180deg, #4f46e5, #7c3aed);
  box-shadow: -5px 5px 20px rgba(99, 102, 241, 0.5);
}

.ai-fab-active {
  background: #374151;
  box-shadow: -3px 5px 12px rgba(0, 0, 0, 0.3);
}

.ai-chat-window {
  position: absolute;
  bottom: -40px;
  right: 50px; /* Muncul di sebelah kiri pita kecil */
  width: 350px;
  height: 480px;
  background: #ffffff;
  border-radius: 20px;
  box-shadow: -5px 15px 40px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border: 1px solid #e5e7eb;
}

.ai-chat-header {
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  padding: 16px 20px;
  color: white;
}

.ai-avatar {
  background: rgba(255, 255, 255, 0.2);
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 20px;
}

.ai-chat-messages {
  flex: 1;
  padding: 20px;
  overflow-y: auto;
  background: #f9fafb;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.msg-user-wrap {
  display: flex;
  justify-content: flex-end;
}

.msg-bot-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.msg-user {
  background: #6366f1;
  color: white;
  padding: 10px 14px;
  border-radius: 16px;
  border-bottom-right-radius: 4px;
  font-size: 13.5px;
  max-width: 85%;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.2);
}

.msg-bot {
  background: #ffffff;
  color: #374151;
  padding: 12px 16px;
  border-radius: 16px;
  border-bottom-left-radius: 4px;
  font-size: 13.5px;
  line-height: 1.5;
  max-width: 85%;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
  border: 1px solid #e5e7eb;
}

.btn-confirm {
  background: #10b981;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-cancel {
  background: #ef4444;
  color: white;
  border: none;
  padding: 8px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.ai-chat-input {
  padding: 16px;
  background: #ffffff;
  border-top: 1px solid #f3f4f6;
  display: flex;
  gap: 10px;
  box-sizing: border-box;
  width: 100%;
}

.ai-chat-input input {
  flex: 1;
  min-width: 0;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  padding: 10px 16px;
  border-radius: 20px;
  font-size: 14px;
  outline: none;
  box-sizing: border-box;
}

.ai-chat-input button {
  background: #6366f1;
  color: white;
  border: none;
  width: 42px;
  height: 42px;
  flex-shrink: 0;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

/* Transitions */
.slide-up-enter-active,
.slide-up-leave-active {
  transition: all 0.3s ease;
  transform-origin: bottom right;
}
.slide-up-enter-from,
.slide-up-leave-to {
  opacity: 0;
  transform: scale(0.8) translateY(20px);
}

/* Dot Typing */
.dot-typing {
  position: relative;
  left: -9999px;
  width: 6px;
  height: 6px;
  border-radius: 5px;
  background-color: #9ca3af;
  color: #9ca3af;
  box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af;
  animation: dot-typing 1.5s infinite linear;
  margin: 10px 20px;
}
@keyframes dot-typing {
  0% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
  16.667% { box-shadow: 9984px -6px 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
  33.333% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
  50% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px -6px 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
  66.667% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
  83.333% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px -6px 0 0 #9ca3af; }
  100% { box-shadow: 9984px 0 0 0 #9ca3af, 9999px 0 0 0 #9ca3af, 10014px 0 0 0 #9ca3af; }
}

/* Responsive Mobile */
@media (max-width: 600px) {
  .ai-chat-window {
    width: calc(100vw - 65px);
    height: 75vh;
    max-height: 500px;
    right: 50px;
    box-sizing: border-box;
  }
}
</style>
