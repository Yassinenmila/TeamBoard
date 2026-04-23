<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="tache">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Détails de la mission</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">{{ tache.titre }}</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
            Retour
          </button>
          <router-link
            v-if="tache"
            :to="`/admin/taches/edit/${tache.id}`"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10"
          >
            Modifier / Évaluer
          </router-link>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20 italic text-slate-300 animate-pulse uppercase tracking-widest text-[10px]">
          Lecture du dossier mission...
        </div>

        <div v-else-if="tache" class="max-w-6xl mx-auto grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-8 space-y-10">

            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6 italic">Objectifs de la mission</label>
              <p class="text-slate-600 leading-relaxed font-bold italic">
                {{ tache.description }}
              </p>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-4 space-y-8">

            <div class="bg-slate-900 text-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-900/10">
              <div class="mb-8 border-b border-white/5 pb-6">
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-3 italic">État de progression</p>
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                  <p class="text-xl font-black uppercase italic tracking-tighter">{{ tache.status }}</p>
                </div>
              </div>
              <div>
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-2 italic">Date Limite</p>
                <p class="text-xl font-black italic tracking-tighter">{{ formatDateFull(tache.date_limite) }}</p>
              </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6 italic">Collaborateur(s) assigné(s)</p>
              <div v-if="tache.utilisateurs?.length" class="space-y-4">
                <div v-for="user in tache.utilisateurs" :key="user.id" class="flex items-center gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100 transition-all hover:bg-white hover:shadow-sm">
                  <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-xs font-black italic">
                    {{ user.first_name[0] }}{{ user.last_name[0] }}
                  </div>
                  <div>
                    <p class="text-[11px] font-black text-slate-900 uppercase italic tracking-tighter">{{ user.first_name }} {{ user.last_name }}</p>
                    <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest">{{ user.role }}</p>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-4 italic text-[10px] text-slate-300 uppercase tracking-widest border border-dashed border-slate-100 rounded-xl">
                Non assigné
              </div>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const route = useRoute();
const tache = ref(null);
const loading = ref(true);

const fetchTache = async () => {
  try {
    const response = await api.get(`/taches/${route.params.id}`);
    tache.value = response.data;
  } catch (error) {
    console.error("Erreur chargement tâche:", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short' });
};

const formatDateFull = (dateString) => {
  if (!dateString) return 'Non définie';
  return new Date(dateString).toLocaleDateString('fr-FR', { day: '2-digit', month: 'long', year: 'numeric' });
};

const goBack = () => window.history.back();

onMounted(fetchTache);
</script>
