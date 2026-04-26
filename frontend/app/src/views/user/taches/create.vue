<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">

    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">

      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Workflow</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Nouvelle Mission</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="submitTask"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Enregistrement...' : 'Enregistrer la tâche' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-4xl mx-auto">

          <div class="grid grid-cols-12 gap-12">

            <div class="col-span-12 lg:col-span-8 space-y-8">
              <div class="bg-white p-10 rounded-[2rem] border border-slate-100 shadow-sm space-y-8">

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Désignation de la tâche</label>
                  <input
                    v-model="form.titre"
                    type="text"
                    placeholder="Ex: Finaliser l'API..."
                    class="w-full text-3xl font-black tracking-tighter border-none p-0 focus:ring-0 placeholder:text-slate-100"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Détails techniques</label>
                  <textarea
                    v-model="form.description"
                    rows="6"
                    placeholder="Expliquez ici ce qui doit être fait..."
                    class="w-full bg-slate-50 border-none rounded-2xl p-6 text-sm font-medium text-slate-600 focus:ring-1 focus:ring-emerald-500 transition-all"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-4 space-y-6">

              <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm space-y-8">

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">État initial</label>
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

              <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                <p class="text-[10px] font-bold text-emerald-700 leading-relaxed uppercase tracking-tight">
                  La tâche sera automatiquement assignée à votre profil en tant que créateur.
                </p>
              </div>

            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/users/Sidebar.vue';
import axios from 'axios'; // Ou ton instance API personnalisée

const loading = ref(false);

const form = ref({
  titre: '',
  description: '',
  status: 'en attente',
  date_limite: ''
});

const submitTask = async () => {
  try {
    loading.value = true;

    const response = await axios.post(
      'http://localhost:8000/api/taches',
      form.value,
      {
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }
    );

    alert(response.data.message);

    form.value = {
      titre: '',
      description: '',
      status: 'en attente',
      date_limite: ''
    };

  } catch (error) {
    console.log(error.response?.data || error);
    alert('Erreur lors de la création');
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  window.history.back();
};
</script>
