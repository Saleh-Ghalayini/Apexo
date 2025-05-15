import React, { useState } from 'react';

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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!prompt.trim()) return;
    setLoading(true);
    setTimeout(() => {
      setPromptHistory([
        ...promptHistory,
        {
          id: Date.now(),
          prompt,
          status: 'pending',
          created_at: new Date().toISOString(),
        },
      ]);
      setLoading(false);
      setPrompt('');
    }, 1000);
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