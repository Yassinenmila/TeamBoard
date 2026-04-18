import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Views
import LoginView from '../views/LoginView.vue'
import Dashboard from '@/views/admin/Dashboard.vue'

// Taches
import Taches from '@/views/admin/taches/index.vue'
import TacheCreate from '@/views/admin/taches/Create.vue'
import TacheShow from '@/views/admin/taches/Show.vue'
import TacheEdit from '@/views/admin/taches/edit.vue'

// Users
import Users from '@/views/admin/users/index.vue'
import UserShow from '@/views/admin/users/show.vue'
import UserCreate from '@/views/admin/users/create.vue'
import UserEdit from '@/views/admin/users/edit.vue'

// Reunions
import Reunions from '@/views/admin/reunions/index.vue'
import ReunionsCreate from '@/views/admin/reunions/create.vue'
import ReunionsShow from '@/views/admin/reunions/show.vue'
import ReunionsEdit from '@/views/admin/reunions/edit.vue'

// Demandes
import Demandes from '@/views/admin/demandes/index.vue'
import DemandeCreate from '@/views/admin/demandes/create.vue'
import DemandeEdit from '@/views/admin/demandes/edit.vue'

// Notifications & annonces
import Notifications from '@/views/admin/notifications/index.vue'
import Annances from '@/views/admin/annances/index.vue'
import AnnancesCreate from '@/views/admin/annances/create.vue'
import AnnancesEdit from '@/views/admin/annances/edit.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // 🔐 LOGIN
    {
      path: '/login',
      name: 'Login',
      component: LoginView
    },

    // 🔥 ADMIN GROUP
    {
      path: '/admin',
      meta: { requiresAuth: true, role: 'admin' },
      children: [
        {
          path: 'dashboard',
          name: 'Dashboard',
          component: Dashboard
        },

        // TACHES
        { path: 'taches', component: Taches },
        { path: 'taches/create', component: TacheCreate },
        { path: 'taches/:id', component: TacheShow },
        { path: 'taches/edit/:id', component: TacheEdit },

        // USERS
        { path: 'users', component: Users },
        { path: 'users/create', component: UserCreate },
        { path: 'users/:id', component: UserShow },
        { path: 'users/edit/:id', component: UserEdit },

        // REUNIONS
        { path: 'reunions', component: Reunions },
        { path: 'reunions/create', component: ReunionsCreate },
        { path: 'reunions/:id', component: ReunionsShow },
        { path: 'reunions/edit/:id', component: ReunionsEdit },

        // DEMANDES
        { path: 'demandes', component: Demandes },
        { path: 'demandes/create', component: DemandeCreate },
        { path: 'demandes/:id/edit', component: DemandeEdit },

        // NOTIFICATIONS & ANNANCES
        { path: 'notifications', component: Notifications },
        { path: 'annances', component: Annances },
        { path: 'annances/create', component: AnnancesCreate },
        { path: 'annances/:id/edit', component: AnnancesEdit }
      ]
    },
    {
      path: '/user',
      meta: { requiresAuth: true, role: 'user' },
      children: [
        {
          path: 'dashboard',
          component: () => import('@/views/user/Dashboard.vue')
        },
        {
          path: 'profile',
          component: () => import('@/views/user/Profile.vue')
        }
      ]
    }
  ]
})


// 🔐 MIDDLEWARE GLOBAL
router.beforeEach((to, from, next) => {
  const auth = useAuthStore()

  const isLoggedIn = !!auth.token
  const isAdmin = auth.user?.role === 'admin'

  // � ROOT REDIRECT
  if (to.path === '/') {
    if (isLoggedIn) {
      return next('/admin/dashboard')
    } else {
      return next('/login')
    }
  }

  // �🔒 route protégée
  if (to.meta.requiresAuth) {
    if (!isLoggedIn) {
      return next('/login')
    }

    // 🔥 check admin
    if (to.meta.role === 'admin' && !isAdmin) {
      return next('/login') // ou page 403
    }
  }

  // ❌ connecté → interdit login
  if (isLoggedIn && to.path === '/login') {
    return next('/admin/dashboard')
  }

  next()
})

export default router
