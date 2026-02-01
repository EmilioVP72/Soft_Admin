import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  
  history: createWebHistory(import.meta.env.BASE_URL),
  
  routes: [
    {
      path: '/',         
      name: 'login',      
      component: () => import('../views/auth/LoginView.vue') ,
      meta : { showNavbar: false }
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../views/dashboard/DashboardView.vue'),
      meta : { showNavbar: true }
    }
  ]
})

export default router