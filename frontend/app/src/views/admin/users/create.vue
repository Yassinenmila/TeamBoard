<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Administration</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Nouvel Utilisateur</h1>
        </div>

        <div class="flex items-center gap-8">
          <button @click="goBack" class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
            Annuler
          </button>
          <button
            @click="submitUser"
            :disabled="loading"
            class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10 disabled:opacity-50"
          >
            {{ loading ? 'Création...' : 'Créer le compte' }}
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-4xl mx-auto">

          <div class="grid grid-cols-12 gap-8">

            <div class="col-span-12 lg:col-span-8 space-y-8">
              <div class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">

                <div class="grid grid-cols-2 gap-6">
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Prénom</label>
                    <input
                      v-model="form.first_name"
                      type="text"
                      placeholder="Ex: Yassine"
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                  <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Nom</label>
                    <input
                      v-model="form.last_name"
                      type="text"
                      placeholder="Ex: B."
                      class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                    >
                  </div>
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Adresse Email Professionnelle</label>
                  <input
                    v-model="form.email"
                    type="email"
                    placeholder="yassine@teamboard.com"
                    class="w-full bg-slate-50 border-none rounded-2xl p-4 text-sm font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500 transition-all"
                  >
                </div>

                <div>
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Mot de passe temporaire</label>
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
                  <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Attribuer un Rôle</label>
                  <select v-model="form.role" class="w-full bg-slate-50 border-none rounded-xl py-4 px-4 text-xs font-bold text-slate-900 focus:ring-1 focus:ring-emerald-500">
                    <option value="admin">Administrateur</option>
                    <option value="responsable">Responsable</option>
                    <option value="membre">Membre</option>
                  </select>
                </div>

                <div class="p-6 bg-slate-900 rounded-2xl">
                  <p class="text-[9px] font-black text-emerald-400 uppercase tracking-[0.2em] mb-2">Note de sécurité</p>
                  <p class="text-[10px] font-medium text-slate-400 leading-relaxed">
                    Un email d'invitation sera envoyé automatiquement pour confirmer l'activation du compte.
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const loading = ref(false);

const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  password: '',
  role: ''
});

const submitUser = async () => {
  // Petite validation front-end
  if (!form.value.first_name || !form.value.email || !form.value.password) {
    alert('Veuillez remplir les champs obligatoires.');
    return;
  }

  try {
    loading.value = true;

    const response = await api.post('/users', form.value, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });

    alert('Utilisateur créé avec succès');
    router.push('/users'); // Redirection vers la liste

  } catch (error) {
    if (error.response?.status === 422) {
      alert('Erreur : cet email est peut-être déjà utilisé.');
    } else {
      console.error(error);
      alert('Une erreur est survenue lors de la création.');
    }
  } finally {
    loading.value = false;
  }
};

const goBack = () => {
  window.history.back();
};
</script>
