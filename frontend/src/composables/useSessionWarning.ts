import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginServices from '@/services/LoginServices'

export function useSessionWarning() {
  const route = useRoute()
  const router = useRouter()
  const authStore = useAuthStore()
  
  const showWarningModal = ref(false)
  const isLoading = ref(false)
  let modalTimer: number | null = null

  const calculateWarningTime = () => {
    const tokenExpiration = localStorage.getItem('tokenExpiration')
    
    if (!tokenExpiration) return null
    
    const expirationTime = parseInt(tokenExpiration)
    const currentTime = Date.now()
    const timeUntilExpiration = expirationTime - currentTime
    const fiveMinutesInMs = 5 * 60 * 1000
    
    return timeUntilExpiration - fiveMinutesInMs
  }

  const startWarningTimer = () => {
    if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }

    const timeUntilWarning = calculateWarningTime()
    
    if (timeUntilWarning === null) return
    
    if (timeUntilWarning > 0) {
      modalTimer = window.setTimeout(() => {
        showWarningModal.value = true
      }, timeUntilWarning)
    } else {
      showWarningModal.value = true
    }
  }

  const refreshToken = async () => {
    isLoading.value = true
    try {
      const response = await LoginServices.refreshToken()
      authStore.setAuthData(response.data.data.token, response.data.data.expires_in)
      showWarningModal.value = false
      startWarningTimer()
    } catch (error) {
      console.error('Error al renovar token:', error)
      logout()
    } finally {
      isLoading.value = false
    }
  }

  const logout = () => {
    if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }
    showWarningModal.value = false
    authStore.logout()
    router.push({ name: 'login' })
  }

  watch(() => route.name, (newRoute) => {
    if (newRoute === 'dashboard' && authStore.token) {
      startWarningTimer()
    } else if (modalTimer) {
      clearTimeout(modalTimer)
      modalTimer = null
    }
  }, { immediate: true })

  return {
    showWarningModal,
    isLoading,
    refreshToken,
    logout
  }
}
