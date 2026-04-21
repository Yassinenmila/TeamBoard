<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Centre d'alertes</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Notifications</h1>
        </div>

        <div class="flex items-center gap-4">
          <button
            @click="markAllAsRead"
            class="text-[9px] font-black text-slate-400 uppercase tracking-widest hover:text-emerald-600 transition-colors"
          >
            Tout marquer comme lu
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-4xl mx-auto space-y-8">

          <div class="flex gap-4">
            <button
              @click="filter = 'all'"
              :class="filter === 'all' ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-400 border-slate-100'"
              class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all"
            >
              Toutes
            </button>
            <button
              @click="filter = 'unread'"
              :class="filter === 'unread' ? 'bg-emerald-500 text-white shadow-emerald-500/20 shadow-lg' : 'bg-white text-slate-400 border-slate-100'"
              class="px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border transition-all flex items-center gap-2"
            >
              Non lues
              <span v-if="unreadCount > 0" class="w-4 h-4 bg-white text-emerald-600 rounded-md flex items-center justify-center text-[8px]">{{ unreadCount }}</span>
            </button>
          </div>

          <div class="space-y-4">
            <div
              v-for="notif in filteredNotifications"
              :key="notif.id"
              :class="notif.lu ? 'bg-white/50 opacity-75' : 'bg-white shadow-sm border-l-4 border-emerald-500'"
              class="group p-6 rounded-[2rem] border border-slate-100 flex items-center justify-between transition-all hover:shadow-md"
            >
              <div class="flex items-center gap-6">
                <div
                  :class="notif.lu ? 'bg-slate-100 text-slate-400' : 'bg-emerald-50 text-emerald-600'"
                  class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl transition-colors"
                >
                  {{ notif.lu ? '🔕' : '🔔' }}
                </div>
                <div>
                  <p class="text-sm font-bold text-slate-900 italic tracking-tight">{{ notif.message }}</p>
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">
                    {{ formatTimeAgo(notif.created_at) }}
                  </p>
                </div>
              </div>

              <div class="flex items-center gap-4">
                <button
                  v-if="!notif.lu"
                  @click="markAsRead(notif.id)"
                  class="p-2 hover:bg-emerald-50 rounded-lg text-emerald-600 transition-colors"
                  title="Marquer comme lu"
                >
                  <span class="text-xs font-black uppercase tracking-tighter italic">Lu</span>
                </button>
                <button
                  @click="deleteNotification(notif.id)"
                  class="p-2 hover:bg-rose-50 rounded-lg text-rose-500 opacity-0 group-hover:opacity-100 transition-all"
                >
                  🗑️
                </button>
              </div>
            </div>

            <div v-if="filteredNotifications.length === 0" class="text-center py-20 bg-white rounded-[3rem] border border-dashed border-slate-200">
              <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] italic">Aucune alerte ici</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const notifications = ref([]);
const filter = ref('all');
const loading = ref(true);

const unreadCount = computed(() => notifications.value.filter(n => !n.lu).length);

const filteredNotifications = computed(() => {
  if (filter.value === 'unread') return notifications.value.filter(n => !n.lu);
  return notifications.value;
});

const fetchNotifications = async () => {
  try {
    const res = await api.get('/notifications');
    notifications.value = res.data.notifications ?? [];
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const markAsRead = async (id) => {
  try {
    await api.post(`/notifications/${id}/mark-as-read`);
    const notif = notifications.value.find(n => n.id === id);
    if (notif) notif.lu = true;
  } catch (error) { console.error(error); }
};

const markAllAsRead = async () => {
  try {
    await api.post('/notifications/mark-all-as-read');
    notifications.value.forEach(n => n.lu = true);
  } catch (error) { console.error(error); }
};

const deleteNotification = async (id) => {
  try {
    await api.delete(`/notifications/${id}`);
    notifications.value = notifications.value.filter(n => n.id !== id);
  } catch (error) { console.error(error); }
};

const formatTimeAgo = (date) => {
  const d = new Date(date);
  return d.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
};

onMounted(fetchNotifications);
</script>
