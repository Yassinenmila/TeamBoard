<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Planification</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Nouvelle Réunion</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="submitReunion"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Planification...' : 'Confirmer la session' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-5xl mx-auto">

          <div class="grid grid-cols-12 gap-8">

            <div class="col-span-12 lg:col-span-7 space-y-8">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Objet de la réunion</label>
                  <input
                    v-model="form.titre"
                    type="text"
                    placeholder="Ex: Weekly Sync / Briefing Projet X"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Date</label>
                    <input
                      v-model="form.date"
                      type="date"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Heure</label>
                    <input
                      v-model="form.heure"
                      type="time"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Lieu ou Lien (Google Meet)</label>
                  <input
                    v-model="form.lieu"
                    type="text"
                    placeholder="Ex: Salle de conférence ou meet.google.com/..."
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Description / Ordre du jour</label>
                  <textarea
                    v-model="form.description"
                    rows="4"
                    placeholder="Quels sont les points à aborder ?"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all resize-none"
                  ></textarea>
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-5 space-y-6">
              <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                <h3 class="text-[10px] font-black text-slate-900 uppercase tracking-[0.3em] mb-6 text-center">Inviter des collaborateurs</h3>

                <div class="space-y-3 max-h-[400px] overflow-y-auto pr-2">
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
                        <p class="text-[10px] font-black text-slate-900 uppercase tracking-tight">{{ user.first_name }} {{ user.last_name }}</p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">{{ user.role }}</p>
                      </div>
                    </div>

                    <div
                      class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                      :class="isInvited(user.id) ? 'bg-emerald-500 border-emerald-500' : 'border-slate-200'"
                    >
                      <span v-if="isInvited(user.id)" class="text-[10px] text-white">✓</span>
                    </div>
                  </div>
                </div>

                <div class="mt-8 p-6 bg-slate-900 rounded-2xl">
                  <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.2em] mb-2">Notification</p>
                  <p class="text-[10px] font-medium text-slate-400 leading-relaxed">
                    Un rappel sera envoyé à tous les invités 15 minutes avant le début.
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
import { useRouter } from 'vue-router';
import Sidebar from '@/components/users/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const loading = ref(false);
const users = ref([]);

const form = ref({
  titre: '',
  date: '',
  heure: '',
  lieu: '',
  description: '',
  invitations: []
});

// Charger les utilisateurs pour la liste d'invitation
const fetchUsers = async () => {
  try {
    const res = await api.get('/users', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    users.value = res.data;
  } catch (error) {
    console.error("Erreur chargement utilisateurs:", error);
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

const submitReunion = async () => {
  if (!form.value.titre || !form.value.date || !form.value.heure) {
    alert('Veuillez remplir les informations essentielles.');
    return;
  }
  console.log("DATA ENVOYÉE:", form.value);

  try {
    loading.value = true;
    await api.post('/reunions', form.value, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    alert('Réunion planifiée avec succès');
    router.push('/admin/reunions');
  } catch (error) {
    console.error(error);
    console.error(error.response?.data);
    alert('Erreur lors de la création.');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();

onMounted(fetchUsers);
</script>

<style scoped>
.max-h-\[400px\]::-webkit-scrollbar {
  width: 4px;
}
.max-h-\[400px\]::-webkit-scrollbar-thumb {
  background: #f1f5f9;
  border-radius: 10px;
}
</style>
