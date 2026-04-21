<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="!fetching">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Édition de session</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Mise à jour : {{ form.titre }}</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="updateReunion"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Synchronisation...' : 'Sauvegarder les changements' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement des données de la réunion...</p>
        </div>

        <div v-else class="max-w-5xl mx-auto">
          <div class="grid grid-cols-12 gap-8">

            <div class="col-span-12 lg:col-span-7 space-y-8">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Titre de la réunion</label>
                  <input
                    v-model="form.titre"
                    type="text"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Date prévue</label>
                    <input
                      v-model="form.date"
                      type="date"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Heure de début</label>
                    <input
                      v-model="form.heure"
                      type="time"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Lieu ou Lien Virtuel</label>
                  <input
                    v-model="form.lieu"
                    type="text"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Notes & Ordre du jour</label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all resize-none"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-5 space-y-6">
              <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.3em] mb-6 text-center italic">Liste des participants</h3>

                <div class="space-y-3 max-h-[420px] overflow-y-auto pr-2">
                  <div
                    v-for="user in users"
                    :key="user.id"
                    @click="toggleInvite(user.id)"
                    class="flex items-center justify-between p-4 rounded-2xl cursor-pointer transition-all border border-transparent"
                    :class="isInvited(user.id) ? 'bg-emerald-50 border-emerald-100' : 'bg-slate-50 hover:bg-white hover:border-slate-100'"
                  >
                    <div class="flex items-center gap-3">
                      <div class="w-8 h-8 rounded-lg bg-slate-900 text-white flex items-center justify-center text-[10px] font-black italic">
                        {{ user.first_name[0] }}{{ user.last_name[0] }}
                      </div>
                      <div>
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight italic">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ user.role }}</p>
                      </div>
                    </div>

                    <div
                      class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all shadow-sm"
                      :class="isInvited(user.id) ? 'bg-emerald-600 border-emerald-600' : 'border-slate-200'"
                    >
                      <span v-if="isInvited(user.id)" class="text-[8px] text-white">✓</span>
                    </div>
                  </div>
                </div>

                <div class="mt-8 p-6 bg-slate-900 rounded-2xl shadow-xl shadow-slate-900/10">
                  <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-2">Informations</p>
                  <p class="text-[10px] font-medium text-slate-400 leading-relaxed italic">
                    La modification de la liste d'invitations enverra une mise à jour automatique aux nouveaux participants.
                  </p>
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
import api from '@/services/api';

const route = useRoute();
const router = useRouter();
const loading = ref(false);
const fetching = ref(true);
const users = ref([]);

const form = ref({
  titre: '',
  date: '',
  heure: '',
  lieu: '',
  description: '',
  invitations: [] // Variable mise à jour selon ta demande
});

const fetchData = async () => {
  try {
    const id = route.params.id;
    const [reunionRes, usersRes] = await Promise.all([
      api.get(`/reunions/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } }),
      api.get('/users', { headers: { Authorization: `Bearer ${localStorage.getItem('token')}` } })
    ]);

    const r = reunionRes.data;
    form.value = {
      titre: r.titre,
      date: r.date,
      heure: r.heure,
      lieu: r.lieu,
      description: r.description,
      // On s'assure de mapper les invités existants dans le tableau 'invitations'
      invitations: r.invites ? r.invites.map(u => u.id) : []
    };
    users.value = usersRes.data;
  } catch (error) {
    console.error("Erreur de chargement:", error);
    router.push('/admin/reunions');
  } finally {
    fetching.value = false;
  }
};

const toggleInvite = (id) => {
  const index = form.value.invitations.indexOf(id);
  if (index > -1) {
    form.value.invitations.splice(index, 1);
  } else {
    form.value.invitations.push(id);
  }
};

const isInvited = (id) => form.value.invitations.includes(id);

const updateReunion = async () => {
  try {
    loading.value = true;
    const id = route.params.id;
    // Envoi du payload avec la clé 'invitations'
    await api.put(`/reunions/${id}`, form.value, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    router.push(`/admin/reunions/${id}`);
  } catch (error) {
    console.log(error.response.data);
    console.error(error);
    alert('Erreur lors de la mise à jour de la session.');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();

onMounted(fetchData);
</script>

<style scoped>
.max-h-\[420px\]::-webkit-scrollbar {
  width: 4px;
}
.max-h-\[420px\]::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
</style>
