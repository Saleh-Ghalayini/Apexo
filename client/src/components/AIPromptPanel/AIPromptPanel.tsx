import React, { useState, useEffect } from 'react';
import useApi from '../../hooks/useApi';
import Button from '../../components/Button';
import Toast from '../../components/Toast';
import './AIPromptPanel.css';

interface AIPromptStatus {
  id: number;
  prompt: string;
  status: 'pending' | 'processing' | 'completed' | 'failed';
  operation_type: 'create' | 'update' | 'delete' | 'query';
  created_at: string;
  ai_response?: unknown;
  notion_response?: unknown;
  error_message?: string;
}

const AIPromptPanel: React.FC = () => {
  const [prompt, setPrompt] = useState('');
  const [loading, setLoading] = useState(false);
  const [promptHistory, setPromptHistory] = useState<AIPromptStatus[]>([]);
  const [refreshKey, setRefreshKey] = useState(0);
  const [showToast, setShowToast] = useState(false);
  const [toastMessage, setToastMessage] = useState('');
  const [toastType, setToastType] = useState<'success' | 'error'>('success');

  const api = useApi();

  useEffect(() => {
    fetchPromptHistory();
    const interval = setInterval(fetchPromptHistory, 5000);
    return () => clearInterval(interval);
    // eslint-disable-next-line
  }, [refreshKey]);

  const fetchPromptHistory = async () => {
    try {
      const response = await api.get('/ai/prompts/history');
      if (response.data?.prompts) {
        setPromptHistory(response.data.prompts);
      }
    } catch (error) {
      console.error('Error fetching prompt history:', error);
      setToastMessage('Failed to fetch prompt history.');
      setToastType('error');
      setShowToast(true);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!prompt.trim()) return;
    setLoading(true);
    try {
      await api.post('/ai/notion/prompt', { prompt });
      setToastMessage('Prompt submitted successfully!');
      setToastType('success');
      setShowToast(true);
      setPrompt('');
      setRefreshKey(prev => prev + 1);
    } catch (error) {
      console.error('Error submitting prompt:', error);
      setToastMessage('Failed to submit prompt. Please try again.');
      setToastType('error');
      setShowToast(true);
    } finally {
      setLoading(false);
    }
  };

  const handleRetry = async (promptId: number) => {
    setLoading(true);
    try {
      await api.post(`/ai/notion/retry/${promptId}`);
      setToastMessage('Retrying prompt...');
      setToastType('success');
      setShowToast(true);
      setRefreshKey(prev => prev + 1);
    } catch (error) {
      console.error('Error retrying prompt:', error);
      setToastMessage('Failed to retry prompt. Please try again.');
      setToastType('error');
      setShowToast(true);
    } finally {
      setLoading(false);
    }
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
    <div className="ai-prompt-panel">
      <h2>AI Assistant for Notion</h2>
      <p>Ask me to create, update, or delete tasks in Notion</p>
      <form onSubmit={handleSubmit} className="prompt-form">
        <textarea
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
          placeholder="e.g., Create a task to write documentation by Friday"
          rows={3}
          disabled={loading}
        />
        <Button
          type="submit"
          disabled={loading || !prompt.trim()}
          loading={loading}
        >
          Submit
        </Button>
      </form>
      <div className="history-section">
        <h3>Recent Prompts</h3>
        {promptHistory.length === 0 ? (
          <div className="empty-state">No prompts yet. Try creating one!</div>
        ) : (
          <div className="prompt-history">
            {promptHistory.map((item) => (
              <div key={item.id} className="history-item">
                <div className="history-header">
                  <div className="badge-container">
                    {getStatusBadge(item.status)}
                    {getOperationBadge(item.operation_type)}
                  </div>
                  <span className="timestamp">
                    {new Date(item.created_at).toLocaleString()}
                  </span>
                </div>
                <div className="prompt-text">{item.prompt}</div>
                {item.error_message && (
                  <div className="error-message">
                    <strong>Error:</strong> {item.error_message}
                  </div>
                )}
                <div className="history-actions">
                  {item.status === 'failed' && (
                    <Button
                      onClick={() => handleRetry(item.id)}
                      size="sm"
                      variant="secondary"
                      disabled={loading}
                    >
                      Retry
                    </Button>
                  )}
                  <Button
                    size="sm"
                    variant="text"
                    onClick={() => {
                      // View details logic
                    }}
                  >
                    View Details
                  </Button>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
      {showToast && (
        <Toast
          message={toastMessage}
          type={toastType}
          onClose={() => setShowToast(false)}
          autoClose={true}
          autoCloseTime={3000}
        />
      )}
    </div>
  );
};

export default AIPromptPanel;