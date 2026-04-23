<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-4xl mx-auto px-8 py-16">

        <div class="mb-12 flex items-center justify-between">
          <button
            @click="$router.back()"
            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-indigo-600 transition-colors flex items-center gap-2"
          >
            ← Retour aux annonces
          </button>

          <router-link
            v-if="isOwner"
            :to="`/user/annances/edit/${annonce.id}`"
            class="bg-indigo-50 text-indigo-600 px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all shadow-sm"
          >
            Modifier l'annonce
          </router-link>
        </div>

        <div v-if="loading" class="py-20 text-center italic text-slate-300 uppercase tracking-[0.5em] text-[10px] animate-pulse">
          Ouverture du communiqué...
        </div>

        <article v-else class="space-y-10">

          <header class="space-y-6">
            <div class="flex items-center gap-4">
              <span class="px-5 py-2 bg-indigo-600 text-white text-[9px] font-black uppercase tracking-widest rounded-full italic shadow-lg shadow-indigo-200">
                Annonce Officielle
              </span>
              <span class="text-[10px] font-black text-slate-300 uppercase tracking-widest">
                Publié le {{ formatDate(annonce.created_at) }}
              </span>
            </div>

            <h1 class="text-6xl font-black text-slate-900 tracking-tighter uppercase italic leading-[0.9] break-words">
              {{ annonce.titre }}
            </h1>

            <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
              <div class="w-12 h-12 rounded-2xl bg-slate-900 flex items-center justify-center text-white font-black italic">
                {{ annonce.user?.first_name?.charAt(0) }} {{ annonce.user?.last_name?.charAt(0) }}
              </div>
              <div>
                <p class="text-xs font-black text-slate-900 uppercase italic">Auteur : {{ annonce.user?.first_name }} {{ annonce.user?.last_name }}</p>
                <p class="text-[9px] font-bold text-indigo-600 uppercase tracking-widest">Responsable Unité</p>
              </div>
            </div>
          </header>

          <div class="bg-white p-12 rounded-[3.5rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 text-slate-50 font-black text-8xl select-none">“</div>

            <p class="text-xl text-slate-700 font-medium leading-relaxed italic whitespace-pre-line relative z-10">
              {{ annonce.contenu }}
            </p>
          </div>

        </article>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import Sidebar from '@/components/users/Sidebar.vue';
import api from '@/services/api';
import { useAuthStore } from '@/stores/auth';

const route = useRoute();
const auth = useAuthStore();
const annonce = ref({});
const loading = ref(true);

// Logique de vérification de propriété
const isOwner = computed(() => {
  if (!annonce.value || !auth.user) return false;

  const isResponsable = auth.user.role === 'responsable';
  const isCreator = annonce.value.user_id === auth.user.id;

  return isResponsable && isCreator;
});

const fetchAnnonce = async () => {
  try {
    loading.value = true;
    const res = await api.get(`/annonces/${route.params.id}`);
    annonce.value = res.data;
  } catch (error) {
    console.error("Erreur lors de la récupération de l'annonce", error);
  } finally {
    loading.value = false;
  }
};

const formatDate = (date) => {
  if (!date) return '';
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

onMounted(fetchAnnonce);
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

/* Animation d'entrée pour le contenu */
article {
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>
