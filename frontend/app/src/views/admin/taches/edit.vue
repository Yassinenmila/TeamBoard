<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Édition</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Modifier la Mission</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="updateTask"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Mise à jour...' : 'Sauvegarder les modifications' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement des données...</p>
        </div>

        <div v-else class="max-w-4xl mx-auto">
          <div class="grid grid-cols-12 gap-12">
            <div class="col-span-12 lg:col-span-8 space-y-8">
              <div class="bg-white p-10 rounded-[2rem] border border-slate-100 shadow-sm space-y-8">
                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Désignation de la tâche</label>
                  <input
                    v-model="form.titre"
                    type="text"
                    class="w-full text-3xl font-black tracking-tighter border-none p-0 focus:ring-0 placeholder:text-slate-100"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Détails techniques</label>
                  <textarea
                    v-model="form.description"
                    rows="6"
                    class="w-full bg-slate-50 border-none rounded-2xl p-6 text-sm font-medium text-slate-600 focus:ring-1 focus:ring-emerald-500 transition-all"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-4 space-y-6">
              <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-8">
                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Statut Actuel</label>
                  <select v-model="form.status" class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500">
                    <option value="en attente">En attente</option>
                    <option value="en cours">En cours</option>
                    <option value="terminée">Terminée</option>
                  </select>
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Date Limite</label>
                  <input
                    v-model="form.date_limite"
                    type="date"
                    class="w-full bg-slate-50 border-none rounded-xl py-3 px-4 text-xs font-bold text-slate-900"
                  >
                </div>
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
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const fetching = ref(true);

const form = ref({
  titre: '',
  description: '',
  status: '',
  date_limite: ''
});

// 1. Récupérer les données de la tâche existante
const fetchTaskData = async () => {
  try {
    const taskId = route.params.id;

    const response = await axios.get(
      `http://localhost:8000/api/taches/${taskId}`,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    );

    const data = response.data; // ✅ correction

    form.value = {
      titre: data.titre,
      description: data.description,
      status: data.status,
      date_limite: data.date_limite
    };

  } catch (error) {
    console.error(error);
    alert("Impossible de charger la tâche");
    goBack();
  } finally {
    fetching.value = false;
  }
};

onMounted(fetchTaskData);

// 2. Envoyer la mise à jour (PUT ou PATCH)
const updateTask = async () => {
  try {
    loading.value = true;
    const taskId = route.params.id;

    const response = await axios.put(
      `http://localhost:8000/api/taches/${taskId}`,
      form.value,
      {
        headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
      }
    );

    alert(response.data.message);
    router.push('/admin/dashboard'); // Retour au dashboard après succès

  } catch (error) {
    console.log(error.response?.data || error);
    alert('Erreur lors de la mise à jour');
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  window.history.back();
};
</script>
