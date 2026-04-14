<template>
  <div class="bg-white min-h-screen flex text-slate-900">

    <!-- LEFT -->
    <div class="hidden lg:flex lg:w-7/12 bg-image-section p-16 flex-col justify-between relative">

      <div class="relative z-10">
        <span class="text-3xl font-extrabold text-white italic uppercase">
          Team<span class="text-emerald-400">Board</span>
        </span>
      </div>

      <div class="relative z-10">
        <h2 class="text-5xl font-extrabold text-white leading-tight">
          L'excellence <br>est un travail <br>
          <span class="text-emerald-400">collectif.</span>
        </h2>
      </div>

      <div class="relative z-10 text-xs text-emerald-100/50 uppercase">
        Secure Enterprise Infrastructure
      </div>

    </div>

    <!-- RIGHT -->
    <div class="w-full lg:w-5/12 flex items-center justify-center p-10">

      <div class="w-full max-w-md">

        <h1 class="text-4xl font-bold mb-2">Bonjour</h1>
        <p class="text-slate-500 mb-8">Connecte-toi à ton espace</p>

        <form @submit.prevent="login" class="space-y-5">

          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="w-full p-4 bg-slate-50 border rounded-xl"
          />

          <input
            v-model="password"
            type="password"
            placeholder="Mot de passe"
            class="w-full p-4 bg-slate-50 border rounded-xl"
          />

          <button
            type="submit"
            class="w-full bg-slate-900 text-white p-4 rounded-xl hover:bg-emerald-600 transition"
          >
            Connexion
          </button>

        </form>

        <p v-if="error" class="text-red-500 mt-4 text-sm">
          {{ error }}
        </p>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref } from "vue"
import { useAuthStore } from "@/stores/auth"
import { useRouter } from "vue-router"

const email = ref("")
const password = ref("")
const error = ref(null)
const router = useRouter()

const auth = useAuthStore()

async function login() {
  try {
    console.log("LOGIN START") // 👈 ajoute ça
    error.value = null

    await auth.login(email.value, password.value)

    console.log("✅ Connecté :", auth.user)

    router.push("/")

  } catch (err) {
    error.value = "Email ou mot de passe incorrect"
  }
}
</script>

<style scoped>
.bg-image-section {
  background-image: linear-gradient(
    to bottom,
    rgba(6, 78, 59, 0.8),
    rgba(15, 23, 42, 0.85)
  ),
  url("https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&q=80&w=1200");
  background-size: cover;
  background-position: center;
}
</style>
