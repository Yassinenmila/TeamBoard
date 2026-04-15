<template>
  <aside class="w-64 bg-white border-r border-slate-100 flex flex-col p-8 shrink-0 h-screen sticky top-0">

    <div class="mb-16">
      <p class="text-2xl font-black tracking-tighter text-slate-900 uppercase italic">
        Team<span class="text-emerald-600">Board</span>
      </p>
      <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em] mt-1">Admin Panel</p>
    </div>

    <nav class="flex-1 space-y-2">
      <router-link
        v-for="link in navLinks"
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
            <span class="text-xs font-black uppercase tracking-[0.15em] transition-transform duration-200 group-hover:translate-x-1">
              {{ link.name }}
            </span>
            <div
              v-if="isActive"
              class="w-4 h-0.5 bg-emerald-500 mt-1 rounded-full"
            ></div>
          </div>
        </div>
      </router-link>
    </nav>

    <div class="mt-auto border-t border-slate-100 pt-8">
      <div class="flex items-center gap-4 px-2">
        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center font-black text-sm border border-emerald-100">
          YB
        </div>
        <div class="overflow-hidden">
          <p class="font-bold text-sm text-slate-900 truncate">Yassine B.</p>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Super Admin</p>
        </div>
      </div>

      <button
        @click="handleLogout"
        class="mt-6 w-full text-left px-2 text-[10px] font-black text-slate-300 hover:text-rose-500 uppercase tracking-[0.2em] transition-colors"
      >
        Déconnexion
      </button>
    </div>

  </aside>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

// Définition des liens avec leurs chemins réels
const navLinks = ref([
  { name: "Vue d'ensemble", path: "/dashboard" },
  { name: "Missions", path: "/taches" },
  { name: "Reunions", path: "/reunions"},
  { name: "Collaborateurs", path: "/users" }, // Ta nouvelle page Index
  { name: "Demandes" , path:"/demandes"},
  { name: "Notifications", path:"/notifications"},
  { name: "Paramètres", path: "/settings" }
]);

const handleLogout = () => {
  // Logique simple pour vider le localStorage et rediriger
  localStorage.removeItem('token');
  router.push('/login');
};
</script>

<style scoped>
/* L'effet de translation est maintenant géré directement par Tailwind (group-hover:translate-x-1) */
</style>
