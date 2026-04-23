<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-5xl mx-auto px-8 py-16">

        <div class="mb-12">
          <button @click="$router.back()" class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-indigo-600 transition-colors mb-6 flex items-center gap-2">
            ← Retour à l'agenda
          </button>

          <div v-if="!fetching" class="flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-2">
              <div class="flex items-center gap-3">
                <span class="px-4 py-1.5 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full italic shadow-lg shadow-indigo-200">
                  Meeting Confirmé
                </span>
                <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest italic">ID: #{{ reunion.id }}</span>
              </div>
              <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
                {{ reunion.titre }}
              </h1>
            </div>

            <a v-if="reunion.lien_video" :href="reunion.lien_video" target="_blank"
               class="group relative px-10 py-5 bg-slate-900 text-white rounded-[2rem] overflow-hidden transition-all hover:scale-105 active:scale-95 shadow-2xl shadow-slate-900/20">
              <div class="relative z-10 flex items-center gap-4">
                <span class="text-[11px] font-black uppercase tracking-[0.2em]">Rejoindre la visio</span>
                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
              </div>
              <div class="absolute inset-0 bg-indigo-600 translate-y-full group-hover:translate-y-0 transition-transform duration-500"></div>
            </a>
          </div>
        </div>

        <div v-if="fetching" class="py-20 text-center italic text-slate-300 uppercase tracking-widest text-[10px] animate-pulse">
          Chargement du briefing...
        </div>

        <div v-else class="grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-7 space-y-8">

            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
              <div class="absolute top-0 right-0 p-8 text-6xl opacity-[0.03] font-black italic select-none">INFO</div>
              <h3 class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.4em] mb-6 italic">Ordre du jour</h3>
              <p class="text-lg text-slate-600 font-medium leading-relaxed italic">
                {{ reunion.description || 'Aucun détail supplémentaire fourni pour cette session.' }}
              </p>
            </div>

            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
              <div class="flex items-center justify-between mb-8">
                <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em] italic">Équipe Convoquée</h3>
                <span class="px-3 py-1 bg-slate-50 rounded-lg text-[10px] font-black">{{ (reunion.participants || []).length }} Présents</span>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div v-for="user in reunion.participants" :key="user.id"
                     class="flex items-center gap-4 p-4 rounded-2xl bg-slate-50/50 border border-transparent hover:border-indigo-100 hover:bg-white transition-all group">
                  <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black italic text-xs group-hover:bg-indigo-600 transition-colors">
                    {{ user.name?.charAt(0) }}
                  </div>
                  <div>
                    <p class="text-xs font-black text-slate-800 uppercase italic">{{ user.name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ user.role }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-5 space-y-6">

            <div class="bg-indigo-600 p-8 rounded-[3rem] text-white shadow-xl shadow-indigo-200 relative overflow-hidden">
              <div class="relative z-10 space-y-6">
                <div>
                  <p class="text-[9px] font-black uppercase tracking-widest text-indigo-200 mb-2 italic text-center">Date du meeting</p>
                  <p class="text-4xl font-black italic text-center tracking-tighter uppercase">
                    {{ formatFullDate(reunion.date_reunion) }}
                  </p>
                </div>

                <div class="flex justify-between items-center bg-white/10 p-6 rounded-2xl backdrop-blur-md">
                  <div class="text-center">
                    <p class="text-[8px] font-black uppercase text-indigo-200 italic mb-1">Début</p>
                    <p class="text-xl font-black italic">{{ reunion.heure_debut }}</p>
                  </div>
                  <div class="h-8 w-[1px] bg-white/20"></div>
                  <div class="text-center">
                    <p class="text-[8px] font-black uppercase text-indigo-200 italic mb-1">Fin prévue</p>
                    <p class="text-xl font-black italic">{{ reunion.heure_fin }}</p>
                  </div>
                </div>
              </div>
              <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm flex items-center gap-6">
              <div class="w-14 h-14 bg-slate-100 rounded-2xl flex items-center justify-center text-xl grayscale shadow-inner">
                🎙️
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic mb-1">Organisé par</p>
                <p class="text-sm font-black text-slate-900 uppercase italic">{{ reunion.organisateur?.name || 'Responsable Unité' }}</p>
              </div>
            </div>

            <div v-if="isOrganisateur" class="pt-4 flex gap-3">
              <button @click="editReunion" class="flex-1 py-4 bg-white border border-slate-200 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                Modifier
              </button>
              <button @click="cancelReunion" class="flex-1 py-4 bg-rose-50 text-rose-600 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-600 hover:text-white transition-all">
                Annuler
              </button>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '@/components/users/Sidebar.vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const router = useRouter();
const auth = useAuthStore();

const reunion = ref({});
const fetching = ref(true);

const isOrganisateur = computed(() => {
  return auth.user?.id === reunion.value.user_id || auth.user?.role === 'admin';
});

const fetchReunionDetails = async () => {
  try {
    fetching.value = true;
    const res = await api.get(`/reunions/${route.params.id}`);
    reunion.value = res.data;
  } catch (error) {
    console.error("Erreur de récupération:", error);
    alert("Réunion introuvable.");
    router.push('/user/reunions');
  } finally {
    fetching.value = false;
  }
};

const formatFullDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('fr-FR', {
    weekday: 'long',
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};

// Actions factices à lier à tes routes
const editReunion = () => router.push(`/user/reunions/${reunion.value.id}/edit`);
const cancelReunion = async () => {
  if(confirm("Voulez-vous vraiment annuler cette réunion ?")) {
    await api.delete(`/reunions/${reunion.value.id}`);
    router.push('/user/reunions');
  }
};

onMounted(fetchReunionDetails);
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
