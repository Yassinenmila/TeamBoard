<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div v-if="!fetching">
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">Gestion & Revue</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Mission #{{ route.params.id }}</h1>
        </div>

        <div class="flex items-center gap-6">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">Annuler</button>
          <button
            @click="updateTask"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Synchronisation...' : 'Enregistrer les modifications' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20 italic text-slate-300 animate-pulse uppercase tracking-widest text-[10px]">Accès aux données...</div>

        <div v-else class="max-w-6xl mx-auto grid grid-cols-12 gap-10">

          <div class="col-span-12 lg:col-span-7 space-y-8">
            <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Titre de la mission</label>
                <input v-model="form.titre" type="text" class="w-full text-3xl font-black tracking-tighter border-none p-0 focus:ring-0 italic">
              </div>

              <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Assigner à un collaborateur</label>
                <select v-model="form.user_id" class="w-full bg-white border-none rounded-xl py-3 px-4 text-xs font-bold text-slate-900 shadow-sm focus:ring-1 focus:ring-emerald-500">
                  <option value="">Sélectionner un utilisateur</option>
                  <option v-for="u in users" :key="u.id" :value="u.id">
                    {{ u.first_name }} {{ u.last_name }} ({{ u.role }})
                  </option>
                </select>
              </div>

              <div>
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4 italic">Description</label>
                <textarea v-model="form.description" rows="5" class="w-full bg-slate-50 border-none rounded-[2rem] p-6 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 italic"></textarea>
              </div>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-5 space-y-6">

            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
              <div>
                <label class="block text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">État</label>
                <select v-model="form.status" class="w-full bg-slate-50 border-none rounded-xl py-4 px-4 text-[10px] font-black uppercase text-slate-900">
                  <option value="en attente">⏳ En attente</option>
                  <option value="en cours">🚀 En cours</option>
                  <option value="terminée">✅ Terminée</option>
                </select>
              </div>
              <div>
                <label class="block text-[9px] font-black text-slate-300 uppercase tracking-widest mb-3">Deadline</label>
                <input v-model="form.date_limite" type="date" class="w-full bg-slate-50 border-none rounded-xl py-4 px-4 text-[10px] font-black text-slate-900">
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
  description: '',
  status: '',
  date_limite: '',
  user_id: '',
  validation: '',
  commentaire_admin: ''
});

const fetchData = async () => {
  try {
    const usersRes = await api.get('/users');
    users.value = usersRes.data;

    const taskRes = await api.get(`/taches/${route.params.id}`);
    const data = taskRes.data;

    form.value = {
      titre: data.titre,
      description: data.description,
      status: data.status,
      date_limite: data.date_limite,
      user_id: data.utilisateurs?.[0]?.id || '',
      validation: data.validation || '',
      commentaire_admin: data.commentaire_admin || ''
    };
  } catch (error) {
    console.error(error);
  } finally {
    fetching.value = false;
  }
};

const updateTask = async () => {
  loading.value = true;
  try {
    const updatePayload = {
      titre: form.value.titre,
      description: form.value.description,
      status: form.value.status,
      date_limite: form.value.date_limite,
      validation: form.value.validation,
      commentaire_admin: form.value.commentaire_admin
    };

    await api.put(`/taches/${route.params.id}`, updatePayload);

    if (form.value.user_id) {
      await api.post(`/taches/${route.params.id}/assigner`, {
        user_ids: [form.value.user_id]
      });
    }

    router.push('/admin/dashboard');
  } catch (error) {
    console.error(error);
    alert('Erreur lors de la mise à jour');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();
onMounted(fetchData);
</script>
