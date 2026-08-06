<script setup lang="ts">
import { AlertTriangle, DatabaseBackup, ServerCrash, XCircle } from 'lucide-vue-next';

const props = defineProps<{
  error: {
    status?: number;
    message: string;
    details?: Record<string, string[]>;
  } | null;
}>();

const emit = defineEmits<{
  (e: 'close'): void;
}>();
</script>

<template>
  <div v-if="error" class="p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 relative overflow-hidden backdrop-blur-md shadow-lg transition-all">
    <div class="flex items-start space-x-3.5">
      
      <!-- Icon based on status -->
      <div class="p-2.5 rounded-xl bg-rose-500/20 text-rose-400 shrink-0">
        <DatabaseBackup v-if="error.status === 404" class="w-6 h-6" />
        <ServerCrash v-else-if="error.status === 500" class="w-6 h-6" />
        <AlertTriangle v-else class="w-6 h-6" />
      </div>

      <div class="flex-1 pr-6">
        <div class="flex items-center space-x-2 mb-1">
          <span class="text-xs font-bold px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">
            HTTP {{ error.status || 'Error' }}
          </span>
          <h3 class="text-sm font-bold text-slate-100">
            {{ error.status === 404 ? 'Registros No Encontrados' : error.status === 400 ? 'Solicitud Inválida' : 'Error de Sistema' }}
          </h3>
        </div>

        <p class="text-xs text-rose-200 leading-relaxed font-medium">
          {{ error.message }}
        </p>

        <!-- Validation Details if present -->
        <div v-if="error.details && Object.keys(error.details).length > 0" class="mt-3 pt-2 border-t border-rose-500/20 space-y-1">
          <div v-for="(msgs, field) in error.details" :key="field" class="text-xs text-rose-300">
            <span class="font-semibold capitalize">{{ field }}:</span> {{ msgs.join(', ') }}
          </div>
        </div>
      </div>

      <!-- Close Button -->
      <button 
        @click="emit('close')" 
        class="text-rose-400 hover:text-slate-100 p-1 rounded-lg hover:bg-rose-500/20 transition-all"
        title="Cerrar aviso"
      >
        <XCircle class="w-5 h-5" />
      </button>

    </div>
  </div>
</template>
