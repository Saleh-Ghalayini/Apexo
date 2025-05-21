import { useCallback } from 'react';

// Dummy implementation for useApi. Replace with your actual logic as needed.
export default function useApi() {
  // Add get and post methods to match usage in AIPromptPanel
  const get = useCallback(async (url: string) => {
    const response = await fetch(url);
    if (!response.ok) throw new Error('API request failed');
    return response.json();
  }, []);

  const post = useCallback(async (url: string, data?: Record<string, unknown>) => {
    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    if (!response.ok) throw new Error('API request failed');
    return response.json();
  }, []);

  return { get, post };
}
