<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Administration</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Modifier Collaborateur</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="updateUser"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Enregistrement...' : 'Mettre à jour le profil' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="fetching" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Récupération du profil...</p>
        </div>

        <div v-else class="max-w-4xl mx-auto">
          <div class="grid grid-cols-12 gap-8">

            <div class="col-span-12 lg:col-span-8 space-y-8">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">

                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Prénom</label>
                    <input
                      v-model="form.first_name"
                      type="text"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Nom</label>
                    <input
                      v-model="form.last_name"
                      type="text"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Adresse Email</label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Nouveau mot de passe (laisser vide si inchangé)</label>
                  <input
                    v-model="form.password"
                    type="password"
                    placeholder="••••••••"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>
              </div>
            </div>

            <div class="col-span-12 lg:col-span-4 space-y-6">
              <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Rôle Système</label>
                  <select v-model="form.role" class="w-full bg-slate-50 border-none rounded-xl py-4 px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500">
                    <option value="admin">Administrateur</option>
                    <option value="developpeur">Développeur</option>
                    <option value="manager">Manager</option>
                    <option value="client">Client</option>
                  </select>
                </div>

                <div class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100">
                  <p class="text-[9px] font-black text-emerald-700 uppercase tracking-[0.2em] mb-2">Compte Actif</p>
                  <p class="text-[10px] font-medium text-emerald-600/80 leading-relaxed">
                    Les modifications prendront effet immédiatement après la validation.
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

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '', // Optionnel pour l'update
  role: ''
});

// Récupérer les données de l'utilisateur au montage
const fetchUser = async () => {
  try {
    const id = route.params.id;
    const res = await api.get(`/users/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    form.value = {
      first_name: res.data.first_name,
      last_name: res.data.last_name,
      email: res.data.email,
      role: res.data.role,
      password: '' // On laisse vide par défaut
    };
  } catch (error) {
    console.error("Erreur de chargement:", error);
    alert("Impossible de trouver cet utilisateur.");
    router.push('/admin/users');
  } finally {
    fetching.value = false;
  }
};

onMounted(fetchUser);

// Envoyer la mise à jour
const updateUser = async () => {
  try {
    loading.value = true;
    const id = route.params.id;

    // On prépare les données (on ne balance pas le password s'il est vide)
    const payload = { ...form.value };
    if (!payload.password) delete payload.password;

    const res = await api.put(`/users/${id}`, payload, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    alert('Profil mis à jour avec succès');
    router.push('/users');

  } catch (error) {
    console.error(error);
    alert('Erreur lors de la mise à jour.');
  } finally {
    loading.value = false;
  }
};

const goBack = () => window.history.back();
</script>
