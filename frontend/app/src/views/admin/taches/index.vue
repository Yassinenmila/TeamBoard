<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Gestion opérationnelle</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Toutes les Missions</h1>
        </div>

        <div class="flex items-center gap-6">
          <router-link
            to="/admin/taches/create"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 active:scale-95"
          >
            + Nouvelle Tâche
          </router-link>
        </div>
      </header>

      <div class="px-12 py-6 bg-white border-b border-slate-50 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <button
            v-for="filter in ['tous', 'en attente', 'en cours', 'terminée']"
            :key="filter"
            @click="activeFilter = filter"
            :class="[
              activeFilter === filter
                ? 'bg-slate-900 text-white'
                : 'text-slate-400 hover:text-slate-900 bg-slate-50'
            ]"
            class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all"
          >
            {{ filter }}
          </button>
        </div>

        <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">
          {{ filteredTaches.length }} Missions trouvées
        </p>
      </div>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement du flux...</p>
        </div>

        <div v-else class="max-w-6xl mx-auto space-y-4">

          <div
            v-for="tache in filteredTaches"
            :key="tache.id"
            class="group bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:border-emerald-200 transition-all flex items-center justify-between"
          >
            <div class="flex items-center gap-6">
              <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center text-xs font-black group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                #{{ tache.id }}
              </div>
              <div>
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight italic group-hover:text-emerald-600 transition-colors">
                  {{ tache.titre }}
                </h3>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                  Échéance : <span class="text-slate-600">{{ formatDate(tache.date_limite) }}</span>
                </p>
              </div>
            </div>

            <div class="flex items-center gap-8">
              <span
                :class="getStatusStyle(tache.status)"
                class="px-4 py-1.5 text-[9px] font-black rounded-full uppercase tracking-[0.15em]"
              >
                {{ tache.status }}
              </span>

              <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <router-link
                  :to="`/admin/taches/${tache.id}`"
                  class="p-3 bg-slate-50 hover:bg-slate-900 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                >
                  Détails
                </router-link>
                <router-link
                  :to="`/admin/taches/edit/${tache.id}`"
                  class="p-3 bg-slate-50 hover:bg-emerald-600 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                >
                  Éditer
                </router-link>
              </div>
            </div>
          </div>

          <div v-if="filteredTaches.length === 0" class="text-center py-20 bg-white rounded-[2rem] border border-dashed border-slate-200">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Aucune mission dans cette catégorie</p>
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

const taches = ref([]);
const loading = ref(true);
const activeFilter = ref('tous');

const fetchTaches = async () => {
  try {
    const res = await api.get('/taches', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    taches.value = res.data;
  } catch (error) {
    console.error("Erreur:", error);
  } finally {
    loading.value = false;
  }
};

const filteredTaches = computed(() => {
  if (activeFilter.value === 'tous') return taches.value;
  return taches.value.filter(t => t.status === activeFilter.value);
});

const getStatusStyle = (status) => {
  switch (status) {
    case 'en cours': return 'bg-blue-50 text-blue-600';
    case 'terminée': return 'bg-emerald-50 text-emerald-600';
    case 'en attente': return 'bg-amber-50 text-amber-600';
    default: return 'bg-slate-50 text-slate-400';
  }
};

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'short', year: 'numeric'
  });
};

onMounted(fetchTaches);
</script>
