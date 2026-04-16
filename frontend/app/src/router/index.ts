import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginView from '../views/LoginView.vue'
import Dashboard from '@/views/admin/Dashboard.vue'
import Taches from '@/views/admin/taches/index.vue'
import TacheCreate from '@/views/admin/taches/Create.vue'
import TacheShow from '@/views/admin/taches/Show.vue'
import TacheEdit from '@/views/admin/taches/edit.vue'
import Users from '@/views/admin/users/index.vue'
import UserShow from'@/views/admin/users/show.vue'
import UserCreate from '@/views/admin/users/create.vue'
import UserEdit from '@/views/admin/users/edit.vue'
import Reunions from '@/views/admin/reunions/index.vue'
import ReunionsCreate from '@/views/admin/reunions/create.vue'
import ReunionsShow from '@/views/admin/reunions/show.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'Login',
      component: LoginView
    },
    {
      path:'/taches',
      component:Taches
    },
    {
      path: '/taches/:id',
      name: 'taches.show',
      component: TacheShow
    },
    {
      path:'/taches/edit/:id',
      component:TacheEdit
    },
    {
      path:'/Dashboard',
      name:'Dashboard',
      component:Dashboard
    },
    {
      path:'/taches/create',
      component:TacheCreate
    },
    {
      path:'/users',
      component:Users
    },
    {
      path:'/users/create',
      component:UserCreate
    },
    {
      path:'/users/edit/:id',
      component:UserEdit
    },
    {
      path:'/users/:id',
      component:UserShow
    },
    {
      path:'/reunions',
      component:Reunions
    },
    {
      path:'/reunions/create',
      component:ReunionsCreate
    },
    {
      path:'/reunions/:id',
      component:ReunionsShow
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
