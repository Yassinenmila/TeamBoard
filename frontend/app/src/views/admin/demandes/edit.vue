<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="demande">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Instruction Administrative</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Traiter la Demande #{{ demande.id }}</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Annuler</button>
          <button
            @click="submitDecision"
            :disabled="loading || (form.statut === 'refusee' && !form.message)"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Synchronisation...' : 'Confirmer la décision' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Accès au dossier...</p>
        </div>

        <div v-else-if="demande" class="max-w-6xl mx-auto grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-7 space-y-8">
            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-10">

              <div class="flex items-center gap-6 p-6 bg-slate-50 rounded-[2rem] border border-slate-100">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-xl font-black italic text-slate-900">
                  {{ demande.user?.first_name[0] }}
                </div>
                <div>
                  <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Soumis par</label>
                  <p class="text-sm font-black text-slate-900 uppercase tracking-tighter italic">
                    {{ demande.user?.first_name }} {{ demande.user?.last_name }}
                  </p>
                  <p class="text-[10px] font-bold text-slate-400 italic">{{ demande.user?.email }}</p>
                </div>
              </div>

              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-4 italic">Description de la requête</label>
                <div class="w-full bg-slate-50 rounded-[2rem] p-8 text-sm font-bold text-slate-900 border border-transparent italic leading-relaxed">
                  {{ demande.description }}
                </div>
              </div>

            </div>
          </div>

          <div class="col-span-12 lg:col-span-5 space-y-6">

            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl shadow-slate-900/20 text-white space-y-8">
              <h3 class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] italic">Action administrative</h3>

              <div class="space-y-3">
                <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-2">Choisir un statut</label>
                <div class="grid grid-cols-1 gap-2">
                  <button
                    v-for="(label, key) in statusOptions" :key="key"
                    type="button"
                    @click="form.statut = key"
                    :class="form.statut === key ? statusThemes[key].active : 'bg-slate-800 text-slate-500 border-transparent'"
                    class="flex items-center justify-between p-4 rounded-xl border transition-all duration-300 group"
                  >
                    <span class="text-[10px] font-black uppercase tracking-widest italic">{{ label }}</span>
                    <span class="text-lg group-hover:scale-125 transition-transform">{{ statusThemes[key].icon }}</span>
                  </button>
                </div>
              </div>

              <div>
                <label class="block text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-4 italic">
                  Justification
                  <span v-if="form.statut === 'refusee'" class="text-rose-500 font-black tracking-tighter ml-2">(Obligatoire pour refus)</span>
                </label>
                <textarea
                  v-model="form.message"
                  rows="5"
                  class="w-full bg-slate-800 border-none rounded-2xl p-5 text-xs font-bold text-white focus:ring-1 focus:ring-emerald-500 placeholder:text-slate-700 resize-none italic transition-all"
                  placeholder="Expliquez la raison de votre choix..."
                ></textarea>
              </div>

            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const fetching = ref(true);
const demande = ref(null);

const form = reactive({
  statut: 'en_attente',
  message: ''
});

// Configuration des styles et icônes
const statusOptions = {
  en_attente: 'Mettre en attente',
  acceptee: 'Accepter la demande',
  refusee: 'Refuser la demande'
};

const statusThemes = {
  en_attente: { icon: '⏳', active: 'bg-amber-500/10 border-amber-500/50 text-amber-500 shadow-lg shadow-amber-500/5' },
  acceptee: { icon: '✅', active: 'bg-emerald-500 text-white border-emerald-400 shadow-lg shadow-emerald-500/20' },
  refusee: { icon: '❌', active: 'bg-rose-600 text-white border-rose-400 shadow-lg shadow-rose-600/20' }
};

const mapStatusToStatut = (status) => {
  if (status === 'accepted' || status === 'acceptee') return 'acceptee';
  if (status === 'rejected' || status === 'refusee') return 'refusee';
  return 'en_attente';
};

const fetchDemande = async () => {
  try {
    const response = await api.get(`/demandes/${route.params.id}`);
    demande.value = response.data;
    // On synchronise le statut initial
    form.statut = mapStatusToStatut(demande.value.statut || demande.value.status);
  } catch (error) {
    router.push('/admin/demandes');
  } finally {
    fetching.value = false;
  }
};

const submitDecision = async () => {
  loading.value = true;
  try {
    await api.put(`/demandes/${demande.value.id}`, form);
    router.push('/admin/demandes');
  } catch (error) {
    console.log(error.response?.data?.message || 'Erreur lors de la mise à jour');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();

onMounted(fetchDemande);
</script>

<style scoped>
/* Scrollbar ultra discrète */
.overflow-y-auto::-webkit-scrollbar { width: 3px; }
.overflow-y-auto::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
