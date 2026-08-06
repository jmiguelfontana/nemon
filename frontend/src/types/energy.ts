export interface CalculateRequest {
  start_date: string;
  end_date: string;
  formula: string;
}

export interface CalculateResponse {
  price_indexed: number;
  total_importes: number;
  total_consumos: number;
}

export interface ConsumptionRecord {
  id: number;
  date: string;
  [key: string]: number | string | null; // h1 to h25
}

export interface PriceRecord {
  id: number;
  date: string;
  [key: string]: number | string | null; // h1 to h25
}

export interface ApiErrorResponse {
  error: string;
  details?: Record<string, string[]>;
  message?: string;
}
