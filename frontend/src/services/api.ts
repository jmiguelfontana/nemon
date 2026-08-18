import axios from 'axios';
import type {
  CalculateRequest,
  CalculateResponse,
  ConsumptionRecord,
  PriceRecord,
} from '../types/energy';

// Forzamos la base URL a '/api' para que el tráfico SIEMPRE pase por los proxies
// (Vite en desarrollo y Nginx en producción), evitando problemas de CORS o resolución de localhost.
const API_BASE_URL = '/api';

export const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

/**
 * Llama al endpoint POST /api/calculate
 */
export async function calculateEnergy(data: CalculateRequest): Promise<CalculateResponse> {
  const response = await apiClient.post<CalculateResponse>('calculate', data);
  return response.data;
}

/**
 * Llama al endpoint GET /api/consumptions
 */
export async function getConsumptions(startDate?: string, endDate?: string): Promise<ConsumptionRecord[]> {
  const params: Record<string, string> = {};
  if (startDate) params.start_date = startDate;
  if (endDate) params.end_date = endDate;

  const response = await apiClient.get<ConsumptionRecord[]>('consumptions', { params });
  return response.data;
}

/**
 * Llama al endpoint GET /api/prices
 */
export async function getPrices(startDate?: string, endDate?: string): Promise<PriceRecord[]> {
  const params: Record<string, string> = {};
  if (startDate) params.start_date = startDate;
  if (endDate) params.end_date = endDate;

  const response = await apiClient.get<PriceRecord[]>('prices', { params });
  return response.data;
}

/**
 * Verifica la salud de la API
 */
export async function checkApiHealth(): Promise<boolean> {
  try {
    await apiClient.get('consumptions', { 
      params: { start_date: '2025-01-01', end_date: '2025-01-01', limit: 1 } 
    });
    return true;
  } catch (error: any) {
    // Si da un error de validación (422) significa que el backend está vivo y contestando.
    if (error.response && error.response.status === 422) {
      return true;
    }
    return false;
  }
}
