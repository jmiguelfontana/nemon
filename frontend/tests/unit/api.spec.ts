import { describe, it, expect, vi, beforeEach } from 'vitest';
import axios from 'axios';
import { checkApiHealth, calculateEnergy, getConsumptions, getPrices } from '../../src/services/api';

// Mockeamos axios completamente para los tests unitarios
vi.mock('axios', () => {
  return {
    default: {
      create: vi.fn(() => ({
        get: vi.fn(),
        post: vi.fn(),
      })),
    },
  };
});

describe('API Service (Unit Test)', () => {
  it('checkApiHealth should return true if request succeeds', async () => {
    // Importamos la instancia mockeada (que fue creada por el mock de arriba)
    const { apiClient } = await import('../../src/services/api');
    
    // Forzamos a que el método GET de nuestro mock se resuelva correctamente
    vi.mocked(apiClient.get).mockResolvedValueOnce({ data: [] });
    
    const isHealthy = await checkApiHealth();
    expect(isHealthy).toBe(true);
    expect(apiClient.get).toHaveBeenCalledWith('consumptions', { params: { start_date: '2025-01-01', end_date: '2025-01-01', limit: 1 } });
  });

  it('checkApiHealth should return true if request fails with 422 (validation error)', async () => {
    const { apiClient } = await import('../../src/services/api');
    
    // Forzamos un error 422
    const error422: any = new Error('Unprocessable Entity');
    error422.response = { status: 422 };
    vi.mocked(apiClient.get).mockRejectedValueOnce(error422);
    
    const isHealthy = await checkApiHealth();
    expect(isHealthy).toBe(true);
  });

  it('checkApiHealth should return false if request fails', async () => {
    const { apiClient } = await import('../../src/services/api');
    
    // Forzamos un error
    vi.mocked(apiClient.get).mockRejectedValueOnce(new Error('Network Error'));
    
    const isHealthy = await checkApiHealth();
    expect(isHealthy).toBe(false);
  });
});
