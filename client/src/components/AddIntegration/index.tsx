import React, { useState, useCallback } from 'react';
import Modal from '../Modal';
import slackIcon from '../../assets/images/w_slack_icon.png';
import calendarIcon from '../../assets/images/calendar_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';
import mailIcon from '../../assets/images/w_mail_icon.png';

interface AddIntegrationProps {
  isOpen: boolean;
  onClose: () => void;
  onAddIntegration: (integration: {
    name: string;
    email: string;
    type: string;
  }) => void;
}

const defaultProviders = [
  { id: 'slack', name: 'Slack', icon: slackIcon, type: 'workspace' },
  { id: 'notion', name: 'Notion', icon: notionIcon, type: 'channel' },
  { id: 'calendar', name: 'Google Calendar', icon: calendarIcon, type: 'scheduler' },
  { id: 'email', name: 'Email', icon: mailIcon, type: 'Email' }
];

const AddIntegration: React.FC<AddIntegrationProps> = ({ isOpen, onClose, onAddIntegration }) => {
  const [selectedType, setSelectedType] = useState('');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [providers, setProviders] = useState(defaultProviders);

  const loadProviders = useCallback(async () => {
    try {
      setIsLoading(true);
      setProviders(defaultProviders);
      setError(null);
    } catch (err) {
      setError('Failed to load integration providers');
      setProviders(defaultProviders);
    } finally {
      setIsLoading(false);
    }
  }, []);

  const handleSubmit = () => {
    if (!selectedType || !name || !email) return;
    const selectedProvider = providers.find(p => p.id === selectedType);
    if (selectedProvider) {
      onAddIntegration({
        name,
        email,
        type: selectedProvider.type
      });
      setSelectedType('');
      setName('');
      setEmail('');
      onClose();
    }
  };

  if (!isOpen) return null;

  return (
    <Modal isOpen={isOpen} onClose={onClose} title="Add New Integration">
      {error && <div style={{ color: 'red', marginBottom: 8 }}>{error}</div>}
      <div>
        <label>Integration Type</label>
        <div>
          {providers.map(type => (
            <button key={type.id} onClick={() => setSelectedType(type.id)}>
              <img src={type.icon} alt={type.name} width={24} height={24} />
              {type.name}
            </button>
          ))}
        </div>
        <div>
          <label>Name</label>
          <input value={name} onChange={e => setName(e.target.value)} />
        </div>
        <div>
          <label>Email</label>
          <input value={email} onChange={e => setEmail(e.target.value)} />
        </div>
        <button onClick={handleSubmit}>Connect</button>
      </div>
      {isLoading && (
        <div style={{ marginTop: 16 }}>
          <span>Loading integration providers...</span>
        </div>
      )}
    </Modal>
  );
};

export default AddIntegration;