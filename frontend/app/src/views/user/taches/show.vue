<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-6xl mx-auto px-8 py-16">

        <div class="mb-12">
          <button @click="$router.back()" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-emerald-600 transition-colors mb-6 flex items-center gap-2">
            ← Retour au flux
          </button>

          <div v-if="!fetching" class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
            <div class="space-y-4">
              <div class="flex items-center gap-3">
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">Mission Ref. #{{ tache.id }}</span>
              </div>
              <h1 class="text-6xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
                {{ tache.titre }}
              </h1>
            </div>
          </div>
        </div>

        <div v-if="fetching" class="py-20 text-center italic text-slate-300 uppercase tracking-[0.5em] text-[10px] animate-pulse">
          Accès au dossier sécurisé...
        </div>

        <div v-else class="grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-8 space-y-8">

            <div class="bg-white p-12 rounded-[3.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
              <h3 class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-8 italic flex items-center gap-3">
                <span class="w-8 h-[1px] bg-emerald-600"></span> Briefing de la mission
              </h3>
              <p class="text-xl text-slate-700 font-medium leading-relaxed italic whitespace-pre-line">
                {{ tache.description }}
              </p>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-4 space-y-6">

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-4 italic">Échéance</p>
              <div class="flex items-end gap-3">
                <span class="text-4xl font-black text-slate-900 italic tracking-tighter">{{ formatDay(tache.date_limite) }}</span>
                <span class="text-lg font-black text-emerald-600 uppercase italic mb-1">{{ formatMonth(tache.date_limite) }}</span>
              </div>
              <p class="text-[10px] font-bold text-slate-400 mt-2">Année {{ formatYear(tache.date_limite) }}</p>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <h3 class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-6 italic">Personnel assigné</h3>
              <div class="space-y-4">
                <div v-for="user in (tache.utilisateurs || [])" :key="user.id" class="flex items-center gap-4 group">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center font-black text-slate-600 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-300 shadow-inner uppercase">
                    {{ user.first_name?.charAt(0) }}{{ user.last_name?.charAt(0) }}
                  </div>
                  <div>
                    <p class="text-xs font-black text-slate-900 uppercase italic">{{ user.first_name }} {{ user.last_name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ user.role }}</p>
                  </div>
                </div>
                <div v-if="!tache.utilisateurs?.length" class="text-[10px] font-black text-slate-300 uppercase italic">Aucun membre assigné</div>
              </div>
            </div>

            <router-link v-if="canEdit" :to="`/user/taches/${tache.id}/edit`"
                         class="block w-full py-5 bg-slate-900 text-white text-center rounded-[2rem] text-[10px] font-black uppercase tracking-[0.3em] hover:bg-emerald-600 transition-all shadow-xl shadow-slate-900/10">
              Éditer la mission
            </router-link>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from '@/components/users/Sidebar.vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const auth = useAuthStore();
const tache = ref({});
const fetching = ref(true);

const fetchTache = async () => {
  try {
    fetching.value = true;
    const res = await api.get(`/taches/${route.params.id}`);
    tache.value = res.data;
  } catch (error) {
    console.error(error);
  } finally {
    fetching.value = false;
  }
};

const canEdit = computed(() => {
  const role = auth.user?.role;
  return role === 'responsable' || role === 'admin';
});

const statusBg = (status) => {
  switch (status) {
    case 'terminée': return 'bg-emerald-500 shadow-emerald-200';
    case 'en cours': return 'bg-blue-600 shadow-blue-200';
    default: return 'bg-orange-500 shadow-orange-200';
  }
};

const formatDay = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { day: '2-digit' }) : '--';
const formatMonth = (d) => d ? new Date(d).toLocaleDateString('fr-FR', { month: 'long' }) : 'N/A';
const formatYear = (d) => d ? new Date(d).getFullYear() : '';

onMounted(fetchTache);
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
