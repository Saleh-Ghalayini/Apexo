import React, { useState, useEffect } from 'react';
import { useApi } from '../../hooks/useApi';

interface AIPromptStatus {
  id: number;
  prompt: string;
  status: string;
  operation_type: string;
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

  const handleRetry = async (promptId: number) => {
    setLoading(true);
    try {
      await api.post(`/ai/notion/retry/${promptId}`);
      setRefreshKey(prev => prev + 1);
    } catch {}
    setLoading(false);
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'pending':
        return <span className="badge badge-pending">Pending</span>;
      case 'processing':
        return <span className="badge badge-processing">Processing</span>;
      case 'completed':
        return <span className="badge badge-completed">Completed</span>;
      case 'failed':
        return <span className="badge badge-failed">Failed</span>;
      default:
        return <span className="badge">{status}</span>;
    }
  };

  const getOperationBadge = (type: string) => {
    switch (type) {
      case 'create':
        return <span className="badge badge-create">Create</span>;
      case 'update':
        return <span className="badge badge-update">Update</span>;
      case 'delete':
        return <span className="badge badge-delete">Delete</span>;
      case 'query':
        return <span className="badge badge-query">Query</span>;
      default:
        return <span className="badge">{type}</span>;
    }
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
                {getStatusBadge(item.status)} {getOperationBadge(item.operation_type)} {item.prompt} - {item.created_at}
                {item.status === 'failed' && (
                  <button onClick={() => handleRetry(item.id)} disabled={loading}>
                    Retry
                  </button>
                )}
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  );
};

export default AIPromptPanel;