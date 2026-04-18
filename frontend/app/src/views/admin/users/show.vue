<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="user">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">
            Dossier {{ isAdminOrManager ? 'Gestionnaire' : 'Collaborateur' }}
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
          Synchronisation des données...
        </div>

        <div v-else-if="user" class="max-w-6xl mx-auto space-y-10">
          <div class="grid grid-cols-12 gap-10">

            <div class="col-span-12 lg:col-span-4 space-y-6">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm text-center">
                <div class="w-24 h-24 mx-auto rounded-3xl bg-slate-900 text-white flex items-center justify-center text-3xl font-black italic mb-6 shadow-xl shadow-slate-900/10">
                  {{ user.first_name[0] }}{{ user.last_name[0] }}
                </div>
                <h2 class="text-xl font-black text-slate-900 uppercase tracking-tighter">
                  {{ user.first_name }} {{ user.last_name }}
                </h2>
                <span class="inline-block mt-3 px-4 py-1.5 bg-slate-100 text-slate-600 text-[9px] font-black uppercase rounded-lg tracking-widest">
                  {{ user.role }}
                </span>

                <div class="mt-8 pt-8 border-t border-slate-50 text-left space-y-4">
                  <div>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1 italic">Email Pro</p>
                    <p class="text-xs font-bold text-slate-900">{{ user.email }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aujourd'hui</p>
                  <span
                    :class="todayPresence?.etat === 'present' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'"
                    class="px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-tighter border border-transparent"
                  >
                    {{ todayPresence?.etat || 'Non Pointé' }}
                  </span>
                </div>

                <div class="space-y-4">
                  <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl">
                    <span class="text-[9px] font-black text-slate-400 uppercase italic">Entrée</span>
                    <span class="text-xs font-black text-slate-900 italic">{{ formatTime(todayPresence?.heure_entree) }}</span>
                  </div>
                  <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl">
                    <span class="text-[9px] font-black text-slate-400 uppercase italic">Sortie</span>
                    <span class="text-xs font-black text-slate-900 italic">{{ formatTime(todayPresence?.heure_sortie) }}</span>
                  </div>
                </div>
              </div>

              <div class="bg-emerald-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-emerald-600/10">
                <div class="flex justify-between items-center">
                  <p class="text-[10px] font-black uppercase tracking-[0.2em]">Disponibilité</p>
                  <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                </div>
                <p class="text-xl font-black italic tracking-tighter mt-4 uppercase italic">Opérationnel</p>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-8">

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                  <p class="text-3xl font-black text-slate-900 tracking-tighter">{{ user.taches_assignees_count || 0 }}</p>
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Missions</p>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                  <p class="text-3xl font-black text-emerald-600 tracking-tighter">{{ user.presences_count || 0 }}</p>
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Jours Présents</p>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                  <p class="text-3xl font-black text-rose-500 tracking-tighter">{{ countRetards }}</p>
                  <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Retards</p>
                </div>
              </div>

              <section class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-8 italic">Journal d'assiduité récent</h3>

                <div class="space-y-3">
                  <div v-for="p in user.presences?.slice(0, 5)" :key="p.id"
                    class="p-5 bg-slate-50 rounded-2xl flex justify-between items-center border border-transparent hover:border-slate-100 hover:bg-white transition-all duration-300">
                    <div class="flex items-center gap-6">
                      <div class="text-center min-w-[50px]">
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight">{{ formatDateDay(p.date) }}</p>
                        <p class="text-[8px] font-bold text-slate-400 uppercase">{{ formatDateMonth(p.date) }}</p>
                      </div>
                      <div class="h-8 w-[1px] bg-slate-200"></div>
                      <div class="flex gap-4">
                        <div>
                          <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">In</p>
                          <p class="text-[10px] font-black text-slate-900 italic">{{ formatTime(p.heure_entree) }}</p>
                        </div>
                        <div>
                          <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Out</p>
                          <p class="text-[10px] font-black text-slate-900 italic">{{ p.heure_sortie ? formatTime(p.heure_sortie) : '--:--' }}</p>
                        </div>
                      </div>
                    </div>
                    <span :class="p.etat === 'present' ? 'text-emerald-500' : 'text-amber-500'" class="text-[9px] font-black uppercase tracking-widest italic">
                      {{ p.etat }}
                    </span>
                  </div>

                  <div v-if="!user.presences?.length" class="text-center py-10 italic text-[10px] text-slate-300 uppercase tracking-widest border border-dashed border-slate-100 rounded-2xl">
                    Aucun pointage enregistré
                  </div>
                </div>
              </section>

              <section class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-8 italic">
                  {{ isAdminOrManager ? 'Dernières créations' : 'Planning & Missions' }}
                </h3>
                </section>

            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const route = useRoute();
const user = ref(null);
const loading = ref(true);

const isAdminOrManager = computed(() => {
  if (!user.value) return false;
  const role = user.value.role.toLowerCase();
  return role === 'admin' || role === 'responsable';
});

// Calcul des retards pour les stats
const countRetards = computed(() => {
  return user.value?.presences?.filter(p => p.etat === 'retard').length || 0;
});

// Présence d'aujourd'hui
const todayPresence = computed(() => {
  if (!user.value?.presences) return null;
  const today = new Date().toISOString().split('T')[0];
  return user.value.presences.find(p => p.date.startsWith(today));
});

const formatTime = (time) => {
  if (!time) return '--:--';
  return new Date(time).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
};

const formatDateDay = (date) => new Date(date).toLocaleDateString('fr-FR', { day: '2-digit' });
const formatDateMonth = (date) => new Date(date).toLocaleDateString('fr-FR', { month: 'short' });

const fetchUser = async () => {
  try {
    const id = route.params.id;
    const res = await api.get(`/users/${id}`);
    user.value = res.data;
  } catch (error) {
    console.error("Erreur API:", error);
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();
onMounted(fetchUser);
</script>
