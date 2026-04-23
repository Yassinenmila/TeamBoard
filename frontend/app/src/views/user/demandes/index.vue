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
            <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
              {{ userRole === 'responsable' ? 'Centre de Décision' : 'Mes Demandes' }}
            </h1>
          </div>

          <div v-if="userRole === 'membre'">
            <router-link :to="`/user/demandes/create`" class="bg-slate-900 text-white px-8 py-5 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-xl shadow-slate-900/10">
              Nouvelle Demande +
            </router-link>
          </div>
        </div>

        <div v-if="loading" class="flex justify-center py-20">
          <div class="w-10 h-10 border-4 border-amber-500 border-t-transparent rounded-full animate-spin"></div>
        </div>

        <div v-else class="grid gap-6">
          <div
            v-for="demande in demandes"
            :key="demande.id"
            class="group bg-white rounded-[2.5rem] border border-slate-100 p-8 hover:shadow-2xl transition-all duration-500 flex flex-col lg:flex-row lg:items-center gap-10"
          >
            <div class="flex items-center gap-6 flex-1">
              <div class="w-16 h-16 bg-slate-50 rounded-[1.5rem] flex items-center justify-center text-2xl shadow-inner">
                {{ getIcon(demande.type) }}
              </div>
              <div>
                <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest italic">{{ demande.type }}</span>
                <h3 class="text-xl font-black text-slate-800 uppercase italic">{{ demande.objet }}</h3>
                <p class="text-xs text-slate-400 font-bold italic" v-if="userRole === 'responsable'">
                  Par : {{ demande.user?.name }}
                </p>
              </div>
            </div>

            <div class="flex flex-col items-center lg:items-end gap-2">
              <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Statut actuel</p>
              <span
                :class="statusStyles(demande.status)"
                class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border"
              >
                {{ demande.status }}
              </span>
            </div>

            <div v-if="userRole === 'responsable' && demande.status === 'en attente'" class="flex gap-3">
              <button
                @click="updateStatus(demande.id, 'acceptee')"
                class="p-4 bg-emerald-50 text-emerald-600 rounded-2xl hover:bg-emerald-600 hover:text-white transition-all shadow-sm"
                title="Accepter"
              >
                ✅
              </button>
              <button
                @click="updateStatus(demande.id, 'refusee')"
                class="p-4 bg-rose-50 text-rose-600 rounded-2xl hover:bg-rose-600 hover:text-white transition-all shadow-sm"
                title="Refuser"
              >
                ❌
              </button>
            </div>

            <div v-else class="text-right hidden sm:block">
              <p class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Soumis le</p>
              <p class="text-sm font-black text-slate-900 italic">{{ formatDate(demande.created_at) }}</p>
            </div>
          </div>

          <div v-if="demandes.length === 0" class="py-32 text-center bg-white rounded-[4rem] border-4 border-dashed border-slate-50">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em] italic">Aucun dossier en cours</p>
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
    // L'API doit retourner les demandes du membre OU toutes les demandes si responsable
    const res = await api.get('/demandes')
    demandes.value = res.data
  } catch (e) {
    console.error(e)
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  try {
    await api.put(`/demandes/${id}/status`, { status })
    // On met à jour localement pour la réactivité
    const index = demandes.value.findIndex(d => d.id === id)
    if (index !== -1) demandes.value[index].status = status
  } catch (e) {
    alert("Erreur lors de la validation")
  }
}

// Helpers Design
const statusStyles = (status) => {
  switch (status) {
    case 'acceptee': return 'bg-emerald-50 text-emerald-600 border-emerald-100'
    case 'refusee': return 'bg-rose-50 text-rose-600 border-rose-100'
    default: return 'bg-amber-50 text-amber-600 border-amber-100'
  }
}

const getIcon = (type) => {
  const icons = { 'conge': '🌴', 'materiel': '💻', 'acces': '🔑' }
  return icons[type?.toLowerCase()] || '📄'
}

const formatDate = (d) => new Date(d).toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' })

onMounted(async () => {
  if (!auth.user && auth.token) await auth.fetchUser()
  await fetchDemandes()
})
</script>
