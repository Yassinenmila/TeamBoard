<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-6xl mx-auto px-8 py-16">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="h-[2px] w-12 bg-indigo-500"></span>
              <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.5em] italic">
                Agenda & Synchro
              </p>
            </div>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
              Briefings Équipe
            </h1>
          </div>

          <div v-if="userRole === 'responsable' || userRole === 'admin'">
            <router-link
              to="/user/reunions/create"
              class="flex items-center gap-4 bg-slate-900 text-white px-8 py-5 rounded-[2rem] hover:bg-indigo-600 transition-all duration-300 shadow-xl shadow-slate-900/10 group"
            >
              <span class="text-[10px] font-black uppercase tracking-[0.2em]">Programmer un call</span>
              <span class="text-xl group-hover:rotate-90 transition-transform duration-300">+</span>
            </router-link>
          </div>
        </div>

        <div v-if="loading" class="flex flex-col items-center justify-center py-32">
          <div class="w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="grid gap-6">

          <div
            v-for="reunion in reunions"
            :key="reunion.id"
            class="group relative bg-white rounded-[2.5rem] border border-slate-100 p-8 hover:shadow-2xl transition-all duration-500 flex flex-col lg:flex-row lg:items-center gap-8"
          >
            <div class="flex flex-col items-center justify-center bg-slate-50 rounded-[2rem] px-6 py-4 min-w-[100px]">
              <span class="text-2xl font-black text-slate-900 italic leading-none">{{ formatDay(reunion.date_reunion) }}</span>
              <span class="text-[9px] font-black text-indigo-500 uppercase italic">{{ formatMonth(reunion.date_reunion) }}</span>
            </div>

            <div class="flex-1 space-y-3">
              <div class="flex items-center gap-3">
                <span class="text-[9px] font-black px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full uppercase tracking-widest">
                  {{ reunion.heure_debut }} — {{ reunion.heure_fin }}
                </span>
                <span v-if="isUpcoming(reunion.date_reunion)" class="animate-pulse w-2 h-2 bg-emerald-500 rounded-full"></span>
              </div>

              <h3 class="text-2xl font-black text-slate-800 uppercase italic group-hover:text-indigo-600 transition-colors">
                {{ reunion.titre }}
              </h3>

              <div class="flex items-center gap-4">
                <p class="text-sm text-slate-400 font-medium italic">
                  {{ reunion.description || 'Pas de description fournie.' }}
                </p>
              </div>
            </div>

            <div class="flex items-center gap-4">
              <a
                v-if="reunion.lien_video"
                :href="reunion.lien_video"
                target="_blank"
                class="p-4 bg-indigo-500 text-white rounded-2xl hover:scale-105 transition-transform shadow-lg shadow-indigo-500/20"
              >
                <span class="text-[10px] font-black uppercase italic tracking-widest">Rejoindre ↗</span>
              </a>

              <router-link
                :to="`/user/reunions/${reunion.id}`"
                class="p-4 bg-slate-50 text-slate-400 rounded-2xl hover:bg-slate-900 hover:text-white transition-all"
              >
                <span class="text-[10px] font-black uppercase italic">Détails</span>
              </router-link>
            </div>
          </div>

          <div v-if="reunions.length === 0" class="py-32 text-center bg-white rounded-[4rem] border-4 border-dashed border-slate-50">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] italic">Aucun meeting à l'horizon</p>
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
const reunions = ref([])
const loading = ref(true)

const userRole = computed(() => auth.user?.role)

const fetchReunions = async () => {
  try {
    loading.value = true
    // Ton API doit retourner les réunions liées à l'user (pivot ou créateur)
    const res = await api.get('/reunions')
    reunions.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

// Helpers Formats
const formatDay = (d) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit' })
const formatMonth = (d) => new Date(d).toLocaleDateString('fr-FR', { month: 'short' }).replace('.', '')
const isUpcoming = (d) => new Date(d) > new Date()

onMounted(async () => {
  if (!auth.user && auth.token) await auth.fetchUser()
  await fetchReunions()
})
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 6px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
