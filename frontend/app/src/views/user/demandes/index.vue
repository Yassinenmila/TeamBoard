<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-6xl mx-auto px-8 py-16">

        <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <span class="h-[2px] w-12 bg-amber-500"></span>
              <p class="text-[10px] font-black text-amber-600 uppercase tracking-[0.5em] italic">
                Requêtes & Validations
              </p>
            </div>
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-[0.85]">
              {{ userRole === 'responsable' ? 'Centre de \n Décision' : 'Suivi de mes \n Demandes' }}
            </h1>
          </div>

          <div v-if="userRole === 'membre'">
            <router-link
              to="/user/demandes/create"
              class="group bg-slate-900 text-white px-8 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-900/10 flex items-center gap-4"
            >
              <span>Nouvelle Demande</span>
              <span class="bg-white/20 w-6 h-6 rounded-full flex items-center justify-center group-hover:rotate-90 transition-transform">+</span>
            </router-link>
          </div>
        </div>

        <div v-if="loading" class="flex flex-col items-center py-20 gap-4">
          <div class="w-12 h-12 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
          <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Synchronisation des dossiers...</p>
        </div>

        <div v-else class="grid gap-6">
          <div
            v-for="demande in demandes"
            :key="demande.id"
            @click="$router.push(`/user/demandes/${demande.id}`)"
            class="group bg-white rounded-[2.5rem] border border-slate-100 p-8 hover:shadow-2xl hover:shadow-slate-200/50 transition-all duration-500 flex flex-col lg:flex-row lg:items-center gap-10 cursor-pointer relative overflow-hidden"
          >
            <div class="flex items-center gap-6 flex-1">
              <div class="w-20 h-20 bg-slate-50 rounded-[2rem] flex items-center justify-center text-3xl shadow-inner group-hover:bg-amber-50 group-hover:scale-110 transition-all duration-500">
                {{ getIcon(demande.type) }}
              </div>
              <div class="space-y-1">
                <div class="flex items-center gap-2">
                  <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest italic">{{ demande.type }}</span>
                  <span class="text-[9px] font-bold text-slate-300 uppercase italic">#DM-{{ demande.id }}</span>
                </div>
                <h3 class="text-2xl font-black text-slate-800 uppercase italic group-hover:text-amber-600 transition-colors leading-tight">
                  {{ demande.objet }}
                </h3>
                <p class="text-[11px] text-slate-400 font-bold italic" v-if="userRole === 'responsable'">
                  Demandeur : <span class="text-slate-700 underline decoration-amber-200">{{ demande.user?.name }}</span>
                </p>
              </div>
            </div>

            <div class="flex flex-col items-start lg:items-end gap-2 px-6 border-l border-slate-50">
              <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Statut actuel</p>
              <span
                :class="statusStyles(demande.status)"
                class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all"
              >
                {{ demande.status }}
              </span>
            </div>

            <div
              v-if="userRole === 'responsable' && demande.status === 'en attente' && demande.user_id !== auth.user?.id"
              class="flex gap-3"
              @click.stop
            >
              <button
                @click="updateStatus(demande.id, 'acceptee')"
                class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm flex items-center justify-center text-xl"
              >
                ✓
              </button>
              <button
                @click="updateStatus(demande.id, 'refusee')"
                class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl hover:bg-rose-600 hover:text-white transition-all shadow-sm flex items-center justify-center text-xl"
              >
                ✕
              </button>
            </div>

            <div v-else class="text-right hidden sm:block min-w-[100px]">
              <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Soumis le</p>
              <p class="text-sm font-black text-slate-900 italic">{{ formatDate(demande.created_at) }}</p>
            </div>
          </div>

          <div v-if="demandes.length === 0" class="py-32 text-center bg-white rounded-[4rem] border-4 border-dashed border-slate-50">
            <div class="text-5xl mb-4">📂</div>
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] italic">Aucun dossier à traiter pour le moment</p>
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
const demandes = ref([])
const loading = ref(true)

const userRole = computed(() => auth.user?.role)

const fetchDemandes = async () => {
  try {
    loading.value = true
    const res = await api.get('/demandes')
    demandes.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  if (!confirm(`Confirmer la décision : ${status} ?`)) return
  try {
    // Note: On ajoute un commentaire par défaut pour l'action rapide
    await api.put(`/demandes/${id}/status`, {
      status,
      commentaire_responsable: `Validé via action rapide par ${auth.user.name}`
    })

    const index = demandes.value.findIndex(d => d.id === id)
    if (index !== -1) demandes.value[index].status = status
  } catch (e) {
    alert("Erreur lors de la mise à jour")
  }
}

const statusStyles = (status) => {
  switch (status) {
    case 'acceptee': return 'bg-emerald-50 text-emerald-600 border-emerald-100'
    case 'refusee': return 'bg-rose-50 text-rose-600 border-rose-100'
    case 'en attente': return 'bg-amber-50 text-amber-600 border-amber-100 animate-pulse'
    default: return 'bg-slate-50 text-slate-400 border-slate-100'
  }
}

const getIcon = (type) => {
  const icons = {
    'conge': '🌴',
    'materiel': '💻',
    'acces': '🔑',
    'formation': '📚',
    'remboursement': '💰'
  }
  return icons[type?.toLowerCase()] || '📄'
}

const formatDate = (d) => {
  if (!d) return '--'
  return new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(async () => {
  await fetchDemandes()
})
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
</style>
