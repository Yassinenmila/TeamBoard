<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Requêtes</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Nouvelle Demande</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="submitDemande"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Envoi en cours...' : 'Envoyer la demande' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-3xl mx-auto">

          <div class="bg-white p-12 rounded-[3rem] border border-slate-100 shadow-sm space-y-10">

            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Nature de la demande</label>
              <div class="grid grid-cols-2 gap-4">
                <button
                  v-for="type in types"
                  :key="type"
                  @click="form.type = type"
                  type="button"
                  :class="form.type === type ? 'bg-slate-900 text-white shadow-xl shadow-slate-900/20' : 'bg-slate-50 text-slate-400 hover:bg-white hover:shadow-md border border-transparent hover:border-slate-100'"
                  class="p-6 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all italic"
                >
                  {{ type }}
                </button>
              </div>
            </div>

            <div>
              <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-6">Détails & Justifications</label>
              <textarea
                v-model="form.description"
                rows="6"
                placeholder="Expliquez votre demande ici..."
                class="w-full bg-slate-50 border-none rounded-[2rem] p-8 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-emerald-500/20 transition-all resize-none placeholder:text-slate-300 italic"
              ></textarea>
            </div>

            <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-center gap-4">
              <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
              <p class="text-[9px] font-black text-emerald-700 uppercase tracking-widest leading-relaxed">
                Votre demande sera enregistrée avec le statut "En attente" (Pending).
              </p>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const loading = ref(false);

const types = ['Matériel', 'Congés', 'Accès', 'Support Technique', 'Autre'];

const form = ref({
  type: '',
  description: ''
});

const submitDemande = async () => {
  if (!form.value.type || !form.value.description) {
    alert('Veuillez sélectionner un type et fournir une description.');
    return;
  }

  try {
    loading.value = true;
    await api.post('/demandes', form.value, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    router.push('/admin/demandes');
  } catch (error) {
    console.log(error.response.data);
    console.error(error);
    alert('Une erreur est survenue lors de l\'envoi.');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();
</script>

<style scoped>
/* Personnalisation légère pour l'input focus */
textarea:focus {
  outline: none;
  background-color: white;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
}
</style>
