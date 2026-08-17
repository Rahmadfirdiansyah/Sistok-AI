<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      style="background: rgba(0, 0, 0, 0.45)"
      @click.self="$emit('close')"
    >
      <transition name="scale">
        <div
          class="bg-white rounded-2xl shadow-2xl w-full overflow-hidden flex flex-col"
          :class="maxWidth"
          @click.stop
        >
          <!-- Header -->
          <div class="flex items-center justify-between px-4 md:px-6 py-4 border-b border-gray-100 shrink-0">
            <h3 class="font-bold text-gray-800 text-base m-0">{{ title }}</h3>
            <button
              @click="$emit('close')"
              class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100 border-0 bg-transparent cursor-pointer text-gray-400 hover:text-gray-600 transition-all"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="18" y1="6" x2="6" y2="18" />
                <line x1="6" y1="6" x2="18" y2="18" />
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="px-4 md:px-6 py-5 overflow-y-auto max-h-[80vh] md:max-h-[70vh] flex-grow">
            <slot></slot>
          </div>

          <!-- Footer -->
          <div v-if="$slots.footer" class="flex items-center justify-end gap-2 px-4 md:px-6 py-4 border-t border-gray-100 shrink-0">
            <slot name="footer"></slot>
          </div>
        </div>
      </transition>
    </div>
  </transition>
</template>

<script>
export default {
  name: 'Modal',
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    title: {
      type: String,
      default: '',
    },
    maxWidth: {
      type: String,
      default: 'max-w-md', // max-w-sm, max-w-md, max-w-lg, max-w-xl, max-w-2xl
    },
  },
  emits: ['close'],
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
  transition: transform 0.25s ease, opacity 0.25s ease;
}
.scale-enter-from,
.scale-leave-to {
  transform: scale(0.95);
  opacity: 0;
}
</style>
