<template>
  <div class="flex min-h-screen bg-[#f8fafb] text-slate-900">
    <Sidebar />

    <main class="flex-1 overflow-y-auto custom-scroll">
      <div class="max-w-4xl mx-auto px-8 py-16">

        <div class="mb-12">
          <button
            @click="$router.back()"
            class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] hover:text-indigo-600 transition-colors mb-8 block"
          >
            ← Annuler les modifications
          </button>

          <div class="flex items-center gap-3 mb-4">
            <span class="h-[2px] w-12 bg-indigo-500"></span>
            <p class="text-[10px] font-black text-indigo-600 uppercase tracking-[0.5em] italic">
              Édition de contenu
            </p>
          </div>
          <h1 class="text-5xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">
            Modifier <br/> l'Annonce
          </h1>
        </div>

        <div v-if="loading" class="py-20 text-center animate-pulse">
          <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.4em]">Chargement des données...</p>
        </div>

        <form v-else @submit.prevent="updateAnnonce" class="space-y-8">

          <div class="bg-white rounded-[2.5rem] p-10 border border-slate-100 shadow-sm space-y-6">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Titre de l'annonce</label>
              <input
                v-model="form.titre"
                type="text"
                placeholder="Ex: Mise à jour des protocoles..."
                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-5 text-sm font-bold text-slate-900 placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                required
              />
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-4">Contenu détaillé</label>
              <textarea
                v-model="form.description"
                rows="8"
                placeholder="Décrivez votre annonce ici..."
                class="w-full bg-slate-50 border-none rounded-[2rem] px-6 py-6 text-sm font-medium text-slate-600 placeholder:text-slate-300 focus:ring-2 focus:ring-indigo-500/20 transition-all italic leading-relaxed"
                required
              ></textarea>
            </div>
          </div>

          <div class="flex items-center justify-end gap-6">
            <button
              type="button"
              @click="$router.back()"
              class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors"
            >
              Ignorer
            </button>

            <button
              type="submit"
              :disabled="submitting"
              class="bg-slate-900 text-white px-10 py-5 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-xl shadow-slate-900/10 flex items-center gap-3"
            >
              <span v-if="submitting">Mise à jour...</span>
              <span v-else>Enregistrer les modifications</span>
              <span v-if="!submitting">✓</span>
            </button>
          </div>
        </form>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Sidebar from '@/components/users/Sidebar.vue'
import api from '@/services/api'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const auth = useAuthStore()

const loading = ref(true)
const submitting = ref(false)

const form = ref({
  titre: '',
  description: ''
})

// Charger les données actuelles
const fetchAnnonce = async () => {
  try {
    const res = await api.get(`/annonces/${route.params.id}`)

    // SÉCURITÉ : Vérifier si l'utilisateur est le propriétaire
    if (res.data.user_id !== auth.user.id) {
      alert("Accès non autorisé : Vous n'êtes pas l'auteur de cette annonce.")
      router.push('/user/annances')
      return
    }

    form.value.titre = res.data.titre
    form.value.description = res.data.description
  } catch (e) {
    console.error(e)
    router.push('/user/annances')
  } finally {
    loading.value = false
  }
}

// Envoyer les modifications
const updateAnnonce = async () => {
  try {
    submitting.value = true
    await api.put(`/annonces/${route.params.id}`, form.value)

    // Feedback visuel et redirection
    router.push(`/user/annances/${route.params.id}`)
  } catch (e) {
    console.error(e)
    alert("Une erreur est survenue lors de la modification.")
  } finally {
    submitting.value = false
  }
}

onMounted(fetchAnnonce)
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 5px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

input, textarea {
  outline: none;
}
</style>
