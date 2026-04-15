<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="tache">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Détails de la mission</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">{{ tache.titre }}</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">
            Retour
          </button>
          <router-link
            v-if="tache"
            :to="`/taches/edit/${tache.id}`"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10"
          >
            Modifier la tâche
          </router-link>
        </div>
      </header>


        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement des données...</p>
        </div>

        <div v-else-if="tache" class="max-w-6xl mx-auto grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-8 space-y-10">

            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6">Objectifs techniques</label>
              <p class="text-slate-600 leading-relaxed font-medium">
                {{ tache.description }}
              </p>
            </div>

            <div class="space-y-6">
              <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em] px-4">Flux de discussion</h3>

              <div v-for="com in tache.commentaires" :key="com.id" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-start mb-4">
                  <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">{{ com.user?.name || 'Collaborateur' }}</span>
                  <span class="text-[9px] font-bold text-slate-300 uppercase">{{ formatDate(com.created_at) }}</span>
                </div>
                <p class="text-sm text-slate-500 font-medium">{{ com.contenu }}</p>
              </div>

              <div class="bg-slate-100/50 p-2 rounded-[2rem] flex items-center gap-2">
                <input type="text" placeholder="Ajouter un commentaire..." class="flex-1 bg-transparent border-none focus:ring-0 px-6 text-sm font-bold text-slate-900">
                <button class="bg-white text-[10px] font-black uppercase tracking-widest px-6 py-3 rounded-2xl shadow-sm hover:bg-slate-900 hover:text-white transition-all">Envoyer</button>
              </div>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-4 space-y-8">

            <div class="bg-slate-900 text-white p-8 rounded-[2.5rem] shadow-xl shadow-slate-900/10">
              <div class="mb-8">
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-2">Statut Actuel</p>
                <p class="text-xl font-black uppercase italic tracking-tighter">{{ tache.status }}</p>
              </div>
              <div>
                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-2">Échéance</p>
                <p class="text-xl font-black italic tracking-tighter">{{ tache.date_limite }}</p>
              </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6">Équipe assignée</p>
              <div class="space-y-4">
                <div v-for="user in tache.utilisateurs" :key="user.id" class="flex items-center gap-3">
                  <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-black border border-emerald-100">
                    {{ user.name.substring(0, 2).toUpperCase() }}
                  </div>
                  <span class="text-xs font-bold text-slate-700">{{ user.name }}</span>
                </div>
                <div v-if="!tache.utilisateurs?.length" class="text-xs font-bold text-slate-400 italic">Aucun membre assigné</div>
              </div>
            </div>

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-6">Livrables attachés</p>
              <div class="space-y-3">
                <div v-for="liv in tache.livrables" :key="liv.id" class="p-4 bg-slate-50 rounded-2xl border border-transparent hover:border-emerald-100 transition-all cursor-pointer">
                  <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight">{{ liv.nom }}</p>
                  <p class="text-[9px] font-bold text-emerald-600 uppercase mt-1">Lien Document</p>
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
import axios from 'axios';

const route = useRoute();
const tache = ref(null);
const loading = ref(true);

const fetchTache = async () => {
  try {
    const response = await axios.get(`http://localhost:8000/api/taches/${route.params.id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    tache.value = response.data;
  } catch (error) {
    console.error("Erreur chargement tâche:", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'short'
  });
};

const goBack = () => window.history.back();

onMounted(fetchTache);
</script>
