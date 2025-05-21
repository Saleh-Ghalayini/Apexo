import React, { useState, useEffect } from 'react';
import slackIcon from '../../assets/images/w_slack_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';

interface AddIntegrationModalProps {
  show: boolean;
  onClose: () => void;
  onIntegrationAdded: () => void;
}

const mockProviders = [
  { id: 'slack', name: 'Slack', type: 'workspace', description: 'Connect Slack', iconUrl: '' },
  { id: 'notion', name: 'Notion', type: 'channel', description: 'Connect Notion', iconUrl: '' }
];

const getIconForProvider = (type: string) => {
  switch (type) {
    case 'workspace':
      return slackIcon;
    case 'channel':
      return notionIcon;
    default:
      return notionIcon;
  }
};

const AddIntegrationModal: React.FC<AddIntegrationModalProps> = ({ show, onClose }) => {
  const [providers, setProviders] = useState<{ id: string; name: string; type: string; description: string; iconUrl: string }[]>([]);
  const [loading, setLoading] = useState(false);
  const [error] = useState<string | null>(null);

  useEffect(() => {
    if (show) {
      setLoading(true);
      setTimeout(() => {
        setProviders(mockProviders);
        setLoading(false);
      }, 500);
    }
  }, [show]);

  const handleConnect = (providerId: string) => {
    alert(`Connecting to ${providerId}`);
  };

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
              <img src={p.iconUrl || getIconForProvider(p.type)} alt={p.name} width={32} height={32} />
              <span>{p.name}</span>
              <span>{p.description}</span>
              <button onClick={() => handleConnect(p.id)}>Connect</button>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
};

export default AddIntegrationModal;