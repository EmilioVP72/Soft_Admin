<script setup lang="ts">
import {reactive, ref} from 'vue';
import {useRouter} from 'vue-router';
import LoginServices from '@/services/LoginServices';
import { useAuthStore } from '../../stores/auth';
import { useGlobalLoader } from '@/composables/useGlobalLoader';
import axios from 'axios';
import {z} from 'zod';

const router = useRouter();
const authStore = useAuthStore();
const { startLoading, stopLoading } = useGlobalLoader();

const formData = reactive({
  email: '',
  password: ''
});
const isLoading = ref(false);

const loginSchema = z.object({
  email: z.string()
    .min(1, { message: "El correo electrónico es obligatorio." })
    .email({ message: "El correo electrónico no es válido." }),
  password: z.string()
    .min(6, { message: "La contraseña debe tener al menos 6 caracteres." })
    .min(1, { message: "La contraseña es obligatoria." })
})

type LoginData = z.infer<typeof loginSchema>;


const formErrors = ref<Partial<Record<keyof LoginData, string>>>({});

const validate = (): boolean => {
  formErrors.value = {};

  const result = loginSchema.safeParse(formData);

  if(result.success){
    return true;
  } else {
     const errors = result.error?.issues || [];
     errors.forEach((error) => {
      const field = error.path[0] as keyof LoginData;
      if (field) {
        formErrors.value[field] = error.message;
      }
     });
  }
  return false;

  
}

const submitForm = async () => {
  isLoading.value = true;
  formErrors.value = {};
  if(!validate()){
    isLoading.value = false;
    return;
  }
  
  startLoading();
  
  try {
    
    const response = await LoginServices.loginUser({email: formData.email, password: formData.password});
    authStore.setAuthData(response.data.data.token, response.data.data.expires_in);
    router.push({ name: 'dashboard' });

  } catch (error) {

    if(axios.isAxiosError(error)){
      const status = error.response?.status;
      if (status === 401) {
        formErrors.value.password = error.response?.data?.message || "Credenciales inválidas.";
        formErrors.value.email = error.response?.data?.message || "Credenciales inválidas.";
      } else if(status === 422){
        formErrors.value = error.response?.data || {};
      }
    }

  } finally{
    isLoading.value = false;
    stopLoading();
  }
}

</script>

<template>
  <div class="login-wrapper">
    <div class="login-container">
      <h1>Bienvenido(a)</h1>
      <h2>Iniciar Sesión</h2>

      <form @submit.prevent="submitForm" class="login-form">

        <div class="form-group"> 

          <label for="email">Correo Electrónico</label>
          <input
            id="email"
            type="email"
            v-model = "formData.email"
            :class="{'input-error': formErrors.email}"
            :disabled="isLoading"
            placeholder="Ingresa tu correo electrónico"
          />
          <span v-if="formErrors.email" class="error-message">{{ formErrors.email }}</span>
        </div>

        <div class="form-group"> 

          <label for="password">Contraseña</label>
          <input
            id="password"
            type="password"
            v-model = "formData.password"
            :class="{'input-error': formErrors.password}"
            :disabled="isLoading"
            placeholder="Ingresa tu contraseña"
          />
          <span v-if="formErrors.password" class="error-message">{{ formErrors.password }}</span>
        </div>

        <button
          type="submit"
          :disabled="isLoading"
          class="submit-button">
          <span class="loader">{{ isLoading ? "Cargando..." : "Iniciar Sesión" }}</span>
          
        </button>

      </form>    
    </div>  
  </div>
</template>

<style src="@/assets/styles/auth/login/login.css" ></style>
