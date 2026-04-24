<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-4xl mx-auto px-8 py-16">

        <div class="mb-12 flex items-center justify-between">
          <button
            @click="$router.back()"
            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-amber-600 transition-colors flex items-center gap-2"
          >
            ← Retour à la liste
          </button>

          <router-link
            v-if="isOwner && demande.status === 'en attente'"
            :to="`/user/demandes/${demande.id}/edit`"
            class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg"
          >
            Modifier ma demande
          </router-link>
        </div>

        <div v-if="loading" class="py-20 text-center animate-pulse">
          <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Analyse du dossier...</p>
        </div>

        <div v-else class="space-y-8">

          <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="relative z-10">
              <div class="flex items-center gap-4 mb-6">
                <span :class="statusBadge(demande.status)" class="px-4 py-1.5 text-[9px] font-black uppercase tracking-widest rounded-full italic">
                  {{ demande.status }}
                </span>
                <span class="text-[10px] font-black text-slate-300 uppercase italic">Référence #DM-{{ demande.id }}</span>
              </div>

              <h1 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic leading-tight mb-4">
                {{ demande.objet }}
              </h1>

              <div class="flex items-center gap-2 mb-8">
                <span class="text-[10px] font-black px-3 py-1 bg-slate-100 text-slate-500 rounded-lg uppercase tracking-widest">
                  Type: {{ demande.type }}
                </span>
              </div>

              <div class="bg-slate-50/50 p-8 rounded-[2rem] border border-slate-50 italic text-slate-600 font-medium leading-relaxed mb-8">
                {{ demande.description }}
              </div>

              <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                <div class="flex items-center gap-4">
                  <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-black italic">
                    {{ demande.user?.first_name?.[0] }}{{ demande.user?.last_name?.[0] }}
                  </div>
                  <div>
                    <p class="text-[10px] font-black text-slate-900 uppercase italic">{{ demande.user?.first_name }} {{ demande.user?.last_name }}</p>
                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Émetteur du dossier</p>
                  </div>
                </div>
                <p class="text-[9px] font-black text-slate-300 uppercase italic tracking-widest">
                  Soumis le {{ formatDate(demande.created_at) }}
                </p>
              </div>
            </div>
          </div>

          <div v-if="demande.status !== 'pending'" class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm border-l-4" :class="demande.status === 'accepted' ? 'border-l-emerald-500' : 'border-l-rose-500'">
            <div class="flex items-start gap-6">
              <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" :class="demande.status === 'accepted' ? 'bg-emerald-50' : 'bg-rose-50'">
                <span class="text-xl">{{ demande.status === 'accepted' ? '✅' : '❌' }}</span>
              </div>
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Commentaire de validation</p>
                <p class="text-sm font-bold text-slate-700 italic leading-relaxed">
                  " {{ demande.commentaires[0].contenu|| 'Aucun motif renseigné.' }} "
                </p>
              </div>
            </div>
          </div>

          <div v-if="canEvaluate" class="bg-slate-900 rounded-[3rem] p-10 shadow-2xl space-y-8">
            <div class="flex items-center justify-between border-b border-slate-800 pb-6">
              <div>
                <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter">Évaluation Responsable</h2>
                <p class="text-[9px] font-black text-amber-500 uppercase tracking-[0.3em]">Décision administrative requise</p>
              </div>
              <span class="text-3xl">⚖️</span>
            </div>

            <div class="space-y-4">
              <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest ml-4">Motif de la décision</label>
              <textarea
                v-model="comment"
                placeholder="Expliquez pourquoi vous acceptez ou refusez cette demande..."
                class="w-full bg-slate-800/50 border-none rounded-[2rem] px-8 py-6 text-sm font-medium text-white placeholder:text-slate-600 focus:ring-2 focus:ring-amber-500/50 transition-all italic"
                rows="4"
              ></textarea>
            </div>

            <div class="flex gap-4">
              <button
                @click="processDemande('rejected')"
                :disabled="submitting || !comment.trim()"
                class="flex-1 bg-rose-500/10 text-rose-500 py-6 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-rose-500 hover:text-white transition-all disabled:opacity-20"
              >
                Refuser le dossier
              </button>
              <button
                @click="processDemande('accepted')"
                :disabled="submitting || !comment.trim()"
                class="flex-1 bg-emerald-500 text-white py-6 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-400 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-20"
              >
                Valider la demande
              </button>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Sidebar from '@/components/users/Sidebar.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const demande = ref({})
const loading = ref(true)
const submitting = ref(false)
const comment = ref('')

// --- LOGIQUE DE DROITS ---
const isOwner = computed(() => demande.value.user_id === auth.user?.id)
const isResponsable = computed(() => auth.user?.role === 'responsable')

// Peut évaluer si : est Responsable ET n'est pas l'auteur ET statut est "en attente"
const canEvaluate = computed(() => {
  return isResponsable.value && !isOwner.value && demande.value.status === 'pending'
})

const fetchDemande = async () => {
  try {
    const res = await api.get(`/demandes/${route.params.id}`)
    demande.value = res.data
  } catch (e) {
    console.error(e)
    router.push('/user/demandes')
  } finally {
    loading.value = false
  }
}

const processDemande = async (newStatus) => {
  if (!comment.value.trim()) return

  try {
    submitting.value = true
    await api.post(`/demandes/${demande.value.id}/traiter`, {
      statut: newStatus,
      message: comment.value
    })

    // Rafraîchir les données pour afficher le résultat
    await fetchDemande()
    comment.value = ''
  } catch (e) {
    alert("Une erreur est survenue lors de l'envoi de la décision.")
  } finally {
    submitting.value = false
  }
}

const statusBadge = (status) => {
  switch (status) {
    case 'acceptee': return 'bg-emerald-100 text-emerald-600'
    case 'refusee': return 'bg-rose-100 text-rose-600'
    default: return 'bg-amber-100 text-amber-600'
  }
}

const formatDate = (d) => {
  if (!d) return ''
  return new Date(d).toLocaleDateString('fr-FR', {
    day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
  })
}

onMounted(fetchDemande)
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
textarea { outline: none; resize: none; }
</style>
