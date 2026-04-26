<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900 font-sans">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-6xl mx-auto px-8 py-16">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-16">
          <div class="space-y-2">
            <div class="flex items-center gap-3">
              <span class="h-1 w-8 bg-emerald-500 rounded-full"></span>
              <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] italic">
                {{ userRole === 'responsable' ? 'Operations Control' : 'Personal Workspace' }}
              </p>
            </div>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
              {{ userRole === 'responsable' ? "Flux d'unité" : 'Mes Missions' }}
            </h1>
          </div>

          <div class="flex items-center gap-6">
            <div class="hidden sm:flex bg-white px-6 py-4 rounded-3xl border border-slate-100 shadow-sm items-center gap-4">
              <span class="text-3xl font-black text-slate-900 italic leading-none">{{ taches.length }}</span>
              <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-tight">Missions<br>Total</span>
            </div>

            <router-link
              v-if="userRole === 'responsable'"
              to="/user/taches/create"
              class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-5 rounded-2xl hover:bg-emerald-600 transition-all shadow-xl shadow-slate-900/10 active:scale-95 flex items-center gap-3"
            >
              <span>+ Nouvelle Tâche</span>
            </router-link>
          </div>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-32 space-y-6">
          <div class="w-12 h-12 border-[3px] border-slate-200 border-t-emerald-500 rounded-full animate-spin"></div>
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.5em] animate-pulse italic">Synchronisation...</p>
        </div>

        <div v-else class="grid gap-6">
          <router-link
            v-for="tache in taches"
            :key="tache.id"
            :to="`/user/taches/${tache.id}`"
            class="group relative bg-white rounded-[2.5rem] border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-emerald-900/5 hover:-translate-y-1 transition-all duration-500 overflow-hidden block"
          >
            <div :class="statusBorder(tache.status)" class="absolute left-0 top-0 bottom-0 w-1.5 transition-all group-hover:w-3"></div>

            <div class="p-8 lg:p-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">

              <div class="flex-1 space-y-6">
                <div class="flex items-center gap-3">
                  <span class="text-[9px] font-black px-4 py-1.5 bg-slate-900 text-white rounded-full uppercase italic tracking-[0.2em]">
                    ID:{{ tache.id }}
                  </span>
                  <span
                    :class="statusClass(tache.status)"
                    class="text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-[0.2em] border border-current"
                  >
                    {{ tache.status }}
                  </span>
                </div>

                <div class="space-y-2">
                  <h3 class="text-2xl font-black text-slate-800 uppercase italic leading-tight group-hover:text-emerald-600 transition-colors">
                    {{ tache.titre }}
                  </h3>
                  <p class="text-sm text-slate-400 font-medium line-clamp-2 italic max-w-2xl">
                    {{ tache.description }}
                  </p>
                </div>

                <div class="flex items-center gap-6 pt-2">
                  <div class="flex -space-x-3">
                    <div
                      v-for="u in (tache.utilisateurs || []).slice(0, 3)"
                      :key="u.id"
                      class="w-9 h-9 rounded-xl border-[3px] border-white bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-600 uppercase"
                    >
                      {{ u.name?.charAt(0) }}
                    </div>
                    <div v-if="(tache.utilisateurs || []).length > 3" class="w-9 h-9 rounded-xl border-[3px] border-white bg-emerald-500 text-white flex items-center justify-center text-[9px] font-black">
                      +{{ (tache.utilisateurs || []).length - 3 }}
                    </div>
                  </div>

                  <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic">
                    By <span class="text-slate-600">{{ tache.createur?.name || 'Admin' }}</span>
                  </span>
                </div>
              </div>

              <div class="flex items-center lg:flex-col lg:items-end justify-between gap-4">
                <div class="text-left lg:text-right">
                  <p class="text-[8px] font-black text-slate-300 uppercase tracking-[0.3em] mb-1 italic">Échéance</p>
                  <p class="text-xl font-black text-slate-900 italic tracking-tighter">{{ formatDate(tache.date_limite) }}</p>
                </div>

                <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
                  <span class="text-xl italic font-black">→</span>
                </div>
              </div>

            </div>
          </router-link>

          <div v-if="taches.length === 0" class="bg-white rounded-[3rem] py-24 text-center border-2 border-dashed border-slate-100">
            <div class="text-5xl mb-6 grayscale opacity-20">📂</div>
            <h2 class="text-xl font-black text-slate-300 uppercase italic tracking-widest">Le flux est vide</h2>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import Sidebar from '@/components/users/Sidebar.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const auth = useAuthStore()
const taches = ref([])
const loading = ref(true)

const userRole = computed(() => auth.user?.role)

const fetchTaches = async () => {
  try {
    loading.value = true
    const res = await api.get('/taches')
    taches.value = res.data
  } catch (e) {
    console.log(e)
  } finally {
    loading.value = false
  }
}

const statusClass = (status) => {
  switch (status) {
    case 'terminée': return 'text-emerald-500'
    case 'en cours': return 'text-blue-500'
    case 'en attente': return 'text-orange-500'
    default: return 'text-slate-400'
  }
}

const statusBorder = (status) => {
  switch (status) {
    case 'terminée': return 'bg-emerald-500'
    case 'en cours': return 'bg-blue-500'
    case 'en attente': return 'bg-orange-500'
    default: return 'bg-slate-200'
  }
}

const formatDate = (date) => {
  return date
    ? new Date(date).toLocaleDateString('fr-FR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      })
    : 'N/A'
}

onMounted(async () => {
  if (!auth.user && auth.token) {
    await auth.fetchUser()
  }
  await fetchTaches()
})
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 6px; }
.custom-scroll::-webkit-scrollbar-track { background: transparent; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

/* Troncature propre de la description sur 2 lignes */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
