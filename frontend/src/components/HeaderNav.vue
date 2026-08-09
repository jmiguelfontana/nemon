<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Zap, Activity, BookOpen, Database, RefreshCw } from 'lucide-vue-next';
import { checkApiHealth } from '../services/api';

const isOnline = ref<boolean | null>(null);
const isChecking = ref<boolean>(false);

async function verifyStatus() {
  isChecking.value = true;
  isOnline.value = await checkApiHealth();
  isChecking.value = false;
}

onMounted(() => {
  verifyStatus();
});
</script>

<template>
  <header class="sticky top-0 z-50 backdrop-blur-md bg-slate-950/80 border-b border-slate-800/80 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Brand Logo & Title -->
      <div class="flex items-center space-x-3">
        <div class="relative flex items-center justify-center w-11 h-11 rounded-xl bg-gradient-to-tr from-sky-500 via-cyan-400 to-blue-600 shadow-[0_0_20px_rgba(14,165,233,0.5)]">
          <Zap class="w-6 h-6 text-slate-950 stroke-[2.5]" />
          <span class="absolute -top-1 -right-1 flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-cyan-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-cyan-400"></span>
          </span>
        </div>
        <div>
          <div class="flex items-center space-x-2">
            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-white via-slate-100 to-sky-300 bg-clip-text text-transparent">
              NEMON
            </span>
            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/20">
              Energy Tech
            </span>
          </div>
          <p class="text-xs text-slate-400 font-medium">Calculadora de Precio Indexado OMIE_MD</p>
        </div>
      </div>

      <!-- Actions & Status Badges -->
      <div class="flex items-center space-x-4">
        
        <!-- Swagger Doc Link -->
        <a 
          href="/api/documentation" 
          target="_blank" 
          rel="noopener noreferrer"
          class="hidden sm:flex items-center space-x-2 text-xs font-semibold px-3.5 py-2 rounded-lg bg-slate-900 text-slate-300 hover:text-sky-400 border border-slate-800 hover:border-sky-500/30 transition-all"
        >
          <BookOpen class="w-4 h-4 text-sky-400" />
          <span>Swagger API Docs</span>
        </a>

        <!-- API Health Badge -->
        <div 
          @click="verifyStatus"
          class="flex items-center space-x-2 text-xs font-medium px-3.5 py-1.5 rounded-full bg-slate-900/90 border cursor-pointer transition-all duration-300"
          :class="isOnline ? 'border-emerald-500/30 text-emerald-400 bg-emerald-500/5' : isOnline === false ? 'border-rose-500/30 text-rose-400 bg-rose-500/5' : 'border-slate-800 text-slate-400'"
          title="Haz clic para comprobar estado de API REST"
        >
          <span class="relative flex h-2.5 w-2.5">
            <span 
              class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75"
              :class="isOnline ? 'bg-emerald-400' : isOnline === false ? 'bg-rose-400' : 'bg-amber-400'"
            ></span>
            <span 
              class="relative inline-flex rounded-full h-2.5 w-2.5"
              :class="isOnline ? 'bg-emerald-400' : isOnline === false ? 'bg-rose-400' : 'bg-amber-400'"
            ></span>
          </span>
          
          <span class="font-semibold">
            {{ isChecking ? 'Comprobando...' : isOnline ? 'API Conectada (PHP 8.4)' : isOnline === false ? 'API Desconectada' : 'Verificando...' }}
          </span>

          <RefreshCw class="w-3.5 h-3.5 ml-1 opacity-70" :class="{ 'animate-spin': isChecking }" />
        </div>

      </div>

    </div>
  </header>
</template>
