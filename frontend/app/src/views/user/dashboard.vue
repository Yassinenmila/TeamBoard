<template>
  <div class="flex min-h-screen text-slate-900 bg-[#fcfdfd]">
    <Sidebar />

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
      <header class="h-24 px-12 flex items-center justify-between shrink-0 bg-white border-b border-slate-100">
        <div>
          <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.4em] mb-1 italic">
            {{ user.role === 'responsable' ? 'Management Unit' : 'Workstation' }}
          </p>
          <h1 class="text-2xl font-black text-slate-900 tracking-tighter uppercase italic">
            {{ user.role === 'responsable' ? 'Supervision Projets' : 'Mon Activité' }}
          </h1>
        </div>

        <div class="flex items-center gap-4">
          <span class="text-[9px] font-black text-slate-300 uppercase tracking-widest italic bg-slate-50 px-4 py-2 rounded-full border border-slate-100">
            Sprint #04 — 2026
          </span>
          <button v-if="user.role === 'responsable'" class="bg-slate-900 text-white text-[10px] font-black uppercase tracking-[0.2em] px-8 py-4 rounded-xl shadow-lg hover:bg-emerald-600 transition-all">
            Assigner Mission +
          </button>
        </div>
      </header>

      <div class="flex-1 overflow-y-auto p-12 bg-slate-50/30 custom-scroll">
        <div class="max-w-7xl mx-auto grid grid-cols-12 gap-6">

          <div class="col-span-12 grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm group hover:border-emerald-500 transition-colors">
              <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-2 italic">
                {{ user.role === 'responsable' ? 'Missions Équipe' : 'Mes Tâches' }}
              </p>
              <p class="text-4xl font-black text-slate-900 tracking-tighter italic group-hover:text-emerald-500">14</p>
            </div>
            <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
              <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest mb-2 italic">Temps Passé</p>
              <p class="text-4xl font-black text-slate-900 tracking-tighter italic">32h</p>
            </div>
            <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl shadow-slate-900/10">
              <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-2 italic">Deadline Proche</p>
              <p class="text-4xl font-black text-white tracking-tighter italic">02</p>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-8 space-y-6">
            <div v-if="user.role === 'responsable'" class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
              <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 italic">Charge de travail par membre</h3>
              <div class="space-y-6">
                <div v-for="member in ['Ahmed', 'Sanae', 'Yassine']" :key="member" class="space-y-2">
                  <div class="flex justify-between text-[10px] font-black uppercase italic">
                    <span class="text-slate-600">{{ member }}</span>
                    <span class="text-emerald-600">80%</span>
                  </div>
                  <div class="w-full bg-slate-50 h-3 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full w-[80%] rounded-full shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
              <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-8 italic">Missions Prioritaires</h3>
              <div class="space-y-4">
                <div v-for="i in 3" :key="i" class="p-6 bg-slate-50/50 rounded-3xl border border-slate-50 hover:bg-white hover:shadow-xl transition-all group flex items-center justify-between">
                  <div>
                    <h4 class="text-lg font-black uppercase tracking-tighter italic group-hover:text-emerald-600">Refactoring API Logic</h4>
                    <p class="text-[9px] font-bold text-slate-400 uppercase italic mt-1 tracking-widest">Sprint #04 — Backend</p>
                  </div>
                  <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center border border-slate-100">
                    <span class="text-xs">⚡</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-span-12 lg:col-span-4 space-y-6">
            <div class="bg-emerald-500 p-10 rounded-[3rem] text-white shadow-xl shadow-emerald-500/10 relative overflow-hidden group">
              <div class="relative z-10">
                <p class="text-[9px] font-black uppercase tracking-widest mb-4 opacity-80 italic">Prochaine Session</p>
                <p class="text-4xl font-black tracking-tighter italic mb-1">16:00</p>
                <p class="text-[11px] font-bold italic opacity-90 mb-6 tracking-wide">Daily Scrum Meeting</p>
                <button class="w-full py-4 bg-white text-emerald-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl shadow-lg hover:scale-[1.02] transition-transform">
                  Ouvrir Meet
                </button>
              </div>
              <div class="absolute -right-6 -bottom-6 text-9xl font-black text-white/10 italic group-hover:rotate-12 transition-transform duration-700">MEET</div>
            </div>

            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm border-l-4 border-l-slate-900">
              <h3 class="text-[10px] font-black text-slate-300 uppercase tracking-widest mb-4 italic italic">Dernière Note</h3>
              <p class="text-[11px] text-slate-500 font-bold italic leading-relaxed">
                "N'oubliez pas de soumettre vos rapports de sprints avant ce soir 18h."
              </p>
            </div>
          </div>

        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import Sidebar from '@/components/users/Sidebar.vue';

// Simulation (Change 'user' par 'responsable' pour voir la différence)
const user = ref({
  role: 'user',
  name: 'Yassine B.'
});
</script>

<style scoped>
.custom-scroll::-webkit-scrollbar { width: 4px; }
.custom-scroll::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
