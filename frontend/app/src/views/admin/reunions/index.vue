<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Calendrier d'équipe</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Réunions & Briefings</h1>
        </div>

        <router-link to="/admin/reunions/create" class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-2xl hover:bg-emerald-600 transition-all shadow-xl shadow-slate-900/10">
          + Planifier une session
        </router-link>
      </header>

      <div class="px-12 py-4 bg-white/50 backdrop-blur-md border-b border-slate-50 flex gap-8">
        <button class="relative py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-900 italic group">
          À venir
          <div class="absolute -bottom-4 left-0 w-full h-0.5 bg-emerald-500"></div>
        </button>
        <button class="py-2 text-[10px] font-black uppercase tracking-[0.2em] text-slate-300 hover:text-slate-600 transition-colors italic">
          Archives
        </button>
      </div>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30 custom-scroll">
        <div v-if="loading" class="flex flex-col items-center justify-center py-32 space-y-4">
          <div class="w-12 h-12 border-4 border-slate-100 border-t-emerald-500 rounded-full animate-spin"></div>
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 italic">Synchronisation de l'agenda...</p>
        </div>

        <div v-else class="max-w-5xl mx-auto space-y-6">
          <div v-for="reunion in reunions" :key="reunion.id"
            class="bg-white rounded-[2.5rem] border border-slate-100 p-2 flex flex-col md:flex-row md:items-center justify-between transition-all duration-500 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.05)] group"
          >
            <div class="flex items-center gap-8 p-6">
              <div class="flex flex-col items-center justify-center w-20 h-24 rounded-[2rem] bg-slate-50 border border-slate-100 group-hover:bg-slate-900 group-hover:border-slate-900 transition-all duration-500">
                <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 group-hover:text-emerald-400 mb-1 italic">{{ formatMonth(reunion.date) }}</span>
                <span class="text-3xl font-black text-slate-900 group-hover:text-white tracking-tighter">{{ formatDate(reunion.date) }}</span>
              </div>

              <div class="space-y-2">
                <div class="flex items-center gap-3">
                   <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-md italic">Confirmé</span>
                   <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">{{ reunion.heure }}</span>
                </div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter italic leading-none group-hover:text-emerald-600 transition-colors">
                  {{ reunion.titre }}
                </h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.1em] flex items-center gap-2">
                   <span class="text-xs">📍</span> {{ reunion.lieu || 'Google Meet' }}
                </p>
              </div>
            </div>

            <div class="px-8 py-4 md:border-l border-slate-50">
               <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em] mb-4 italic">Équipe conviée</p>
               <div class="flex -space-x-3">
                  <div v-for="invite in reunion.invitations?.slice(0, 4) || []" :key="invite.id"
                    class="w-10 h-10 rounded-2xl ring-4 ring-white bg-slate-50 border border-slate-100 flex items-center justify-center text-[10px] font-black text-slate-600 uppercase transition-transform hover:-translate-y-1 hover:z-10 cursor-pointer"
                    :title="invite.user?.first_name"
                  >
                    {{ invite.user?.first_name?.[0] }}{{ invite.user?.last_name?.[0] }}
                  </div>
                  <div v-if="reunion.invitations.length > 4" class="w-10 h-10 rounded-2xl ring-4 ring-white bg-slate-900 text-white flex items-center justify-center text-[9px] font-black">
                    +{{ reunion.invitations.length - 4 }}
                  </div>
               </div>
            </div>

            <div class="p-6 flex items-center gap-2 bg-slate-50/50 rounded-[2rem] m-2">
                <router-link :to="`/admin/reunions/${reunion.id}`"
                  class="h-12 px-6 flex items-center justify-center bg-white border border-slate-200 text-slate-900 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all shadow-sm"
                >
                  Détails
                </router-link>

                <button @click="deleteReunion(reunion.id)"
                  class="w-12 h-12 flex items-center justify-center text-slate-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all"
                >
                  <span class="text-lg">🗑️</span>
                </button>
            </div>
          </div>

          <div v-if="reunions.length === 0" class="py-32 flex flex-col items-center justify-center bg-white rounded-[3rem] border border-dashed border-slate-200">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-2xl mb-6">📅</div>
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] italic">Aucune réunion à l'horizon</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const reunions = ref([]);
const loading = ref(true);

const fetchReunions = async () => {
  try {
    const res = await api.get('/reunions');
    reunions.value = res.data;
  } catch (error) {
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const deleteReunion = async (id) => {
  if (!confirm('Annuler cette session ?')) return;
  try {
    await api.delete(`/reunions/${id}`);
    reunions.value = reunions.value.filter(r => r.id !== id);
  } catch (error) {
    console.error(error);
  }
};

const formatDate = (d) => new Date(d).getDate();
const formatMonth = (d) => new Date(d).toLocaleString('fr-FR', { month: 'short' }).replace('.', '');

onMounted(fetchReunions);
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }

/* Animation fluide des cartes */
.transition-all {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
</style>
