import React, { useState } from 'react';
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
  const api = useApi();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!prompt.trim()) return;
    setLoading(true);
    try {
      await api.post('/ai/notion/prompt', { prompt });
      setPrompt('');
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
      {/* History UI omitted for brevity */}
    </div>
  );
};

export default AIPromptPanel;