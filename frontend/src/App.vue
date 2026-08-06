<script setup lang="ts">
import { ref } from 'vue';
import HeaderNav from './components/HeaderNav.vue';
import CalculatorForm from './components/CalculatorForm.vue';
import ResultCard from './components/ResultCard.vue';
import ErrorMessage from './components/ErrorMessage.vue';
import DataTable from './components/DataTable.vue';
import { calculateEnergy } from './services/api';
import type { CalculateRequest, CalculateResponse } from './types/energy';
import { Zap, ShieldCheck, Sparkles } from 'lucide-vue-next';

const loading = ref<boolean>(false);
const result = ref<CalculateResponse | null>(null);
const error = ref<{
  status?: number;
  message: string;
  details?: Record<string, string[]>;
} | null>(null);

async function handleCalculate(payload: CalculateRequest) {
  loading.value = true;
  error.value = null;
  result.value = null;

  try {
    const res = await calculateEnergy(payload);
    result.value = res;
  } catch (err: any) {
    if (err.response) {
      error.value = {
        status: err.response.status,
        message: err.response.data?.error || err.response.data?.message || 'Error en la solicitud.',
        details: err.response.data?.details,
      };
    } else if (err.request) {
      error.value = {
        status: 0,
        message: 'No se pudo establecer conexión con el servidor backend en http://localhost:8000. Asegúrate de ejecutar php artisan serve.',
      };
    } else {
      error.value = {
        message: err.message || 'Ocurrió un error inesperado al procesar la solicitud.',
      };
    }
  } finally {
    loading.value = false;
  }
}

function clearError() {
  error.value = null;
}
</script>

<template>
  <div class="min-h-screen bg-slate-950 text-slate-100 font-sans relative overflow-x-hidden selection:bg-cyan-500 selection:text-slate-950">
    
    <!-- Background Ambient Glow Effects -->
    <div class="fixed top-0 left-1/4 w-[600px] h-[600px] bg-sky-600/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="fixed bottom-0 right-1/4 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>

    <!-- Header Navigation Bar -->
    <HeaderNav />

    <!-- Main Content Layout -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-10">
      
      <!-- Hero Banner -->
      <section class="text-center max-w-3xl mx-auto space-y-3">
        <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-gradient-to-r from-sky-500/10 to-cyan-500/10 border border-sky-500/20 text-sky-300 text-xs font-semibold">
          <Zap class="w-3.5 h-3.5 text-cyan-400" />
          <span>Plataforma Energética de Indexación OMIE</span>
        </div>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black tracking-tight bg-gradient-to-r from-white via-slate-100 to-sky-200 bg-clip-text text-transparent">
          Cálculo Inteligente de Precio Indexado
        </h1>
        <p class="text-sm sm:text-base text-slate-400 font-normal leading-relaxed">
          Simula y evalúa fórmulas personalizadas sobre los consumos y precios horarios del mercado mayorista eléctrico OMIE_MD.
        </p>
      </section>

      <!-- Calculator Form & Results Grid -->
      <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Form Component (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
          <CalculatorForm 
            :loading="loading" 
            @submit="handleCalculate" 
          />

          <!-- Error Alert Banner -->
          <ErrorMessage 
            :error="error" 
            @close="clearError" 
          />
        </div>

        <!-- Right: Results Card Component (5 cols) -->
        <div class="lg:col-span-5">
          <ResultCard 
            v-if="result" 
            :result="result" 
          />

          <!-- Placeholder when no result yet -->
          <div 
            v-else 
            class="glass-card p-8 text-center border-dashed border-slate-800/80 flex flex-col items-center justify-center min-h-[380px] space-y-4"
          >
            <div class="w-16 h-16 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-500">
              <Sparkles class="w-8 h-8 text-sky-500/50" />
            </div>
            <div>
              <h3 class="text-base font-bold text-slate-200">Esperando Parámetros</h3>
              <p class="text-xs text-slate-400 max-w-xs mx-auto mt-1">
                Ingresa el rango de fechas y la fórmula arriba para ver el precio indexado y acumulados en tiempo real.
              </p>
            </div>
          </div>
        </div>

      </section>

      <!-- Data Table Component -->
      <section>
        <DataTable />
      </section>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-900 py-8 mt-16 bg-slate-950/60 text-xs text-slate-500 text-center">
      <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p>© 2025 NEMON Energy Tech. Prueba Técnica de Cálculo Indexado.</p>
        <div class="flex items-center space-x-4">
          <span class="inline-flex items-center space-x-1 text-slate-400">
            <ShieldCheck class="w-4 h-4 text-emerald-400" />
            <span>PHP 8.4.4 / Laravel API</span>
          </span>
          <span class="text-slate-600">•</span>
          <span class="text-slate-400">Vue 3 + TypeScript</span>
        </div>
      </div>
    </footer>

  </div>
</template>
