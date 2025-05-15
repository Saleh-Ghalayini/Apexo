import React, { useState, useEffect } from 'react';
import { useApi } from '../../hooks/useApi';

interface AIPromptStatus {
  id: number;
  prompt: string;
  status: string;
  created_at: string;
}

const AIPromptPanel: React.FC = () => {
  const [prompt, setPrompt] = useState('');
  const [loading, setLoading] = useState(false);
  const [promptHistory, setPromptHistory] = useState<AIPromptStatus[]>([]);
  const [refreshKey, setRefreshKey] = useState(0);
  const api = useApi();

  useEffect(() => {
    fetchPromptHistory();
    const interval = setInterval(fetchPromptHistory, 5000);
    return () => clearInterval(interval);
  }, [refreshKey]);

  const fetchPromptHistory = async () => {
    try {
      const response = await api.get('/ai/prompts/history');
      if (response.data?.prompts) {
        setPromptHistory(response.data.prompts);
      }
    } catch {}
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!prompt.trim()) return;
    setLoading(true);
    try {
      await api.post('/ai/notion/prompt', { prompt });
      setPrompt('');
      setRefreshKey(prev => prev + 1);
    } catch {}
    setLoading(false);
  };

  return (
    <div>
      <h2>AI Assistant for Notion</h2>
      <form onSubmit={handleSubmit}>
        <textarea
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
          placeholder="Type your prompt..."
          disabled={loading}
        />
        <button type="submit" disabled={loading || !prompt.trim()}>
          {loading ? 'Submitting...' : 'Submit'}
        </button>
      </form>
      <div>
        <h3>Recent Prompts</h3>
        {promptHistory.length === 0 ? (
          <div>No prompts yet.</div>
        ) : (
          <ul>
            {promptHistory.map((item) => (
              <li key={item.id}>
                {item.prompt} - {item.status} - {item.created_at}
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
};

export default AIPromptPanel;