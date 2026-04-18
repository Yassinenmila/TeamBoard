<template>
  <aside class="w-64 bg-white border-r border-slate-100 flex flex-col p-8 shrink-0 h-screen sticky top-0 z-50">

    <div class="mb-16">
      <p class="text-2xl font-black tracking-tighter text-slate-900 uppercase italic">
        Team<span class="text-emerald-600">Board</span>
      </p>
      <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.4em] mt-1 italic">Admin Panel</p>
    </div>

    <nav class="flex-1 space-y-2 relative">
      <template v-for="link in navLinks" :key="link.name">

        <div v-if="link.isNotif" class="relative group">
          <div
            @click="toggleNotif"
            :class="[
              isNotifOpen ? 'bg-slate-50 text-slate-900' : 'text-slate-400 hover:text-slate-900 hover:bg-slate-50'
            ]"
            class="flex items-center justify-between px-6 py-4 rounded-2xl transition-all duration-300 cursor-pointer group"
          >
            <span class="text-xs font-black uppercase tracking-[0.15em] italic">
              {{ link.name }}
            </span>
            <span v-if="unreadCount > 0" class="w-2 h-2 bg-rose-500 rounded-full animate-pulse shadow-sm shadow-rose-200"></span>
          </div>

          <div
            v-if="isNotifOpen"
            class="absolute left-full ml-4 top-0 w-80 bg-white rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-slate-100 overflow-hidden animate-in fade-in slide-in-from-left-4 duration-300"
          >
            <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
              <h3 class="text-[9px] font-black text-slate-900 uppercase tracking-widest italic tracking-tighter">Alertes ({{ unreadCount }})</h3>
              <button @click="markAllAsRead" class="text-[8px] font-black text-emerald-600 uppercase hover:underline">Tout lire</button>
            </div>

            <div class="max-h-[350px] overflow-y-auto custom-scroll">
              <div
                v-for="n in notifications"
                :key="n.id"
                @click="goToNotifications"
                class="p-5 border-b border-slate-50 last:border-none hover:bg-slate-50 transition-all cursor-pointer"
                :class="{'opacity-40': n.lu}">
                <p class="text-[10px] font-bold text-slate-900 leading-tight italic mb-2">{{ n.message }}</p>
                <div class="flex justify-between items-center">
                  <span class="text-[8px] font-black text-slate-300 uppercase italic">{{ formatTime(n.created_at) }}</span>
                  <button v-if="!n.lu" @click.stop="markAsRead(n.id)" class="text-[8px] font-black text-emerald-500 uppercase px-2 py-1 bg-emerald-50 rounded-md">Lu</button>
                </div>
              </div>
            </div>
            <router-link to="/admin/notifications" class="block p-4 bg-slate-900 text-center text-[8px] font-black text-emerald-400 uppercase tracking-widest italic">Voir l'historique</router-link>
          </div>
        </div>

        <router-link
          v-else
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
              <div v-if="isActive" class="w-4 h-0.5 bg-emerald-500 mt-1 rounded-full animate-in zoom-in duration-300"></div>
            </div>
          </div>
        </router-link>
      </template>
    </nav>

    <div class="mt-auto border-t border-slate-100 pt-8">
      <div class="flex items-center gap-4 px-2">
        <div class="w-10 h-10 bg-slate-900 text-emerald-400 rounded-2xl flex items-center justify-center font-black text-sm shadow-lg shadow-slate-200 italic">
          Y
        </div>
        <div class="overflow-hidden">
          <p class="font-bold text-sm text-slate-900 truncate italic tracking-tighter">Yassine B.</p>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] italic">Super Admin</p>
        </div>
      </div>

      <button
        @click="handleLogout"
        class="mt-6 w-full text-left px-2 text-[10px] font-black text-slate-300 hover:text-rose-500 uppercase tracking-[0.3em] transition-colors italic"
      >
        Déconnexion
      </button>
    </div>

  </aside>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';

const router = useRouter();
const auth = useAuthStore();
const isNotifOpen = ref(false);
const notifications = ref([]);

const navLinks = ref([
  { name: "Vue d'ensemble", path: "/admin/dashboard" },
  { name: "Missions", path: "/admin/taches" },
  { name: "Reunions", path: "/admin/reunions"},
  { name: "Collaborateurs", path: "/admin/users" },
  { name: "Demandes" , path:"/admin/demandes"},
  { name: "Annances", path:"/admin/annances" },
  { name: "Notifications", path:"/admin/notifications", isNotif: true },
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.lu).length);

const toggleNotif = () => isNotifOpen.value = !isNotifOpen.value;

const fetchNotifications = async () => {
  try {
    const res = await api.get('/notifications');
    notifications.value = res.data.notifications ?? [];
  } catch (e) {
    console.error('Erreur notifications sidebar:', e);
  }
};

const markAsRead = async (id) => {
  await api.post(`/notifications/${id}/mark-as-read`);
  fetchNotifications();
};

const markAllAsRead = async () => {
  await api.post('/notifications/mark-all-as-read');
  fetchNotifications();
};

const formatTime = (d) => new Date(d).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

const goToNotifications = () => {
  isNotifOpen.value = false;
  router.push('/admin/notifications');
};

const handleLogout = () => {
  auth.logout();
  router.push('/login');
};

onMounted(() => {
  fetchNotifications();
  setInterval(fetchNotifications, 60000);
});
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 3px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
