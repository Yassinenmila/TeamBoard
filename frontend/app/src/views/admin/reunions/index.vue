<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Calendrier d'équipe</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Réunions & Briefings</h1>
        </div>

        <router-link to="/reunions" class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10">
          + Planifier une session
        </router-link>
      </header>

      <div class="px-12 py-6 bg-white border-b border-slate-50 flex gap-4">
        <button class="px-6 py-2 bg-slate-900 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">À venir</button>
        <button class="px-6 py-2 bg-slate-50 text-slate-400 hover:text-slate-900 rounded-lg text-[9px] font-black uppercase tracking-widest transition-colors">Archives</button>
      </div>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement de l'agenda...</p>
        </div>

        <div v-else class="max-w-6xl mx-auto grid grid-cols-1 gap-6">

          <div v-for="reunion in reunions" :key="reunion.id" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-xl transition-all p-8 flex flex-col md:flex-row md:items-center justify-between gap-8 group">

            <div class="flex items-center gap-6 shrink-0">
              <div class="w-20 h-20 rounded-3xl bg-slate-900 text-white flex flex-col items-center justify-center italic border-4 border-slate-50">
                <span class="text-xs font-black uppercase tracking-tighter">{{ formatMonth(reunion.date) }}</span>
                <span class="text-2xl font-black">{{ formatDate(reunion.date) }}</span>
              </div>
              <div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tighter group-hover:text-emerald-600 transition-colors">
                  {{ reunion.titre }}
                </h3>
                <div class="flex items-center gap-2 mt-1">
                  <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ reunion.heure }} — {{ reunion.lieu || 'Google Meet' }}</p>
                </div>
              </div>
            </div>

            <div class="flex-1">
              <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">Participants invités</p>
              <div class="flex -space-x-3 overflow-hidden">
                <div
                  v-for="invite in reunion.invitations.slice(0, 5)"
                  :key="invite.id"
                  class="inline-block h-10 w-10 rounded-xl ring-4 ring-white bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-600 uppercase border border-slate-200"
                  :title="invite.first_name + ' ' + invite.last_name"
                >
                  {{ invite.first_name ?? '' }}{{ invite.last_name ?? '' }}
                </div>
                <div v-if="reunion.invitations.length > 5" class="inline-block h-10 w-10 rounded-xl ring-4 ring-white bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-black">
                  +{{ reunion.invitations.length - 5 }}
                </div>
              </div>
            </div>

            <div class="flex items-center gap-3 shrink-0">
              <router-link
                :to="`/reunions/${reunion.id}`"
                class="px-6 py-3 bg-slate-50 text-slate-900 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-900 hover:text-white transition-all"
              >
                Rejoindre
              </router-link>
              <button @click="deleteReunion(reunion.id)" class="p-3 text-slate-300 hover:text-rose-500 transition-colors">
                <span class="text-[10px] font-black uppercase tracking-widest italic">
                  Annuler
                </span>
              </button>
            </div>

          </div>

          <div v-if="reunions.length === 0" class="text-center py-32 bg-white rounded-[3rem] border border-dashed border-slate-200">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Aucune réunion planifiée cette semaine</p>
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
    const res = await api.get('/reunions', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    console.log("RESPONSE API:", res.data);
    // On s'attend à recevoir : titre, date, heure, lieu, et le tableau 'invitations'
    reunions.value = res.data;
  } catch (error) {
    console.error("Erreur chargement réunions:", error);
  } finally {
    loading.value = false;
  }
};

const deleteReunion = async (id) => {
  if (!confirm('Voulez-vous vraiment supprimer cette réunion ?')) return;

  try {
    await api.delete(`/reunions/${id}`, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    // enlever de la liste sans reload
    reunions.value = reunions.value.filter(r => r.id !== id);

    alert('Réunion supprimée');
  } catch (error) {
    console.error(error);
    alert('Erreur lors de la suppression');
  }
};

const formatDate = (d) => new Date(d).getDate();
const formatMonth = (d) => new Date(d).toLocaleString('fr-FR', { month: 'short' }).replace('.', '');

onMounted(fetchReunions);
</script>

<style scoped>
/* Personnalisation de la scrollbar */
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
</style>
