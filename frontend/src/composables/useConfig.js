import { ref, computed } from 'vue'
import api from '@/configs/api'

// Global state để cache config
const configData = ref(null)
const isLoading = ref(false)
const lastFetch = ref(null)

// Cache timeout (5 phút)
const CACHE_DURATION = 5 * 60 * 1000

export function useConfig() {
  // Kiểm tra xem cache có còn valid không
  const isCacheValid = computed(() => {
    if (!configData.value || !lastFetch.value) return false
    return Date.now() - lastFetch.value < CACHE_DURATION
  })

  // Fetch config từ API
  const fetchConfig = async (force = false) => {
    if (!force && isCacheValid.value) {
      return configData.value
    }

    if (isLoading.value) {
      // Nếu đang loading, đợi cho đến khi xong
      return new Promise((resolve) => {
        const interval = setInterval(() => {
          if (!isLoading.value && configData.value) {
            clearInterval(interval)
            resolve(configData.value)
          }
        }, 100)
      })
    }

    try {
      isLoading.value = true
      const res = await api.get('/config')
      configData.value = res.data.config
      lastFetch.value = Date.now()
      return configData.value
    } catch (error) {
      console.error('Error fetching config:', error)
      throw error
    } finally {
      isLoading.value = false
    }
  }

  // Getter cho các config cụ thể
  const config = computed(() => configData.value)
  
  const mainColor = computed(() => configData.value?.main_color)
  const logoHeader = computed(() => configData.value?.logo_header)
  const logoFooter = computed(() => configData.value?.logo_footer)
  const phone = computed(() => configData.value?.phone)
  const email = computed(() => configData.value?.email)
  const twitter = computed(() => configData.value?.twitter)
  const facebook = computed(() => configData.value?.facebook)
  const tiktok = computed(() => configData.value?.tiktok)
  const youtube = computed(() => configData.value?.youtube)
  const instagram = computed(() => configData.value?.instagram)

  // Khởi tạo config nếu chưa có
  const initConfig = async () => {
    if (!configData.value) {
      await fetchConfig()
    }
    return configData.value
  }

  // Refresh config (force fetch)
  const refreshConfig = () => fetchConfig(true)

  return {
    config,
    isLoading,
    isCacheValid,
    
    // Specific config values
    mainColor,
    logoHeader,
    logoFooter,
    phone,
    email,
    twitter,
    facebook,
    tiktok,
    youtube,
    instagram,
    
    // Methods
    fetchConfig,
    initConfig,
    refreshConfig
  }
}
