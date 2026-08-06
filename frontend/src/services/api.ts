import axios from 'axios';
import type {
  CalculateRequest,
  CalculateResponse,
  ConsumptionRecord,
  PriceRecord,
} from '../types/energy';

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api';

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
  const response = await apiClient.post<CalculateResponse>('/calculate', data);
  return response.data;
}

/**
 * Llama al endpoint GET /api/consumptions
 */
export async function getConsumptions(startDate?: string, endDate?: string): Promise<ConsumptionRecord[]> {
  const params: Record<string, string> = {};
  if (startDate) params.start_date = startDate;
  if (endDate) params.end_date = endDate;

  const response = await apiClient.get<ConsumptionRecord[]>('/consumptions', { params });
  return response.data;
}

/**
 * Llama al endpoint GET /api/prices
 */
export async function getPrices(startDate?: string, endDate?: string): Promise<PriceRecord[]> {
  const params: Record<string, string> = {};
  if (startDate) params.start_date = startDate;
  if (endDate) params.end_date = endDate;

  const response = await apiClient.get<PriceRecord[]>('/prices', { params });
  return response.data;
}

/**
 * Verifica la salud de la API
 */
export async function checkApiHealth(): Promise<boolean> {
  try {
    await apiClient.get('/consumptions', { params: { limit: 1 } });
    return true;
  } catch {
    return false;
  }
}
