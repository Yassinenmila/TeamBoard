<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="user">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">
            Dossier {{ user.role }}
          </p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
            {{ user.first_name }} {{ user.last_name }}
          </h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
            Retour
          </button>
          <router-link
            v-if="user"
            :to="`/admin/users/edit/${user.id}`"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10"
          >
            Modifier le profil
          </router-link>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20 italic text-slate-400 tracking-widest text-[10px] uppercase animate-pulse">
          Récupération des données de {{ user?.first_name || 'utilisateur' }}...
        </div>

        <div v-else-if="user" class="max-w-6xl mx-auto space-y-10">
          <div class="grid grid-cols-12 gap-10">

            <div class="col-span-12 lg:col-span-4 space-y-6">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-3xl bg-slate-900 text-white flex items-center justify-center text-3xl font-black italic mb-6 shadow-xl shadow-slate-900/10">
                  {{ user.first_name?.[0] }}{{ user.last_name?.[0] }}
                </div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tighter">
                  {{ user.first_name }} {{ user.last_name }}
                </h2>
                <span class="inline-block mt-3 px-4 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase rounded-lg tracking-widest">
                  {{ user.role }}
                </span>

                <div class="mt-8 pt-8 border-t border-slate-50 text-left space-y-4">
                  <div>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Email</p>
                    <p class="text-xs font-bold text-slate-900 truncate">{{ user.email }}</p>
                  </div>
                  <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                      <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Créations</p>
                      <p class="text-sm font-black text-slate-900">{{ user.taches_creees_count + user.reunions_creees_count }}</p>
                    </div>
                    <div>
                      <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Invitations</p>
                      <p class="text-sm font-black text-slate-900">{{ user.reunions_invitations_count }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-8">

              <section class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                  <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] italic">Missions affectées</h3>
                  <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-3 py-1 rounded-full uppercase">
                    {{ user.taches_assignees_count }} Active(s)
                  </span>
                </div>

                <div v-if="user.assignations?.length" class="space-y-4">
                  <div
                    v-for="item in user.assignations"
                    :key="item.id"
                    class="group p-6 rounded-2xl border border-slate-50 bg-slate-50/30 hover:bg-white hover:shadow-md transition-all border-l-4 border-l-emerald-500"
                  >
                    <div class="flex justify-between items-start">
                      <div>
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">ID #{{ item.id }} — {{ item.type }}</p>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight group-hover:text-emerald-600 transition-colors">
                          {{ item.title }}
                        </h4>
                      </div>
                      <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                        Échéance : {{ formatDate(item.date) }}
                      </span>
                    </div>
                  </div>
                </div>
                <div v-else class="py-10 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                  <p class="text-[10px] font-black text-slate-300 uppercase italic">Aucune mission assignée</p>
                </div>
              </section>

              <section v-if="user.creations?.length || user?.role !== 'membre'" class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-8 italic">Dernières créations</h3>

                <div class="space-y-4">
                  <div
                    v-for="creation in user.creations"
                    :key="creation.id"
                    class="flex items-center justify-between p-5 rounded-2xl bg-slate-50/50 border border-transparent hover:border-slate-100 transition-all"
                  >
                    <div class="flex items-center gap-4">
                      <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-[10px] font-black">
                        {{ creation.type?.[0].toUpperCase() }}
                      </div>
                      <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-tight">{{ creation.title }}</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ creation.type }}</p>
                      </div>
                    </div>
                    <p class="text-[10px] font-black text-slate-300 uppercase">{{ creation.date }}</p>
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
const user = ref(null);
const loading = ref(true);

const formatDate = (dateString) => {
  if (!dateString) return 'N/C';
  // Gestion du format "2026-04-30 00:00:00"
  const date = new Date(dateString);
  return date.toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

const fetchUser = async () => {
  try {
    const id = route.params.id;
    const res = await api.get(`/users/${id}`);
    user.value = res.data;
  } catch (error) {
    console.error("Erreur chargement utilisateur:", error);
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();
onMounted(fetchUser);
</script>
