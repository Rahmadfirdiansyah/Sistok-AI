/**
 * Simple In-Memory Cache Manager
 * Digunakan untuk teknik SWR (Stale-While-Revalidate)
 * Menyimpan respon API di dalam memori agar saat user kembali ke halaman,
 * data langsung muncul instan tanpa loading.
 */
import { reactive } from 'vue'

const cacheStorage = reactive(new Map())

export function getCache(key) {
  const item = cacheStorage.get(key)
  if (!item) return null
  return item.data
}

export function setCache(key, data) {
  cacheStorage.set(key, { data, timestamp: Date.now() })
}

export function clearCache(key) {
  if (key) {
    cacheStorage.delete(key)
  } else {
    cacheStorage.clear()
  }
}
