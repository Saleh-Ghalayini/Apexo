import { useCallback } from 'react';

// Dummy implementation for useApi. Replace with your actual logic as needed.
export default function useApi() {
  const callApi = useCallback(async (url: string, options?: RequestInit) => {
    const response = await fetch(url, options);
    if (!response.ok) throw new Error('API request failed');
    return response.json();
  }, []);
  return { callApi };
}
