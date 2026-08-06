<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { Database, Activity, Euro, Search, RefreshCw, ChevronLeft, ChevronRight, Filter, Calendar } from 'lucide-vue-next';
import { getConsumptions, getPrices } from '../services/api';
import type { ConsumptionRecord, PriceRecord } from '../types/energy';

const activeTab = ref<'consumptions' | 'prices'>('consumptions');
const consumptions = ref<ConsumptionRecord[]>([]);
const prices = ref<PriceRecord[]>([]);
const loading = ref<boolean>(false);
const error = ref<string | null>(null);

const startDateFilter = ref<string>('2025-03-01');
const endDateFilter = ref<string>('2025-03-31');

// Pagination state
const currentPage = ref<number>(1);
const itemsPerPage = ref<number>(10);

async function fetchData() {
  loading.value = true;
  error.value = null;

  try {
    if (activeTab.value === 'consumptions') {
      consumptions.value = await getConsumptions(startDateFilter.value, endDateFilter.value);
    } else {
      prices.value = await getPrices(startDateFilter.value, endDateFilter.value);
    }
  } catch (err: any) {
    error.value = 'No se pudieron cargar los datos desde la API REST backend.';
  } finally {
    loading.value = false;
  }
}

watch(activeTab, () => {
  currentPage.value = 1;
  fetchData();
});

onMounted(() => {
  fetchData();
});

function applyFilter() {
  currentPage.value = 1;
  fetchData();
}

function resetFilter() {
  startDateFilter.value = '';
  endDateFilter.value = '';
  currentPage.value = 1;
  fetchData();
}

const activeData = computed(() => {
  return activeTab.value === 'consumptions' ? consumptions.value : prices.value;
});

const totalPages = computed(() => {
  return Math.ceil(activeData.value.length / itemsPerPage.value) || 1;
});

const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  return activeData.value.slice(start, start + itemsPerPage.value);
});

function getHeatmapClass(value: number | string | null, type: 'consumptions' | 'prices'): string {
  if (value === null || value === undefined) return 'text-slate-600 bg-slate-950/40';
  const num = typeof value === 'string' ? parseFloat(value) : value;

  if (type === 'consumptions') {
    if (num > 1.5) return 'text-amber-300 font-bold bg-amber-400/10 border-amber-400/20';
    if (num > 0.8) return 'text-sky-300 bg-sky-500/10';
    return 'text-slate-300';
  } else {
    if (num > 0.09) return 'text-amber-300 font-bold bg-amber-400/15';
    if (num > 0.06) return 'text-cyan-300 bg-cyan-400/10';
    return 'text-slate-300';
  }
}
</script>

<template>
  <div class="glass-card p-6 sm:p-8">
    
    <!-- Table Header & Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
      
      <!-- Title -->
      <div class="flex items-center space-x-3">
        <div class="p-2.5 rounded-xl bg-slate-800 text-sky-400 border border-slate-700">
          <Database class="w-6 h-6" />
        </div>
        <div>
          <h2 class="text-lg font-bold text-slate-100">Registros Horarios de Base de Datos</h2>
          <p class="text-xs text-slate-400">Consulta los datos almacenados de consumos (kWh) y precios OMIE (€/kWh)</p>
        </div>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex items-center p-1 bg-slate-950/90 rounded-xl border border-slate-800 self-start md:self-auto">
        <button
          @click="activeTab = 'consumptions'"
          class="flex items-center space-x-2 text-xs font-semibold px-4 py-2 rounded-lg transition-all"
          :class="activeTab === 'consumptions' ? 'bg-sky-500 text-slate-950 shadow-md' : 'text-slate-400 hover:text-slate-200'"
        >
          <Activity class="w-4 h-4" />
          <span>Consumos (kWh)</span>
        </button>

        <button
          @click="activeTab = 'prices'"
          class="flex items-center space-x-2 text-xs font-semibold px-4 py-2 rounded-lg transition-all"
          :class="activeTab === 'prices' ? 'bg-cyan-400 text-slate-950 shadow-md' : 'text-slate-400 hover:text-slate-200'"
        >
          <Euro class="w-4 h-4" />
          <span>Precios OMIE (€/kWh)</span>
        </button>
      </div>

    </div>

    <!-- Date Filters Bar -->
    <div class="mb-6 p-4 rounded-xl bg-slate-950/60 border border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
      <div class="flex flex-wrap items-center gap-3">
        <div class="flex items-center space-x-2 text-xs text-slate-300">
          <Filter class="w-3.5 h-3.5 text-sky-400" />
          <span>Filtrar rango:</span>
        </div>
        <div class="relative">
          <Calendar class="w-3.5 h-3.5 text-white absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" />
          <input
            v-model="startDateFilter"
            type="date"
            class="pl-8 pr-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:border-sky-500 outline-none [color-scheme:dark]"
          />
        </div>
        <span class="text-xs text-slate-500">hasta</span>
        <div class="relative">
          <Calendar class="w-3.5 h-3.5 text-white absolute left-2.5 top-1/2 -translate-y-1/2 pointer-events-none z-10" />
          <input
            v-model="endDateFilter"
            type="date"
            class="pl-8 pr-2 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 focus:border-sky-500 outline-none [color-scheme:dark]"
          />
        </div>
        <button
          @click="applyFilter"
          class="px-3 py-1.5 rounded-lg bg-sky-500/20 text-sky-300 hover:bg-sky-500/30 text-xs font-semibold border border-sky-500/30 transition-all"
        >
          Filtrar
        </button>
        <button
          @click="resetFilter"
          class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium transition-all"
        >
          Limpiar
        </button>
      </div>

      <button
        @click="fetchData"
        class="flex items-center space-x-1.5 text-xs text-slate-400 hover:text-sky-300 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 transition-all ml-auto"
      >
        <RefreshCw class="w-3.5 h-3.5" :class="{ 'animate-spin': loading }" />
        <span>Actualizar</span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="py-12 text-center">
      <div class="inline-flex items-center space-x-3 text-sky-400">
        <RefreshCw class="w-6 h-6 animate-spin" />
        <span class="text-sm font-semibold">Cargando registros desde la API...</span>
      </div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs text-center">
      {{ error }}
    </div>

    <!-- Empty State -->
    <div v-else-if="activeData.length === 0" class="py-12 text-center text-slate-400 text-xs">
      No se encontraron registros en la base de datos para el rango seleccionado.
    </div>

    <!-- Data Table Grid -->
    <div v-else class="space-y-4">
      <div class="overflow-x-auto rounded-xl border border-slate-800/80 shadow-inner">
        <table class="w-full text-xs text-left">
          <thead class="bg-slate-950 text-slate-300 font-semibold border-b border-slate-800 uppercase tracking-wider sticky top-0">
            <tr>
              <th class="py-3 px-4 min-w-[110px] bg-slate-950 border-r border-slate-800">Fecha</th>
              <th v-for="h in 25" :key="h" class="py-3 px-2 text-center min-w-[55px]">
                h{{ h }}
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-800/50 bg-slate-900/40 font-mono">
            <tr 
              v-for="row in paginatedData" 
              :key="row.id"
              class="hover:bg-slate-800/40 transition-colors"
            >
              <!-- Date Column -->
              <td class="py-2.5 px-4 font-semibold text-slate-200 bg-slate-950/80 border-r border-slate-800">
                {{ row.date }}
              </td>
              <!-- Hours h1 to h25 -->
              <td
                v-for="h in 25"
                :key="h"
                class="py-2.5 px-2 text-center transition-all"
                :class="getHeatmapClass(row[`h${h}`], activeTab)"
              >
                {{ row[`h${h}`] !== null && row[`h${h}`] !== undefined ? row[`h${h}`] : '-' }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Pagination Footer -->
      <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
        <span>Mostrando página {{ currentPage }} de {{ totalPages }} ({{ activeData.length }} registros totales)</span>
        
        <div class="flex items-center space-x-2">
          <button
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            <ChevronLeft class="w-4 h-4" />
          </button>
          
          <button
            @click="currentPage++"
            :disabled="currentPage >= totalPages"
            class="p-1.5 rounded-lg bg-slate-900 border border-slate-800 hover:bg-slate-800 text-slate-300 disabled:opacity-40 disabled:cursor-not-allowed transition-all"
          >
            <ChevronRight class="w-4 h-4" />
          </button>
        </div>
      </div>

    </div>

  </div>
</template>
