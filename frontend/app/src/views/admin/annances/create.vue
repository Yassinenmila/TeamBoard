<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Rédaction</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Publier une annonce</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Annuler</button>
          <button
            @click="storeAnnonce"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-10 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Publication...' : 'Diffuser l\'annonce' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-4xl mx-auto">

          <div class="bg-white p-12 rounded-[3rem] border border-slate-100 shadow-sm space-y-10">

            <div class="space-y-4">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Titre du flash</label>
              <input
                v-model="form.titre"
                type="text"
                placeholder="Ex: Maintenance du serveur ce soir..."
                class="w-full text-4xl font-black tracking-tighter border-none bg-slate-50/50 rounded-[1.5rem] p-8 focus:ring-2 focus:ring-emerald-500/20 placeholder:text-slate-200 transition-all italic"
              >
            </div>

            <div class="grid grid-cols-2 gap-8">
              <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Niveau d'urgence</label>
                <div class="flex gap-4 p-2 bg-slate-50/50 rounded-2xl border border-slate-100">
                  <button
                    @click="form.type = 'general'"
                    :class="form.type === 'general' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 py-3 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all"
                  >
                    Général
                  </button>
                  <button
                    @click="form.type = 'urgent'"
                    :class="form.type === 'urgent' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400 hover:text-slate-600'"
                    class="flex-1 py-3 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all"
                  >
                    🔥 Urgent
                  </button>
                </div>
              </div>

              <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Date prévue</label>
                <div class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-4 text-[10px] font-black text-slate-400 uppercase italic">
                  Aujourd'hui, {{ currentDate }}
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Corps de l'annonce</label>
              <textarea
                v-model="form.contenu"
                rows="8"
                placeholder="Décrivez ici les détails de votre annonce..."
                class="w-full bg-slate-50/50 border-none rounded-[2rem] p-8 text-lg font-bold text-slate-600 focus:ring-2 focus:ring-emerald-500/20 placeholder:text-slate-200 transition-all italic"
              ></textarea>
            </div>

          </div>

          <div class="mt-8 px-12 py-6 bg-emerald-50/50 border border-emerald-100 rounded-3xl flex items-center justify-between">
            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest italic">
              L'annonce sera visible par tous les collaborateurs dès la validation.
            </p>
            <span class="text-xl">📢</span>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const loading = ref(false);

const form = ref({
  titre: '',
  contenu: '',
  type: '' // Valeur par défaut compatible avec le backend
});

const currentDate = computed(() => {
  return new Date().toLocaleDateString('fr-FR', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
});

const storeAnnonce = async () => {
  if (!form.value.titre || !form.value.contenu) {
    alert('Veuillez remplir tous les champs');
    return;
  }

  loading.value = true;
  try {
    await api.post('/annonces', form.value);
    router.push('/admin/annances'); // Redirection vers l'index
  } catch (error) {
    console.error(error);
    alert('Erreur lors de la création');
  } finally {
    loading.value = false;
  }
};

const goBack = () => router.back();
</script>

<style scoped>
/* Suppression de l'outline par défaut sur les inputs */
input:focus, textarea:focus {
  outline: none;
}
</style>
