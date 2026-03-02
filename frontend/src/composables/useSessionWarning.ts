import { ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginServices from '@/services/LoginServices'

// ─────────────────────────────────────────────────────────
// FLUJO COMPLETO DE ADVERTENCIA DE SESIÓN
//
// 1. El usuario inicia sesión → el backend devuelve token + expires_in (en segundos)
// 2. setAuthData() guarda el token y calcula el timestamp exacto de expiración:
//       tokenExpiration = Date.now() + (expires_in * 1000)
// 3. Al navegar a cualquier ruta protegida, el watch detecta el cambio de ruta
//    y llama a startWarningTimer()
// 4. startWarningTimer() lee tokenExpiration de localStorage y calcula
//    cuántos milisegundos faltan para llegar a (expiración - 5 minutos)
// 5. setTimeout espera ese tiempo y luego muestra el modal (showWarningModal = true)
// 6. El usuario ve el modal y tiene dos opciones:
//    a) "Mantener Sesión" → llama a refreshToken() → POST /auth/refresh
//       → backend devuelve nuevo token + nueva expiración
//       → se guardan en localStorage y el timer se reinicia desde cero
//    b) "Cerrar Sesión" → llama a logout()
//       → limpia timer, limpia localStorage, redirige al login
// ─────────────────────────────────────────────────────────

export function useSessionWarning() {
    const route = useRoute()
    const router = useRouter()
    const authStore = useAuthStore()

    // Controla si el modal de advertencia es visible
    const showWarningModal = ref(false)

    // Controla el spinner del botón mientras se renueva el token
    const isLoading = ref(false)

    // Referencia al setTimeout para poder cancelarlo si el usuario navega al login
    let warningTimer: number | null = null

    // ─── PASO 4: Calcular cuánto tiempo falta para mostrar el aviso ───
    // Lee la expiración guardada y resta 5 minutos.
    // Retorna los milisegundos que faltan, o null si no hay token guardado.
    const calcularTiempoDeAviso = (): number | null => {
        const expiracion = localStorage.getItem('tokenExpiration')
        if (!expiracion) return null

        const msHastaExpiracion = parseInt(expiracion) - Date.now()
        const cincoMinutosEnMs = 5 * 60 * 1000

        return msHastaExpiracion - cincoMinutosEnMs
    }

    // ─── PASO 3 / 5: Arrancar el timer de advertencia ───
    // Si ya hay un timer corriendo lo cancela antes de crear uno nuevo
    // (necesario cuando se renueva el token y el timer se reinicia).
    const startWarningTimer = () => {
        // Cancelar timer previo si existe
        if (warningTimer) {
            clearTimeout(warningTimer)
            warningTimer = null
        }

        const tiempoDeEspera = calcularTiempoDeAviso()

        // Si no hay expiración guardada no hay nada que hacer
        if (tiempoDeEspera === null) return

        if (tiempoDeEspera > 0) {
            // Mostrar el modal cuando falten 5 minutos para expirar
            warningTimer = window.setTimeout(() => {
                showWarningModal.value = true
            }, tiempoDeEspera)
        } else {
            // El token ya está a menos de 5 minutos de expirar: mostrar modal de inmediato
            showWarningModal.value = true
        }
    }

    // ─── PASO 6a: Renovar el token ───
    // Llama a POST /auth/refresh. El backend valida el token actual
    // y devuelve uno nuevo con una nueva fecha de expiración.
    const refreshToken = async () => {
        isLoading.value = true
        try {
            const response = await LoginServices.refreshToken()
            const { token, expires_in } = response.data.data

            // Guardar el nuevo token y la nueva expiración en localStorage
            authStore.setAuthData(token, expires_in)

            // Cerrar el modal
            showWarningModal.value = false

            // Reiniciar el timer con la nueva expiración
            startWarningTimer()
        } catch {
            // Si la renovación falla (token ya expirado), forzar logout
            logout()
        } finally {
            isLoading.value = false
        }
    }

    // ─── PASO 6b: Cerrar sesión ───
    const logout = () => {
        // Cancelar el timer pendiente para no mostrar el modal después de salir
        if (warningTimer) {
            clearTimeout(warningTimer)
            warningTimer = null
        }
        showWarningModal.value = false
        authStore.logout()            // limpia token y localStorage
        router.push({ name: 'login' })
    }

    // ─── PASO 3: Detectar cambio de ruta y arrancar/detener el timer ───
    // immediate: true → también se ejecuta al montar App.vue por primera vez
    watch(
        () => route.name,
        (nombreRuta) => {
            const estaLogueado = !!authStore.token

            if (estaLogueado && nombreRuta !== 'login') {
                // El usuario está autenticado y en una ruta protegida → arrancar timer
                startWarningTimer()
            } else {
                // El usuario llegó al login o no tiene token → cancelar timer
                if (warningTimer) {
                    clearTimeout(warningTimer)
                    warningTimer = null
                }
            }
        },
        { immediate: true }
    )

    return {
        showWarningModal,
        isLoading,
        refreshToken,
        logout,
    }
}
