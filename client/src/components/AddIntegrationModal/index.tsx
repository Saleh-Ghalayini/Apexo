import React, { useState, useEffect } from 'react';

interface AddIntegrationModalProps {
  show: boolean;
  onClose: () => void;
  onIntegrationAdded: () => void;
}

const mockProviders = [
  { id: 'slack', name: 'Slack', type: 'workspace', description: 'Connect Slack', iconUrl: '' },
  { id: 'notion', name: 'Notion', type: 'channel', description: 'Connect Notion', iconUrl: '' }
];

const AddIntegrationModal: React.FC<AddIntegrationModalProps> = ({ show, onClose }) => {
  const [providers, setProviders] = useState<any[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (show) {
      setLoading(true);
      setTimeout(() => {
        setProviders(mockProviders);
        setLoading(false);
      }, 500);
    }
  }, [show]);

  if (!show) return null;
  return (
    <div>
      <div>
        <h2>Add Integration</h2>
        <button onClick={onClose}>Close</button>
      </div>
      <div>
        {loading && <div>Loading...</div>}
        {error && <div>{error}</div>}
        <div>
          {providers.map(p => (
            <div key={p.id}>
              <span>{p.name}</span>
              <span>{p.description}</span>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default AddIntegrationModal;