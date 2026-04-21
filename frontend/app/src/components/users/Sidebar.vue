<template>
  <aside class="w-64 bg-white border-r border-slate-100 flex flex-col p-8 shrink-0 h-screen sticky top-0 z-50">

    <!-- LOGO -->
    <div class="mb-16">
      <p class="text-2xl font-black tracking-tighter text-slate-900 uppercase italic">
        Team<span class="text-emerald-600">Board</span>
      </p>
      <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em] mt-1 italic">
        Espace {{ roleLabel }}
      </p>
    </div>

    <!-- NAV -->
    <nav class="flex-1 space-y-2">
      <router-link
        v-for="link in filteredLinks"
        :key="link.name"
        :to="link.path"
        custom
        v-slot="{ isActive, navigate }"
      >
        <div
          @click="navigate"
          :class="[
            isActive
              ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/10'
              : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50'
          ]"
          class="flex items-center px-6 py-4 rounded-2xl transition-all duration-300 group cursor-pointer"
        >
          <div class="flex flex-col">
            <span class="text-xs font-black uppercase tracking-[0.15em] italic transition-transform duration-200 group-hover:translate-x-1">
              {{ link.name }}
            </span>
            <div v-if="isActive" class="w-4 h-0.5 bg-emerald-500 mt-1 rounded-full"></div>
          </div>
        </div>
      </router-link>
    </nav>

    <!-- USER -->
    <div class="mt-auto border-t border-slate-100 pt-8">
      <div class="flex items-center gap-4 px-2">
        <div class="w-10 h-10 bg-slate-50 text-slate-900 rounded-2xl flex items-center justify-center font-black text-sm italic border border-slate-100 shadow-sm">
          {{ user?.name ? user.name[0] : '?' }}
        </div>

        <div class="overflow-hidden">
          <p class="font-bold text-sm text-slate-900 truncate italic tracking-tighter">
            {{ user?.name || 'Utilisateur' }}
          </p>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest italic">
            {{ roleLabel }}
          </p>
        </div>
      </div>

      <!-- LOGOUT -->
      <button
        @click="handleLogout"
        class="mt-6 w-full text-left px-2 text-[10px] font-black text-slate-300 hover:text-rose-500 uppercase tracking-[0.3em] transition-colors italic"
      >
        Quitter l'espace
      </button>
    </div>

  </aside>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// 🔥 STORE
const auth = useAuthStore()
const router = useRouter()

// 🔥 USER DYNAMIQUE
const user = computed(() => auth.user || null)

// 🔥 ROLE LABEL
const roleLabel = computed(() => {
  if (!user.value?.role) return ''
  return user.value.role === 'responsable' ? 'Responsable' : 'Membre'
})

// 🔗 LINKS
const allLinks = [
  { name: "Dashboard", path: "/user/dashboard", roles: ['responsable', 'membre'] },
  { name: "Mes Missions", path: "/user/taches", roles: ['responsable', 'membre'] },
  { name: "Réunions", path: "/user/reunions", roles: ['responsable', 'membre'] },
  { name: "Demandes", path: "/user/demandes", roles: ['membre'] },
]

// 🔥 FILTER PAR ROLE
const filteredLinks = computed(() => {
  if (!user.value?.role) return []
  return allLinks.filter(link => link.roles.includes(user.value.role))
})

// 🚪 LOGOUT
const handleLogout = () => {
  auth.logout()
  router.push('/login')
}
</script>
