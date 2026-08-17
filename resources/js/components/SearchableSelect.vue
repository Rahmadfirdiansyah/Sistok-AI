<template>
  <div class="relative" ref="container">
    <!-- Input Field -->
    <div class="relative">
      <input
        type="text"
        v-model="search"
        @focus="openDropdown"
        @keydown.down.prevent="highlightNext"
        @keydown.up.prevent="highlightPrev"
        @keydown.enter.prevent="selectHighlighted"
        @keydown.esc="closeDropdown"
        :placeholder="placeholder"
        :class="inputClass"
        class="w-full truncate pr-8"
        :disabled="disabled"
      />
      <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none text-gray-400" @click="openDropdown">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
      </div>
    </div>

    <!-- Dropdown Menu -->
    <div
      v-if="isOpen"
      class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-auto"
    >
      <ul class="py-1 m-0 list-none p-0">
        <li
          v-if="filteredOptions.length === 0"
          class="px-4 py-2 text-sm text-gray-500 text-center"
        >
          Tidak ada hasil.
        </li>
        <li
          v-for="(option, index) in filteredOptions"
          :key="option[valueKey]"
          @mousedown.prevent="selectOption(option)"
          @mouseenter="highlightedIndex = index"
          class="px-4 py-3 md:py-2 text-sm cursor-pointer transition-colors"
          :class="[
            highlightedIndex === index ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50',
            modelValue === option[valueKey] ? 'font-semibold text-indigo-600' : ''
          ]"
        >
          {{ option[labelKey] }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SearchableSelect',
  props: {
    modelValue: {
      type: [String, Number],
      default: ''
    },
    options: {
      type: Array,
      required: true
    },
    labelKey: {
      type: String,
      default: 'label'
    },
    valueKey: {
      type: String,
      default: 'value'
    },
    placeholder: {
      type: String,
      default: 'Pilih...'
    },
    inputClass: {
      type: String,
      default: 'px-3 py-2.5 rounded-xl border border-gray-200 text-sm text-gray-700 outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100 transition-all bg-white cursor-text'
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  emits: ['update:modelValue', 'change'],
  data() {
    return {
      search: '',
      isOpen: false,
      highlightedIndex: -1,
      selectedLabel: ''
    }
  },
  computed: {
    filteredOptions() {
      const query = this.search.toLowerCase()
      if (!query || query === this.selectedLabel.toLowerCase()) {
        return this.options
      }
      return this.options.filter(opt =>
        String(opt[this.labelKey]).toLowerCase().includes(query)
      )
    }
  },
  watch: {
    modelValue() {
      this.syncSelection()
    },
    options() {
      this.syncSelection()
    },
    isOpen(val) {
      if (val) {
        this.search = '' // clear search on open so user can see all and type to filter
        this.$nextTick(() => {
          this.highlightedIndex = this.filteredOptions.findIndex(o => o[this.valueKey] === this.modelValue)
          if (this.highlightedIndex === -1) this.highlightedIndex = 0
        })
      } else {
        this.syncSelection()
      }
    }
  },
  mounted() {
    this.syncSelection()
    document.addEventListener('mousedown', this.handleClickOutside)
  },
  beforeUnmount() {
    document.removeEventListener('mousedown', this.handleClickOutside)
  },
  methods: {
    syncSelection() {
      const selected = this.options.find(o => o[this.valueKey] === this.modelValue)
      if (selected) {
        this.selectedLabel = selected[this.labelKey]
        this.search = this.selectedLabel
      } else {
        this.selectedLabel = ''
        this.search = ''
      }
    },
    openDropdown() {
      if (!this.disabled) {
        this.isOpen = true
      }
    },
    closeDropdown() {
      this.isOpen = false
    },
    selectOption(option) {
      this.$emit('update:modelValue', option[this.valueKey])
      this.$emit('change', option)
      this.search = option[this.labelKey]
      this.selectedLabel = option[this.labelKey]
      this.isOpen = false
    },
    highlightNext() {
      if (!this.isOpen) {
        this.openDropdown()
        return
      }
      if (this.highlightedIndex < this.filteredOptions.length - 1) {
        this.highlightedIndex++
      }
    },
    highlightPrev() {
      if (this.highlightedIndex > 0) {
        this.highlightedIndex--
      }
    },
    selectHighlighted() {
      if (this.isOpen && this.highlightedIndex >= 0 && this.highlightedIndex < this.filteredOptions.length) {
        this.selectOption(this.filteredOptions[this.highlightedIndex])
      }
    },
    handleClickOutside(event) {
      if (this.$refs.container && !this.$refs.container.contains(event.target)) {
        this.closeDropdown()
      }
    }
  }
}
</script>
