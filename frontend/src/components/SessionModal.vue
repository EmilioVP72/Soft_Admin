<script setup lang="ts">
// ─────────────────────────────────────────────────────────────
// RESPONSABILIDAD DE ESTE COMPONENTE:
// Solo muestra el modal de advertencia y emite dos eventos:
//   @confirm → el usuario quiere renovar el token
//   @cancel  → el usuario quiere cerrar sesión
//
// La lógica real (llamar a la API, manejar el timer, etc.)
// vive en useSessionWarning.ts, NO aquí.
// ─────────────────────────────────────────────────────────────

defineProps<{
    isLoading: boolean; // true mientras el backend procesa la renovación del token
}>();

const emit = defineEmits<{
    (e: 'confirm'): void; // "Mantener Sesión" → dispara refreshToken() en el composable
    (e: 'cancel'): void;  // "Cerrar Sesión"   → dispara logout() en el composable
}>();
</script>

<template>
    <!-- Fondo oscuro que cubre toda la pantalla -->
    <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/55">

        <!-- Tarjeta del modal -->
        <div class="w-full max-w-md rounded-xl bg-white p-8 shadow-2xl flex flex-col gap-5">

            <!-- Icono + título -->
            <div class="flex items-center gap-2">
                <span class="text-2xl">⚠️</span>
                <h3 class="text-lg font-bold text-slate-800">Tu sesión está por expirar</h3>
            </div>

            <!-- Mensaje informativo -->
            <p class="text-sm text-slate-500 leading-relaxed">
                Por seguridad, tu sesión se cerrará en menos de 5 minutos.
                ¿Deseas mantenerte conectado?
            </p>

            <!-- Acciones -->
            <div class="flex justify-end gap-3">

                <!-- Cerrar sesión: emite 'cancel' → composable llama a logout() -->
                <button
                    class="px-5 py-2 rounded-md text-sm font-semibold border-2 border-slate-300 text-slate-500 hover:bg-slate-100 transition-colors"
                    @click="emit('cancel')"
                >
                    Cerrar Sesión
                </button>

                <!-- Renovar token: emite 'confirm' → composable llama a refreshToken() -->
                <button
                    class="px-5 py-2 rounded-md text-sm font-semibold bg-[#1179a2] text-white hover:bg-[#0e6689] disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
                    :disabled="isLoading"
                    @click="emit('confirm')"
                >
                    {{ isLoading ? 'Renovando...' : 'Mantener Sesión' }}
                </button>

            </div>
        </div>
    </div>
</template>

