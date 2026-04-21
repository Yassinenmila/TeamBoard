<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">
            Administration
          </p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
            Mes Demandes
          </h1>
        </div>

        <button class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl shadow-lg shadow-slate-900/10">
          Nouvelle Demande +
        </button>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30">
        <div class="max-w-7xl mx-auto">
          <p class="text-center text-slate-400 italic">Page Mes Demandes - En développement</p>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import Sidebar from '@/components/users/Sidebar.vue'
</script>
