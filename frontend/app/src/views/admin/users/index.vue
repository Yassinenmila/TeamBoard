<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1">Administration</p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">Collaborateurs</h1>
        </div>

        <div class="flex items-center gap-6">
          <div class="relative">
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Rechercher par nom ou email..."
              class="bg-slate-50 border-none rounded-xl px-6 py-3 text-xs font-bold w-72 focus:ring-1 focus:ring-emerald-500 transition-all placeholder:text-slate-300"
            >
          </div>
          <router-link to="/admin/users/create" class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/10">
            + Ajouter un utilisateur
          </router-link>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div v-if="loading" class="flex justify-center py-20">
          <p class="text-[10px] font-black uppercase tracking-[0.5em] text-slate-300 animate-pulse">Chargement de l'équipe...</p>
        </div>

        <div v-else class="max-w-7xl mx-auto space-y-12">
          <section class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-white">
              <h2 class="text-xs font-black text-slate-900 uppercase tracking-[0.3em]">Répertoire global</h2>
              <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ filteredUsers.length }} Membres</span>
            </div>

            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse">
                <thead>
                  <tr class="bg-slate-50/50">
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest w-20">ID</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Collaborateur</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Email</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rôle</th>
                    <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                  <tr
                    v-for="user in filteredUsers"
                    :key="user.id"
                    @click="goToUser(user.id)"
                    class="hover:bg-slate-50/50 transition-colors group cursor-pointer"
                  >
                    <td class="px-8 py-6 text-xs font-bold text-slate-300">
                      #{{ user.id }}
                    </td>

                    <td class="px-8 py-6">
                      <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center text-[10px] font-black italic group-hover:bg-emerald-600 transition-colors">
                          {{ user.first_name?.[0] }}{{ user.last_name?.[0] }}
                        </div>
                        <div class="flex flex-col">
                          <p class="text-sm font-black text-slate-900 uppercase tracking-tight">
                            {{ user.first_name }} <span class="text-emerald-600">{{ user.last_name }}</span>
                          </p>
                          <span class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter">Membre actif</span>
                        </div>
                      </div>
                    </td>

                    <td class="px-8 py-6">
                      <span class="text-xs font-bold text-slate-500">{{ user.email }}</span>
                    </td>

                    <td class="px-8 py-6">
                      <span class="px-4 py-1.5 bg-slate-100 text-slate-600 text-[9px] font-black rounded-full uppercase tracking-widest group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                        {{ user.role }}
                      </span>
                    </td>

                    <td class="px-8 py-6 text-right space-x-2">
                      <router-link
                        :to="`/admin/users/edit/${user.id}`"
                        @click.stop
                        class="text-[10px] font-black text-slate-400 hover:text-slate-900 uppercase tracking-widest transition-colors px-2"
                      >
                        Éditer
                      </router-link>
                      <button
                        @click.stop="deleteUser(user.id)"
                        class="text-[10px] font-black text-rose-300 hover:text-rose-600 uppercase tracking-widest transition-colors px-2"
                      >
                        Supprimer
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import Sidebar from '@/components/admin/Sidebar.vue';
import api from '@/services/api';

const router = useRouter();
const users = ref([]);
const loading = ref(true);
const searchQuery = ref('');

// Navigation vers la fiche détaillée
const goToUser = (id) => {
  router.push(`/admin/users/${id}`);
};

// Récupération des utilisateurs
const fetchUsers = async () => {
  try {
    const res = await api.get('/users', {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    users.value = res.data;
  } catch (error) {
    console.error("Erreur de chargement:", error);
  } finally {
    loading.value = false;
  }
};

// Suppression d'un utilisateur
const deleteUser = async (id) => {
  if (!confirm("Êtes-vous sûr de vouloir supprimer ce collaborateur ?")) return;

  try {
    await api.delete(`/users/${id}`, {
      headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
    });
    // Mise à jour de la liste locale après suppression réussie
    users.value = users.value.filter(user => user.id !== id);
  } catch (error) {
    console.error("Erreur lors de la suppression:", error);
    alert("Impossible de supprimer l'utilisateur.");
  }
};

// Recherche filtrée
const filteredUsers = computed(() => {
  const search = searchQuery.value.toLowerCase().trim();
  if (!search) return users.value;

  return users.value.filter(user => {
    const firstName = user.first_name?.toLowerCase() || '';
    const lastName = user.last_name?.toLowerCase() || '';
    const email = user.email?.toLowerCase() || '';
    const fullName = `${firstName} ${lastName}`;

    return fullName.includes(search) || email.includes(search);
  });
});

onMounted(fetchUsers);
</script>
