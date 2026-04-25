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
          <router-link
            to="/admin/taches/create"
            class="flex items-center gap-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-6 rounded-2xl shadow-lg shadow-emerald-500/20 transition-all text-sm uppercase tracking-tight active:scale-95"
          >
            + Nouvelle Tâche
          </router-link>
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
          <div class="flex justify-between items-end mb-10">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-1.5 block">Suivi opérationnel</p>
              <h2 class="text-xl font-bold tracking-tight text-slate-900">Dernières Tâches</h2>
            </div>

            <div class="flex items-center gap-3">
              <router-link to="/admin/taches" class="text-[11px] font-bold text-emerald-600 uppercase tracking-widest hover:bg-emerald-100 transition-colors bg-emerald-50 px-5 py-2.5 rounded-xl">
                Voir tout
              </router-link>
              <router-link
                to="/admin/taches/create"
                class="text-[11px] font-black text-white uppercase tracking-widest bg-slate-900 hover:bg-emerald-600 transition-all px-6 py-2.5 rounded-xl shadow-lg shadow-slate-900/10 active:scale-95"
              >
                + Ajouter une tâche
              </router-link>
            </div>
          </div>

          <div class="space-y-4">
            <router-link
              v-for="project in projects"
              :key="project.id"
              :to="`/admin/taches/${project.id}`"
              class="p-6 rounded-2xl bg-slate-50 border border-transparent hover:border-emerald-100 hover:bg-white transition-all flex items-center justify-between group"
            >
              <div class="flex items-center gap-5">
                <div class="w-12 h-12 bg-white rounded-xl shadow-sm border border-slate-100 flex items-center justify-center font-black text-emerald-600 text-lg group-hover:border-emerald-200 transition-colors">
                  {{ project.initials }}
                </div>
                <div>
                  <p class="font-bold text-slate-900 text-sm uppercase tracking-tighter italic">
                    {{ project.name }}
                  </p>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ project.category }}</p>
                </div>
              </div>

              <div class="flex items-center gap-10">
                <span :class="project.statusColor" class="px-4 py-1.5 text-[10px] font-black rounded-full uppercase tracking-widest">
                  {{ project.status }}
                </span>

                <div class="text-right text-xs font-bold text-slate-500 uppercase tracking-tight">
                  Limite : <span class="text-slate-900">{{ project.date }}</span>
                </div>
              </div>
            </router-link>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/services/api';
import Sidebar from '@/components/admin/Sidebar.vue';

const projects = ref([]);

const stats = ref([
  { label: 'Tâches Finies', value: '124' },
  { label: 'Temps Moyen', value: '32h' }
]);

// Adapté à ton contrôleur Laravel
const getStatusColor = (status) => {
  switch (status) {
    case 'en cours':
      return 'bg-blue-100 text-blue-700';
    case 'terminée':
      return 'bg-emerald-100 text-emerald-700';
    case 'en attente':
      return 'bg-amber-100 text-amber-700';
    default:
      return 'bg-slate-100 text-slate-600';
  }
};

const fetchTaches = async () => {
  try {
    const res = await api.get('/taches', {
      headers: {
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    projects.value = res.data.map((tache) => ({
      id: tache.id,
      name: tache.titre,
      initials: tache.titre.substring(0, 2).toUpperCase(),
      category: 'Mission Technique',
      date: new Date(tache.date_limite).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short'
      }),
      status: tache.status,
      statusColor: getStatusColor(tache.status)
    }));

  } catch (error) {
    console.error("Erreur API:", error);
  }
};

onMounted(fetchTaches);

const currentDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'short', year: 'numeric'
  });
});
</script>
