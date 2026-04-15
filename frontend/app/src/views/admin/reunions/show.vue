<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="reunion">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Détails de la session</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">{{ reunion.titre }}</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
            Retour
          </button>
          <button class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10">
            Lancer la réunion
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement des données...</p>
        </div>

        <div v-else-if="reunion" class="max-w-7xl mx-auto space-y-8">

          <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <span class="font-black">📅</span>
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Date</p>
                <p class="text-xs font-bold text-slate-900">{{ fullDateFormat(reunion.date) }}</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center">
                <span class="font-black">⏰</span>
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Heure</p>
                <p class="text-xs font-bold text-slate-900">{{ reunion.heure }}</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <span class="font-black">📍</span>
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Lieu</p>
                <p class="text-xs font-bold text-slate-900">{{ reunion.lieu }}</p>
              </div>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm flex items-center gap-4">
              <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <span class="font-black">👤</span>
              </div>
              <div>
                <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">Organisateur</p>
                <p class="text-xs font-bold text-slate-900">{{ reunion.organisateur?.first_name }}</p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-4 space-y-6">
              <section class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                  <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em]">Participants</h3>
                  <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">{{ reunion.invites?.length }}</span>
                </div>

                <div class="space-y-4">
                  <div v-for="invite in reunion.invites" :key="invite.id" class="flex items-center justify-between group p-2 hover:bg-slate-50 rounded-2xl transition-all">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-black group-hover:bg-slate-900 group-hover:text-white transition-all">
                        {{ invite.first_name[0] }}{{ invite.last_name[0] }}
                      </div>
                      <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight">{{ invite.first_name }} {{ invite.last_name }}</p>
                        <p class="text-[9px] font-medium text-slate-400">{{ invite.role }}</p>
                      </div>
                    </div>
                    <div class="w-1.5 h-1.5 rounded-full bg-slate-200"></div>
                  </div>
                </div>
              </section>
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-6">
              <section class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm h-full">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-8">Ordre du jour & Objectifs</h3>

                <div class="prose prose-slate max-w-none">
                  <p class="text-sm font-medium text-slate-600 leading-relaxed italic border-l-4 border-emerald-500 pl-6 py-2 bg-emerald-50/30 rounded-r-2xl">
                    {{ reunion.description || "Aucune description fournie pour cette réunion." }}
                  </p>
                </div>

                <div class="mt-12">
                  <h4 class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6">Fichiers partagés</h4>
                  <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center gap-3 border border-dashed border-slate-200">
                      <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-xs">📄</div>
                      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Briefing_Projet.pdf</p>
                    </div>
                  </div>
                </div>
              </section>
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
const reunion = ref(null);
const loading = ref(true);

const fetchReunion = async () => {
  try {
    const id = route.params.id;
    const res = await api.get(`/reunions/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    reunion.value = res.data;
  } catch (error) {
    console.error("Erreur:", error);
  } finally {
    loading.value = false;
  }
};

const fullDateFormat = (d) => {
  return new Date(d).toLocaleDateString('fr-FR', {
    weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
  });
};

const goBack = () => window.history.back();

onMounted(fetchReunion);
</script>

<style scoped>
.overflow-y-auto::-webkit-scrollbar { width: 4px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
