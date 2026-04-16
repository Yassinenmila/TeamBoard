<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="user">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">
            Espace {{ isAdminOrManager ? 'Gestionnaire' : 'Collaborateur' }}
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
          Synchronisation des données de profil...
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
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Coordonnées</p>
                    <p class="text-xs font-bold text-slate-900 lowercase">{{ user.email }}</p>
                  </div>
                  <div>
                    <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-1">Identifiant unique</p>
                    <p class="text-xs font-bold text-slate-900">#TB-{{ user.id }}</p>
                  </div>
                </div>
              </div>

              <div class="bg-emerald-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-emerald-600/10">
                <div class="flex justify-between items-center">
                  <p class="text-[10px] font-black uppercase tracking-[0.2em]">Disponibilité</p>
                  <div class="w-2 h-2 rounded-full bg-white animate-pulse"></div>
                </div>
                <p class="text-xl font-black italic tracking-tighter mt-4 uppercase">Opérationnel</p>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-8 space-y-8">

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <template v-if="isAdminOrManager">
                  <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-3xl font-black text-slate-900 tracking-tighter">{{ user.taches_creees_count || 0 }}</p>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Tâches créées</p>
                  </div>
                  <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-3xl font-black text-emerald-600 tracking-tighter">{{ user.annonces_count || 0 }}</p>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Annonces</p>
                  </div>
                  <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-3xl font-black text-slate-900 tracking-tighter">{{ user.reunions_creees_count || 0 }}</p>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Réunions</p>
                  </div>
                </template>

                <template v-else>
                  <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm col-span-2">
                    <p class="text-3xl font-black text-emerald-600 tracking-tighter">{{ user.taches_assignees_count || 0 }}</p>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Missions assignées</p>
                  </div>
                  <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <p class="text-3xl font-black text-slate-900 tracking-tighter">{{ user.reunions_invitations_count || 0 }}</p>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mt-1">Invitations</p>
                  </div>
                </template>
              </div>

              <section class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] mb-8">
                  {{ isAdminOrManager ? 'Dernières créations' : 'Planning & Missions' }}
                </h3>

                <div class="space-y-4">
                  <router-link
                    v-for="item in activeList"
                    :key="item.id"
                    :to="item.type === 'tache' ? `/taches/${item.id}` : `/reunions/${item.id}`"
                    class="p-6 bg-slate-50 rounded-2xl flex justify-between items-center group hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-transparent hover:border-slate-100 cursor-pointer block"
                  >
                    <div class="flex items-center gap-4">
                      <div
                        class="w-2 h-2 rounded-full"
                        :class="item.type === 'tache' ? 'bg-emerald-500' : 'bg-blue-500'"
                      ></div>
                      <div>
                        <p class="text-[11px] font-black text-slate-900 uppercase tracking-tight italic group-hover:text-emerald-600 transition-colors">
                          {{ item.title }}
                        </p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">
                          {{ item.date }}
                        </p>
                      </div>
                    </div>

                    <div class="flex items-center gap-4">
                      <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest group-hover:text-slate-900 transition-colors">
                        {{ item.type }}
                      </span>
                      <span class="text-emerald-600 opacity-0 group-hover:opacity-100 transition-all translate-x-2 group-hover:translate-x-0">
                        →
                      </span>
                    </div>
                  </router-link>

                  <div v-if="activeList.length === 0" class="text-center py-10 italic text-[10px] text-slate-300 uppercase tracking-widest border border-dashed border-slate-100 rounded-2xl">
                    Aucun historique enregistré
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
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const route = useRoute();
const user = ref(null);
const loading = ref(true);

// 1. Détection du rôle pour affichage conditionnel
const isAdminOrManager = computed(() => {
  if (!user.value) return false;
  const role = user.value.role.toLowerCase();
  return role === 'admin' || role === 'responsable';
});

// 2. Gestion de la liste à afficher
const activeList = computed(() => {
  if (!user.value) return [];
  // Le backend doit envoyer soit 'creations' soit 'assignations'
  return isAdminOrManager.value ? (user.value.creations || []) : (user.value.assignations || []);
});

const fetchUser = async () => {
  try {
    const id = route.params.id;
    const res = await api.get(`/users/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
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

<style scoped>
.overflow-y-auto::-webkit-scrollbar {
  width: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
</style>
