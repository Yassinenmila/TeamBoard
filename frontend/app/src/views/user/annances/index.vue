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
                Communication & Flux
              </p>
            </div>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-[0.85]">
              Tableau des <br/> Annonces
            </h1>
          </div>

          <div v-if="userRole === 'responsable'">
            <router-link
              to="/user/annances/create"
              class="group bg-slate-900 text-white px-8 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-4"
            >
              <span>Diffuser une annonce</span>
              <span class="bg-white/20 w-6 h-6 rounded-full flex items-center justify-center group-hover:rotate-90 transition-transform">+</span>
            </router-link>
          </div>
        </div>

        <div v-if="loading" class="py-20 text-center animate-pulse">
          <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Chargement du flux...</p>
        </div>

        <div v-else class="grid grid-cols-1 gap-8">
          <div
            v-for="annonce in annonces"
            :key="annonce.id"
            @click="$router.push(`/user/annances/${annonce.id}`)"
            :class="[
              'group bg-white rounded-[2.5rem] border p-8 hover:shadow-2xl transition-all duration-500 relative cursor-pointer',
              annonce.status === 'urgent' ? 'border-amber-200 shadow-lg shadow-amber-500/5' : 'border-slate-100'
            ]"
          >
            <div v-if="annonce.status === 'urgent'" class="absolute top-8 right-8 flex items-center gap-2">
              <span class="w-2 h-2 bg-rose-500 rounded-full animate-ping"></span>
              <span class="text-[9px] font-black text-rose-500 uppercase tracking-widest italic">Priorité Haute</span>
            </div>

            <div class="flex flex-col lg:flex-row justify-between gap-8">

              <div class="flex-1 space-y-5">
                <div class="flex items-center gap-4">
                  <span
                    :class="[
                      'text-[8px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest italic',
                      annonce.type === 'urgent' ? 'bg-rose-100 text-rose-600' : 'bg-indigo-50 text-indigo-600'
                    ]"
                  >
                    {{ annonce.type }}
                  </span>

                  <span class="text-[9px] font-black text-slate-300 uppercase italic">
                    {{ formatDate(annonce.created_at) }}
                  </span>
                </div>

                <div>
                  <h3 class="text-3xl font-black text-slate-800 uppercase italic group-hover:text-indigo-600 transition-colors leading-tight mb-2">
                    {{ annonce.titre }}
                  </h3>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                    Par <span class="text-slate-900 underline decoration-indigo-200">{{ annonce.user?.first_name }} {{ annonce.user?.last_name }}</span>
                  </p>
                </div>

                <p class="text-base text-slate-500 font-medium leading-relaxed italic whitespace-pre-line max-w-4xl line-clamp-3">
                  {{ annonce.description }}
                </p>
              </div>

              <div v-if="canManage(annonce)" class="flex lg:flex-col items-center justify-center gap-3 lg:border-l lg:border-slate-50 lg:pl-8 min-w-[150px]" @click.stop>
                <router-link
                  :to="`/user/annances/${annonce.id}/edit`"
                  class="w-full text-center px-6 py-3 bg-slate-50 hover:bg-slate-900 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                >
                  Modifier
                </router-link>

                <button
                  @click="deleteAnnonce(annonce.id)"
                  class="w-full text-center px-6 py-3 text-rose-500 hover:bg-rose-50 rounded-xl text-[9px] font-black uppercase tracking-widest transition-all"
                >
                  Supprimer
                </button>
              </div>

            </div>
          </div>

          <div v-if="annonces.length === 0" class="py-32 text-center bg-white rounded-[4rem] border-4 border-dashed border-slate-50">
            <div class="text-4xl mb-4 opacity-20">📭</div>
            <h2 class="text-xl font-black text-slate-200 uppercase italic tracking-widest">Le flux est vide</h2>
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
const annonces = ref([])
const loading = ref(true)

const userRole = computed(() => auth.user?.role)

const canManage = (annonce) => {
  return (userRole.value === 'responsable' || userRole.value === 'admin') && annonce.user_id === auth.user?.id
}

const fetchAnnonces = async () => {
  try {
    loading.value = true
    const res = await api.get('/annonces')
    // Tri optionnel : les urgentes en haut
    annonces.value = res.data.sort((a, b) => {
      if (a.status === 'urgent' && b.status !== 'urgent') return -1
      return 0
    })
  } catch (e) {
    console.error("Erreur flux:", e)
  } finally {
    loading.value = false
  }
}

const deleteAnnonce = async (id) => {
  if (!confirm('Retirer définitivement cette annonce du tableau ?')) return
  try {
    await api.delete(`/annonces/${id}`)
    annonces.value = annonces.value.filter(a => a.id !== id)
  } catch (e) {
    alert("Erreur lors de la suppression")
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('fr-FR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  })
}

onMounted(fetchAnnonces)
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
