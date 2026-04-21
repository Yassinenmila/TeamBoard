<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Administration</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Registre des Demandes</h1>
        </div>

        <router-link
          to="/admin/demandes/create"
          class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10"
        >
          + Nouvelle Requête
        </router-link>
      </header>

      <div class="px-12 py-6 bg-white border-b border-slate-50 flex items-center justify-between">
        <div class="flex gap-4">
          <button @click="filterStatus = 'all'" :class="filterStatus === 'all' ? 'bg-slate-900 text-white' : 'bg-slate-50 text-slate-400'" class="px-6 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Toutes</button>
          <button @click="filterStatus = 'pending'" :class="filterStatus === 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-50 text-slate-400'" class="px-6 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">En attente</button>
          <button @click="filterStatus = 'accepted'" :class="filterStatus === 'accepted' ? 'bg-emerald-500 text-white' : 'bg-slate-50 text-slate-400'" class="px-6 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest transition-all">Acceptées</button>
        </div>

        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">{{ filteredDemandes.length }} résultat(s)</p>
      </div>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Récupération des dossiers...</p>
        </div>

        <div v-else class="max-w-6xl mx-auto space-y-4">

          <div
            v-for="demande in filteredDemandes"
            :key="demande.id"
            class="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 flex items-center justify-between group hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
          >
            <div class="flex items-center gap-6">
              <div
                class="w-14 h-14 rounded-2xl flex items-center justify-center text-xl shadow-inner"
                :class="statusClasses(demande.status).bg"
              >
                {{ typeIcon(demande.type) }}
              </div>

              <div>
                <div class="flex items-center gap-3 mb-1">
                  <span class="text-[11px] font-black text-slate-900 uppercase tracking-tight italic">{{ demande.type }}</span>
                  <span class="text-[9px] font-bold text-slate-300">#DEM-{{ demande.id }}</span>
                </div>
                <p class="text-[10px] font-medium text-slate-400 line-clamp-1 max-w-md italic">
                  {{ demande.description }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-8">
              <div class="text-right hidden md:block">
                <p class="text-[10px] font-black text-slate-900 uppercase tracking-tighter italic">
                  {{ demande.user?.first_name }} {{ demande.user?.last_name }}
                </p>
                <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest mt-0.5">
                  {{ formatDate(demande.created_at) }}
                </p>
              </div>

              <div
                class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-[0.2em] border shadow-sm"
                :class="statusClasses(demande.status).badge"
              >
                {{ demande.status }}
              </div>

              <router-link
                :to="`/admin/demandes/${demande.id}/edit`"
                class="w-10 h-10 rounded-xl bg-slate-50 text-slate-300 flex items-center justify-center group-hover:bg-slate-900 group-hover:text-white transition-all"
              >
                →
              </router-link>
            </div>
          </div>

          <div v-if="filteredDemandes.length === 0" class="text-center py-32 bg-white rounded-[3rem] border border-dashed border-slate-200">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Aucune demande répertoriée</p>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const demandes = ref([]);
const loading = ref(true);
const filterStatus = ref('all');

const fetchDemandes = async () => {
  try {
    const res = await api.get('/demandes', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    demandes.value = res.data;
  } catch (error) {
    console.log(error.response.data);
    console.error("Erreur:", error);
  } finally {
    loading.value = false;
  }
};

const filteredDemandes = computed(() => {
  if (filterStatus.value === 'all') return demandes.value;
  return demandes.value.filter(d => d.status === filterStatus.value);
});

const statusClasses = (status) => {
  switch (status) {
    case 'accepted':
      return { bg: 'bg-emerald-50', badge: 'bg-emerald-50 text-emerald-600 border-emerald-100' };
    case 'rejected':
      return { bg: 'bg-rose-50', badge: 'bg-rose-50 text-rose-600 border-rose-100' };
    default:
      return { bg: 'bg-amber-50', badge: 'bg-amber-50 text-amber-600 border-amber-100' };
  }
};

const typeIcon = (type) => {
  const t = type.toLowerCase();
  if (t.includes('matériel')) return '📦';
  if (t.includes('congé')) return '🏝️';
  if (t.includes('accès')) return '🔑';
  return '📁';
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
};

onMounted(fetchDemandes);
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { width: 4px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
