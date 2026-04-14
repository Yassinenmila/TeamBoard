import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginView from '../views/LoginView.vue'
import Dashboard from '@/views/admin/Dashboard.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: LoginView
    },
    {
      path:'/Dashboard',
      name:'Dashboard',
      component:Dashboard
    }
  ],
})


router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  const isLoggedIn = !!auth.token

  // ❌ PAS connecté → seulement login autorisé
  if (!isLoggedIn && to.path !== '/login') {
    next('/login')
  }

  // ❌ connecté → interdit de revenir sur login
  else if (isLoggedIn && to.path === '/login') {
    next('/dashboard')
  }

  // ✅ tout est OK
  else {
    next()
  }
})

export default router
