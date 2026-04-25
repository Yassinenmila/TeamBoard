import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// ================= LOGIN =================
const LoginView = () => import('@/views/LoginView.vue')

// ================= USER =================
const UserDashboard = () => import('@/views/user/dashboard.vue')

// ================= ADMIN =================
const AdminDashboard = () => import('@/views/admin/Dashboard.vue')

// TACHES
const Taches = () => import('@/views/admin/taches/index.vue')
const TacheCreate = () => import('@/views/admin/taches/Create.vue')
const TacheShow = () => import('@/views/admin/taches/Show.vue')
const TacheEdit = () => import('@/views/admin/taches/edit.vue')

// USERS
const Users = () => import('@/views/admin/users/index.vue')
const UserShow = () => import('@/views/admin/users/show.vue')
const UserCreate = () => import('@/views/admin/users/create.vue')
const UserEdit = () => import('@/views/admin/users/edit.vue')

// REUNIONS
const Reunions = () => import('@/views/admin/reunions/index.vue')
const ReunionsCreate = () => import('@/views/admin/reunions/create.vue')
const ReunionsShow = () => import('@/views/admin/reunions/show.vue')
const ReunionsEdit = () => import('@/views/admin/reunions/edit.vue')

// DEMANDES
const Demandes = () => import('@/views/admin/demandes/index.vue')
const DemandeCreate = () => import('@/views/admin/demandes/create.vue')
const DemandeEdit = () => import('@/views/admin/demandes/edit.vue')

// NOTIFICATIONS
const Notifications = () => import('@/views/admin/notifications/index.vue')

// ANNONCES
const Annances = () => import('@/views/admin/annances/index.vue')
const AnnancesCreate = () => import('@/views/admin/annances/create.vue')
const AnnancesEdit = () => import('@/views/admin/annances/edit.vue')

// ================= ROUTER =================
const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    // 🔐 LOGIN
    {
      path: '/login',
      name: 'login',
      component: LoginView
    },

    // ================= USER AREA =================
    {
      path: '/user',
      meta: { requiresAuth: true, roles: ['responsable', 'membre'] },
      redirect: '/user/dashboard',
      children: [
        {
          path: 'dashboard',
          name: 'user.dashboard',
          component: UserDashboard
        },
        {
          path: 'taches',
          name: 'user.taches',
          component: () => import('@/views/user/taches/index.vue')
        },
        {
          path: 'taches/:id',
          name: 'user.taches.show',
          component: () => import('@/views/user/taches/show.vue')
        },
        {
          path: 'reunions',
          name: 'user.reunions',
          component: () => import('@/views/user/reunions/index.vue')
        },
        {
          path: 'reunions/create',
          name: 'user.reunions.create',
          component: ()=> import ('@/views/user/reunions/create.vue')
        },
        {
          path: 'reunions/:id',
          name: 'user.reunions.show',
          component: () => import('@/views/user/reunions/show.vue')
        },
        {
          path: 'demandes',
          name: 'user.demandes',
          component: () => import('@/views/user/demandes/index.vue'),
          meta: { roles: ['membre'] }
        },
        {
          path: 'demandes/:id',
          name: 'user.demandes.show',
          component: () => import('@/views/user/demandes/show.vue'),
          meta: { roles: ['membre'] }
        },
        {
          path: 'demandes/create',
          name: 'user.demandes.create',
          component: () => import('@/views/user/demandes/create.vue'),
          meta: { roles: ['membre'] }
        },
        {
          path:'notifications',
          name: 'user.notifications',
          component: () => import('@/views/user/notifications/index.vue')
        },
        {
          path:'annances',
          name: 'user.annances',
          component: () => import('@/views/user/annances/index.vue')
        },
        {
          path:'annances/:id',
          name: 'user.annances.show',
          component: () => import('@/views/user/annances/show.vue')
        },
        {
          path:'annances/create',
          name: 'user.annances.create',
          component: () => import('@/views/user/annances/create.vue'),
          meta: { roles: ['responsable'] }
        },
        {
          path:'annances/edit/:id',
          name: 'user.annances.edit',
          component: () => import('@/views/user/annances/edit.vue'),
          meta: { roles: ['responsable'] }
        }
      ]
    },

    // ================= ADMIN AREA =================
    {
      path: '/admin',
      meta: { requiresAuth: true, role: 'admin' },
      redirect: '/admin/dashboard',
      children: [
        {
          path: 'dashboard',
          name: 'admin.dashboard',
          component: AdminDashboard
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

        // NOTIFICATIONS
        { path: 'notifications', component: Notifications },

        // ANNONCES
        { path: 'annances', component: Annances },
        { path: 'annances/create', component: AnnancesCreate },
        { path: 'annances/:id/edit', component: AnnancesEdit }
      ]
    }
  ]
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  const isLoggedIn = !!auth.token

  // 🔴 pas connecté
  if (!isLoggedIn) {
    if (to.path !== "/login") return "/login"
    return true
  }

  // 🟡 charger user une seule fois
  if (!auth.user) {
    await auth.fetchUser()
  }

  const role = auth.user?.role

  // 🔥 login → redirection intelligente
  if (to.path === "/login") {
    return role === "admin"
      ? "/admin/dashboard"
      : "/user/dashboard"
  }

  // 🔥 accès root
  if (to.path === "/") {
    return role === "admin"
      ? "/admin/dashboard"
      : "/user/dashboard"
  }

  // 🔐 protection admin
  if (to.path.startsWith("/admin") && role !== "admin") {
    return "/user/dashboard"
  }

  // 🔐 protection user area
  if (to.path.startsWith("/user") && role === "admin") {
    return "/admin/dashboard"
  }

  return true
})

export default router
