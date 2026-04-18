<template>
  <aside class="w-64 bg-white border-r border-slate-100 flex flex-col p-8 shrink-0 h-screen sticky top-0 z-50">

    <div class="mb-16">
      <p class="text-2xl font-black tracking-tighter text-slate-900 uppercase italic">
        Team<span class="text-emerald-600">Board</span>
      </p>
      <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em] mt-1 italic">
        {{ roleName }}
      </p>
    </div>

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

    <div class="mt-auto border-t border-slate-100 pt-8">
      <div class="flex items-center gap-4 px-2">
        <div class="w-10 h-10 bg-slate-900 text-emerald-400 rounded-2xl flex items-center justify-center font-black text-sm italic shadow-lg shadow-slate-200">
          {{ user.name[0] }}
        </div>
        <div class="overflow-hidden">
          <p class="font-bold text-sm text-slate-900 truncate italic tracking-tighter">{{ user.name }}</p>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest italic">{{ roleName }}</p>
        </div>
      </div>

      <button @click="handleLogout" class="mt-6 w-full text-left px-2 text-[10px] font-black text-slate-300 hover:text-rose-500 uppercase tracking-[0.3em] transition-colors italic">
        Déconnexion
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// On récupère l'user (et son rôle) depuis le store ou localStorage
const user = ref(JSON.parse(localStorage.getItem('user')) || { name: 'Utilisateur', role: 'membre' });

const roleName = computed(() => {
  if (user.value.role === 'admin') return 'Super Admin';
  if (user.value.role === 'responsable') return 'Team Leader';
  return 'Collaborateur';
});

// Définition de tous les liens possibles
const allLinks = [
  { name: "Dashboard", path: "/dashboard", roles: ['admin', 'responsable', 'membre'] },
  { name: "Missions", path: "/taches", roles: ['admin', 'responsable', 'membre'] },
  { name: "Équipe", path: "/users", roles: ['admin', 'responsable'] },
  { name: "Analytiques", path: "/stats", roles: ['admin', 'responsable'] },
  { name: "Réunions", path: "/reunions", roles: ['admin', 'responsable', 'membre'] },
  { name: "Annonces", path: "/annonces", roles: ['admin'] },
  { name: "Mes Demandes", path: "/demandes", roles: ['membre'] },
];

// Filtrage des liens selon le rôle de l'utilisateur connecté
const filteredLinks = computed(() => {
  return allLinks.filter(link => link.roles.includes(user.value.role));
});

const handleLogout = () => {
  localStorage.clear();
  router.push('/login');
};
</script>
