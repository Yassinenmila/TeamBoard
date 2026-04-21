<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Communication</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Annonces & Flash</h1>
        </div>

        <router-link
          to="/admin/annances/create"
          class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10"
        >
          Nouvelle Annonce +
        </router-link>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20 italic text-slate-300 animate-pulse uppercase tracking-widest text-[10px]">
          Récupération du flux...
        </div>

        <div v-else class="max-w-7xl mx-auto grid grid-cols-12 gap-6">

          <div
            v-for="annonce in annonces"
            :key="annonce.id"
            :class="[
              annonce.type === 'urgent' ? 'col-span-12 lg:col-span-6 border-emerald-500 shadow-emerald-500/5' : 'col-span-12 lg:col-span-4 border-slate-100 shadow-slate-900/5',
              'bg-white p-8 rounded-[2.5rem] border shadow-sm transition-all hover:shadow-xl group relative overflow-hidden'
            ]"
          >
            <div class="flex justify-between items-start mb-6">
              <span
                :class="annonce.type === 'urgent' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400'"
                class="text-[8px] font-black uppercase tracking-[0.2em] px-3 py-1 rounded-full italic"
              >
                {{ annonce.type }}
              </span>
              <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">
                {{ formatDate(annonce.created_at) }}
              </span>
            </div>

            <h2 class="text-xl font-black text-slate-900 tracking-tighter uppercase italic mb-4 group-hover:text-emerald-600 transition-colors">
              {{ annonce.titre }}
            </h2>
            <p class="text-sm text-slate-500 font-bold italic leading-relaxed mb-8 line-clamp-3">
              {{ annonce.contenu }}
            </p>

            <div class="flex items-center justify-between pt-6 border-t border-slate-50">
              <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-[10px] font-black text-slate-400 border border-slate-100">
                  {{ annonce.user?.name ? annonce.user.name[0] : 'A' }}
                </div>
                <span class="text-[10px] font-black text-slate-900 uppercase italic">{{ annonce.user?.name || 'Admin' }}</span>
              </div>

              <div class="flex gap-2">
                <RouterLink :to="`/admin/annances/${annonce.id}/edit`" class="p-2 hover:bg-slate-50 rounded-lg text-slate-300 hover:text-slate-900 transition-all text-xs">✏️</RouterLink>
                <button @click="deleteAnnonce(annonce.id)" class="p-2 hover:bg-rose-50 rounded-lg text-slate-300 hover:text-rose-500 transition-all text-xs">🗑️</button>
              </div>
            </div>
          </div>

          <div v-if="annonces.length === 0" class="col-span-12 text-center py-20">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] italic">Aucune annonce publiée pour le moment.</p>
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
import router from '@/router';
import { RouterLink } from 'vue-router';

const annonces = ref([]);
const loading = ref(true);

const fetchAnnonces = async () => {
  try {
    const response = await api.get('/annonces');
    annonces.value = response.data;
  } catch (error) {
    console.error("Erreur annonces:", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'short', year: 'numeric'
  });
};

const deleteAnnonce = async (id) => {
  if(confirm('Supprimer cette annonce ?')) {
    try {
      await api.delete(`/annonces/${id}`);
      annonces.value = annonces.value.filter(a => a.id !== id);
    } catch (e) { alert('Erreur suppression'); }
  }
};

// Fonctions placeholder pour tes futures modals
const openCreateModal = () => { console.log('Ouvrir création'); };
const editAnnonce = (annonce) => { console.log('Editer', annonce); };

onMounted(fetchAnnonces);
</script>

<style scoped>
/* Effet Line-clamp pour limiter le texte à 3 lignes */
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
