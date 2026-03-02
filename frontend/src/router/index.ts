import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth';

const router = createRouter({
  
  history: createWebHistory(import.meta.env.BASE_URL),
  
  routes: [
    {
      path: '/',         
      name: 'login',      
      component: () => import('@/views/auth/LoginView.vue') ,
      meta : { 
        showNavbar: false,
        requiresAuth: false 
       }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('@/views/dashboard/DashboardView.vue'),
      meta : { 
        showNavbar: true,
        requiresAuth: true 
       }
    },
    {
      path: '/data',
      name: 'data',
      component: () => import('@/views/data/DataView.vue'),
      meta : { 
        showNavbar: true,
        requiresAuth: true 
       }
    },
    {
      path: '/calculate',
      name: 'calculate',
      component: () => import('@/views/calculate/CalculateView.vue'),
      meta : { 
        showNavbar: true,
        requiresAuth: true 
       }
    }
  ]
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();
  if(to.meta.requiresAuth) {
    if (authStore.token) {
      next();
    }else{
      next({ name: 'login' });
    }
  } else {
    if(to.name === 'login' && authStore.token){
      next({ name: 'dashboard' });
    }else{
      next();
    }
  }
});

export default router