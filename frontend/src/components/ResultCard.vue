<script setup lang="ts">
import { Zap, Euro, Activity, CheckCircle2, Award } from 'lucide-vue-next';
import type { CalculateResponse } from '../types/energy';

defineProps<{
  result: CalculateResponse;
}>();
</script>

<template>
  <div class="glass-card electric-border-glow p-6 sm:p-8 relative overflow-hidden transition-all duration-500">
    <!-- Ambient Energy Glow Background -->
    <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-cyan-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center space-x-3">
        <div class="p-2.5 rounded-xl bg-gradient-to-tr from-cyan-500/20 to-sky-500/20 text-cyan-400 border border-cyan-500/30">
          <Award class="w-6 h-6" />
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-100">Resultado del Cálculo</h2>
          <p class="text-xs text-slate-400">Precio ponderado y acumulados del periodo</p>
        </div>
      </div>

      <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
        <CheckCircle2 class="w-3.5 h-3.5" />
        <span>Cálculo Exitoso</span>
      </span>
    </div>

    <!-- Main Metric: Precio Indexado -->
    <div class="mb-8 p-6 rounded-2xl bg-gradient-to-br from-slate-900/90 to-slate-950/90 border border-cyan-500/30 shadow-[inset_0_0_20px_rgba(14,165,233,0.1)] text-center relative overflow-hidden">
      <span class="text-xs font-semibold uppercase tracking-wider text-cyan-400 block mb-1">
        Precio Indexado Calculado
      </span>

      <div class="flex items-baseline justify-center space-x-2">
        <span class="text-4xl sm:text-5xl font-black tracking-tight bg-gradient-to-r from-cyan-300 via-sky-400 to-blue-400 bg-clip-text text-transparent glowing-text-cyan">
          {{ result.price_indexed.toFixed(4) }}
        </span>
        <span class="text-xl font-bold text-slate-300">€/kWh</span>
      </div>

      <p class="text-xs text-slate-400 mt-2">
        Resultado ponderado según fórmula y consumo horario
      </p>
    </div>

    <!-- Secondary Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      
      <!-- Total Importes -->
      <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center space-x-4">
        <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
          <Euro class="w-6 h-6" />
        </div>
        <div>
          <span class="text-xs font-medium text-slate-400 block">Total Importes Acumulados</span>
          <span class="text-xl font-extrabold text-slate-100">
            {{ result.total_importes.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }} €
          </span>
        </div>
      </div>

      <!-- Total Consumos -->
      <div class="p-4 rounded-xl bg-slate-950/70 border border-slate-800 flex items-center space-x-4">
        <div class="p-3 rounded-xl bg-sky-500/10 text-sky-400 border border-sky-500/20">
          <Activity class="w-6 h-6" />
        </div>
        <div>
          <span class="text-xs font-medium text-slate-400 block">Total Consumo Energético</span>
          <span class="text-xl font-extrabold text-slate-100">
            {{ result.total_consumos.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 4 }) }} kWh
          </span>
        </div>
      </div>

    </div>
  </div>
</template>
