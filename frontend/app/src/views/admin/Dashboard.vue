<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">

    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Vue d'ensemble</h1>
          <p class="text-sm text-slate-500 font-medium">Statistiques clés et activité de TeamBoard.</p>
        </div>

        <div class="flex items-center gap-4">
          <div class="text-right text-xs font-bold text-slate-400 uppercase tracking-widest px-4 py-2 bg-slate-50 rounded-xl">
            Aujourd'hui, {{ currentDate }}
          </div>

          <button class="flex items-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all text-sm uppercase tracking-tight active:scale-95">
            + Nouveau Projet
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 space-y-12">

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

          <div v-for="stat in stats" :key="stat.label" class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow group">
            <p class="text-5xl font-black text-slate-900 tracking-tighter group-hover:text-emerald-600 transition-colors">{{ stat.value }}</p>
            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] mt-3">{{ stat.label }}</p>
          </div>

          <div class="bg-slate-900 p-8 rounded-3xl text-white md:col-span-2 flex items-center justify-between gap-6 shadow-xl shadow-slate-900/10 transition-transform duration-300 hover:-translate-y-1">
            <div>
              <p class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-2 block">Index de Santé</p>
              <p class="text-xl font-bold leading-snug">Performance globale de <br>votre équipe</p>
            </div>
            <div class="text-center bg-white/10 p-5 rounded-2xl backdrop-blur-sm border border-white/20">
              <span class="text-6xl font-black italic tracking-tighter">9.2</span>
              <p class="text-[10px] uppercase font-bold tracking-widest opacity-80 mt-1">Excellent</p>
            </div>
          </div>
        </section>

        <section class="bg-white rounded-[2rem] border border-slate-100 p-8 shadow-sm">
          <div class="flex justify-between items-center mb-10">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1.5 block">Suivi opérationnel</p>
              <h2 class="text-xl font-bold tracking-tight text-slate-900">Derniers Projets</h2>
            </div>
            <button class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest hover:text-emerald-700 transition-colors bg-emerald-50 px-5 py-2.5 rounded-xl">Voir tout</button>
          </div>

          <div class="space-y-4">
            <div v-for="project in projects" :key="project.id" class="p-6 rounded-2xl bg-slate-50 border border-transparent hover:border-emerald-100 hover:bg-white transition-all cursor-pointer flex items-center justify-between">

              <div class="flex items-center gap-5">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center font-black text-emerald-600 text-lg">
                  {{ project.initials }}
                </div>
                <div>
                  <p class="font-bold text-slate-900 text-sm uppercase tracking-tighter italic">
                    {{ project.name }} <span class="text-emerald-600 font-medium">{{ project.version }}</span>
                  </p>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ project.category }}</p>
                </div>
              </div>

              <div class="flex items-center gap-10">
                <span :class="project.statusColor" class="px-4 py-1.5 text-[10px] font-black rounded-full uppercase tracking-widest">
                  {{ project.status }}
                </span>

                <div class="text-right text-xs font-bold text-slate-500 uppercase tracking-tight">
                  Lancé le, <span class="text-slate-900">{{ project.date }}</span>
                </div>
              </div>
            </div>
          </div>
        </section>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import Sidebar from '@/components/admin/Sidebar.vue';

// On simule des données dynamiques
const stats = ref([
  { label: 'Tâches Finies', value: '124' },
  { label: 'Temps Moyen', value: '32h' }
]);

const fetchTaches = async ()=> {

  try{
    

  }catch{

  }
}


// Date du jour dynamique
const currentDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'short', year: 'numeric'
  });
});
</script>
