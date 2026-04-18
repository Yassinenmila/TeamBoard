<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Édition</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Modifier l'annonce</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Annuler</button>
          <button
            @click="updateAnnonce"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-10 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Mise à jour...' : 'Enregistrer les modifications' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20 italic text-slate-300 animate-pulse uppercase tracking-widest text-[10px]">
          Chargement des données...
        </div>

        <div v-else class="max-w-4xl mx-auto">
          <div class="bg-white p-12 rounded-[3rem] border border-slate-100 shadow-sm space-y-10">

            <div class="space-y-4">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Titre du flash</label>
              <input
                v-model="form.titre"
                type="text"
                class="w-full text-4xl font-black tracking-tighter border-none bg-slate-50/50 rounded-[1.5rem] p-8 focus:ring-2 focus:ring-emerald-500/20 transition-all italic text-slate-900"
              >
            </div>

            <div class="grid grid-cols-2 gap-8">
              <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Niveau d'urgence</label>
                <div class="flex gap-4 p-2 bg-slate-50/50 rounded-2xl border border-slate-100">
                  <button
                    @click="form.type = 'general'"
                    :class="form.type === 'general' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-400'"
                    class="flex-1 py-3 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all"
                  >
                    Général
                  </button>
                  <button
                    @click="form.type = 'urgent'"
                    :class="form.type === 'urgent' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'text-slate-400'"
                    class="flex-1 py-3 text-[9px] font-black uppercase tracking-widest rounded-xl transition-all"
                  >
                    🔥 Urgent
                  </button>
                </div>
              </div>

              <div class="space-y-4">
                <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Dernière modification</label>
                <div class="w-full bg-slate-50/50 border border-slate-100 rounded-2xl p-4 text-[10px] font-black text-slate-400 uppercase italic">
                  {{ lastUpdate }}
                </div>
              </div>
            </div>

            <div class="space-y-4">
              <label class="block text-[10px] font-black text-slate-300 uppercase tracking-[0.3em] ml-2 italic">Corps de l'annonce</label>
              <textarea
                v-model="form.contenu"
                rows="8"
                class="w-full bg-slate-50/50 border-none rounded-[2rem] p-8 text-lg font-bold text-slate-600 focus:ring-2 focus:ring-emerald-500/20 transition-all italic"
              ></textarea>
            </div>

          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const route = useRoute(); // Pour récupérer l'ID dans l'URL

const loading = ref(false);
const fetching = ref(true);
const lastUpdate = ref('');

const form = ref({
  titre: '',
  contenu: '',
  type: ''
});

// 1. Récupérer l'annonce existante au montage
const fetchAnnonceData = async () => {
  try {
    const id = route.params.id; // On suppose que ta route est /annonces/edit/:id
    const res = await api.get(`/annonces/${id}`);

    form.value.titre = res.data.titre;
    form.value.contenu = res.data.contenu;
    form.value.type = res.data.type;

    lastUpdate.value = new Date(res.data.updated_at).toLocaleString('fr-FR');
  } catch (error) {
    console.error("Erreur de chargement:", error);
    alert("Impossible de charger l'annonce");
    router.push('/admin/annances');
  } finally {
    fetching.value = false;
  }
};

// 2. Envoyer les modifications
const updateAnnonce = async () => {
  loading.value = true;
  try {
    const id = route.params.id;
    await api.put(`/annonces/${id}`, form.value);
    router.push('/admin/annances');
  } catch (error) {
    console.error(error);
    alert('Erreur lors de la mise à jour');
  } finally {
    loading.value = false;
  }
};

const goBack = () => router.back();

onMounted(fetchAnnonceData);
</script>

<style scoped>
input:focus, textarea:focus {
  outline: none;
}
</style>
