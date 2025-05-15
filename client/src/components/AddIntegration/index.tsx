import React, { useState, useCallback } from 'react';
import Modal from '../Modal';
import slackIcon from '../../assets/images/w_slack_icon.png';
import calendarIcon from '../../assets/images/calendar_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';
import mailIcon from '../../assets/images/w_mail_icon.png';
import SlackConnectButton from './SlackConnectButton';
import SlackStatus from './SlackStatus';
import NotionConnectButton from './NotionConnectButton';
import NotionStatus from './NotionStatus';

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

  const [isSlackSelected, setIsSlackSelected] = useState(false);
  const [slackConnected, setSlackConnected] = useState(false);
  const [isNotionSelected, setIsNotionSelected] = useState(false);
  const [notionConnected, setNotionConnected] = useState(false);

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

  const handleTypeSelect = (typeId: string) => {
    setSelectedType(typeId);
    setIsSlackSelected(typeId === 'slack');
    setIsNotionSelected(typeId === 'notion');
  };

  const handleSubmit = () => {
    if (!selectedType) return;
    if ((selectedType === 'slack' && !slackConnected) ||
        (selectedType === 'notion' && !notionConnected)) {
      return;
    }
    if (!name || !email) return;
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
      setIsSlackSelected(false);
      setIsNotionSelected(false);
      setSlackConnected(false);
      setNotionConnected(false);
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
            <button
              key={type.id}
              onClick={() => handleTypeSelect(type.id)}
              style={{
                fontWeight: selectedType === type.id ? 'bold' : 'normal',
                border: selectedType === type.id ? '2px solid #007bff' : '1px solid #ccc',
                marginRight: 8
              }}
            >
              <img src={type.icon} alt={type.name} width={24} height={24} />
              {type.name}
            </button>
          ))}
        </div>
        {selectedType === 'slack' ? (
          <div style={{ margin: '24px 0' }}>
            <SlackStatus />
            <div style={{ marginTop: '16px' }}>
              <SlackConnectButton onSuccess={() => setSlackConnected(true)} />
            </div>
            {slackConnected && (
              <div style={{ marginTop: 16, color: 'green', fontWeight: 500 }}>
                Slack Connected! You can now fill out the rest of the form and submit.
              </div>
            )}
            <div style={{ marginTop: 8, color: '#888', fontSize: 13 }}>
              Click "Connect Slack" to authorize this application with your Slack workspace.
              The window will open in a new tab.
            </div>
            {slackConnected && (
              <>
                <div>
                  <label>Workspace Name</label>
                  <input
                    type="text"
                    value={name}
                    onChange={e => setName(e.target.value)}
                    placeholder="Enter workspace name"
                  />
                </div>
                <div>
                  <label>Workspace Email</label>
                  <input
                    type="email"
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    placeholder="Enter workspace email"
                  />
                </div>
                <button
                  onClick={handleSubmit}
                  disabled={!name || !email}
                  style={{ marginTop: 12 }}
                >
                  Save Integration
                </button>
              </>
            )}
          </div>
        ) : selectedType === 'notion' ? (
          <div style={{ margin: '24px 0' }}>
            <NotionStatus />
            <div style={{ marginTop: '16px' }}>
              <NotionConnectButton onSuccess={() => setNotionConnected(true)} />
            </div>
            {notionConnected && (
              <div style={{ marginTop: 16, color: 'green', fontWeight: 500 }}>
                Notion Connected! You can now fill out the rest of the form and submit.
              </div>
            )}
            <div style={{ marginTop: 8, color: '#888', fontSize: 13 }}>
              Click "Connect Notion" to authorize this application with your Notion workspace.
              The window will open in a new tab.
            </div>
            {notionConnected && (
              <>
                <div>
                  <label>Workspace Name</label>
                  <input
                    type="text"
                    value={name}
                    onChange={e => setName(e.target.value)}
                    placeholder="Enter workspace name"
                  />
                </div>
                <div>
                  <label>Workspace Email</label>
                  <input
                    type="email"
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    placeholder="Enter workspace email"
                  />
                </div>
                <button
                  onClick={handleSubmit}
                  disabled={!name || !email}
                  style={{ marginTop: 12 }}
                >
                  Save Integration
                </button>
              </>
            )}
          </div>
        ) : (
          <>
            <div>
              <label>Name</label>
              <input
                type="text"
                value={name}
                onChange={e => setName(e.target.value)}
                placeholder="Enter name for this integration"
              />
            </div>
            <div>
              <label>Email</label>
              <input
                type="email"
                value={email}
                onChange={e => setEmail(e.target.value)}
                placeholder="Enter email associated with this account"
              />
            </div>
            <button onClick={handleSubmit} disabled={!selectedType || !name || !email}>
              Connect
            </button>
          </>
        )}
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