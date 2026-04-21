<template>
  <div class="h-screen w-full bg-[#fcfdfd] flex items-center justify-center p-6 font-sans">
    <div class="max-w-2xl w-full bg-white rounded-[3.5rem] border border-slate-100 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.05)] p-16 text-center relative overflow-hidden">

      <div class="absolute -top-10 -right-10 text-[15rem] font-black text-slate-50 italic select-none pointer-events-none">
        403
      </div>

      <div class="relative z-10 flex flex-col items-center">
        <div class="w-20 h-20 bg-slate-900 rounded-[2rem] flex items-center justify-center mb-8 shadow-2xl shadow-slate-900/20">
          <span class="text-3xl">🔒</span>
        </div>

        <p class="text-[10px] font-black text-rose-500 uppercase tracking-[0.5em] mb-4 italic">Zone Restreinte</p>

        <h1 class="text-6xl font-black text-slate-900 tracking-tighter uppercase italic leading-none mb-6">
          Accès <span class="text-slate-300">Refusé</span>
        </h1>

        <p class="max-w-sm mx-auto text-sm text-slate-400 font-bold italic leading-relaxed mb-10">
          Désolé, vos permissions actuelles ne vous permettent pas d'entrer dans cet espace. Contactez l'administrateur si vous pensez qu'il s'agit d'une erreur.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <button
            @click="$router.back()"
            class="px-8 py-4 bg-slate-50 text-slate-900 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-slate-100 transition-all italic"
          >
            Retour en arrière
          </button>

          <router-link
            to="/admin/dashboard"
            class="px-8 py-4 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-emerald-600 transition-all shadow-lg shadow-slate-900/20 italic"
          >
            Tableau de Bord
          </router-link>
        </div>
      </div>

      <div class="absolute bottom-8 left-0 w-full text-center">
        <p class="text-[8px] font-black text-slate-200 uppercase tracking-[0.3em]">TeamBoard Security Protocol v3.0</p>
      </div>
    </div>
  </div>
</template>

<script setup>
// Pas de logique complexe nécessaire ici
</script>

<style scoped>
/* Animation d'entrée douce */
div {
  animation: fadeInScale 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInScale {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
