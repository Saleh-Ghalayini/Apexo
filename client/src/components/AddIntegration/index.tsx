import React, { useState, useEffect, useCallback } from 'react';
import Modal from '../Modal';
import './AddIntegration.css';
import slackIcon from '../../assets/images/w_slack_icon.png';
import calendarIcon from '../../assets/images/calendar_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';
import mailIcon from '../../assets/images/w_mail_icon.png';
import { IntegrationService } from '../../services/integrationService';
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
    type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  }) => void;
}

interface IntegrationType {
  id: string;
  name: string;
  icon: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
}

const AddIntegration: React.FC<AddIntegrationProps> = ({ isOpen, onClose, onAddIntegration }) => {
  const [selectedType, setSelectedType] = useState<string>('');
  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [providers, setProviders] = useState<IntegrationType[]>([]);
  const [isSlackSelected, setIsSlackSelected] = useState(false);
  const [slackConnected, setSlackConnected] = useState(false);
  const [isNotionSelected, setIsNotionSelected] = useState(false);
  const [notionConnected, setNotionConnected] = useState(false);

  const getIconForType = (type: string): string => {
    switch (type) {
      case 'workspace':
        return slackIcon;
      case 'channel':
        return notionIcon;
      case 'scheduler':
        return calendarIcon;
      case 'Email':
        return mailIcon;
      default:
        return notionIcon;
    }
  };

  const loadProviders = useCallback(async () => {
    try {
      setIsLoading(true);
      const apiProviders = await IntegrationService.getProviders();
      interface ApiProvider {
        id: string;
        name: string;
        type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
      }
      if (apiProviders && apiProviders.length > 0) {
        const mappedProviders = apiProviders.map((p: ApiProvider) => ({
          id: p.id,
          name: p.name,
          icon: getIconForType(p.type),
          type: p.type
        }));
        setProviders(mappedProviders);
      } else {
        setProviders([
          { id: 'slack', name: 'Slack', icon: slackIcon, type: 'workspace' },
          { id: 'notion', name: 'Notion', icon: notionIcon, type: 'channel' },
          { id: 'calendar', name: 'Google Calendar', icon: calendarIcon, type: 'scheduler' },
          { id: 'email', name: 'Email', icon: mailIcon, type: 'Email' }
        ]);
      }
      setError(null);
    } catch (err) {
      console.error('Failed to load integration providers:', err);
      setError('Failed to load integration providers');
      setProviders([
        { id: 'slack', name: 'Slack', icon: slackIcon, type: 'workspace' },
        { id: 'notion', name: 'Notion', icon: notionIcon, type: 'channel' },
        { id: 'calendar', name: 'Google Calendar', icon: calendarIcon, type: 'scheduler' },
        { id: 'email', name: 'Email', icon: mailIcon, type: 'Email' }
      ]);
    } finally {
      setIsLoading(false);
    }
  }, []);

  useEffect(() => {
    if (isOpen) {
      loadProviders();
      setSelectedType('');
      setName('');
      setEmail('');
      setIsSlackSelected(false);
      setIsNotionSelected(false);
      setError(null);
      setSlackConnected(false);
      setNotionConnected(false);
    }
  }, [isOpen, loadProviders]);

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
    const selectedIntegration = providers.find(type => type.id === selectedType);
    if (selectedIntegration) {
      onAddIntegration({
        name,
        email,
        type: selectedIntegration.type
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

  if (!isOpen) {
    return null;
  }

  return (
    <Modal
      isOpen={isOpen}
      onClose={onClose}
      title="Add New Integration"
      footer={
        selectedType === 'slack' || selectedType === 'notion' ? (
          <button className="modal-button secondary" onClick={onClose}>Close</button>
        ) : (
          <>
            <button className="modal-button secondary" onClick={onClose}>Cancel</button>
            <button
              className="modal-button primary"
              onClick={handleSubmit}
              disabled={!selectedType || ((!isSlackSelected && !isNotionSelected) && (!name || !email))}
            >
              Connect
            </button>
          </>
        )
      }
    >
      {error && <div className="integration-error-message">{error}</div>}
      <div className="add-integration-form">
        <div className="modal-form-group">
          <label>Integration Type</label>
          <div className="integration-types-grid">
            {providers.map(type => (
              <div
                key={type.id}
                className={`integration-type-item ${selectedType === type.id ? 'selected' : ''}`}
                onClick={() => handleTypeSelect(type.id)}
              >
                <div className="integration-type-icon">
                  <img src={type.icon} alt={type.name} width={24} height={24} />
                </div>
                <div className="integration-type-name">{type.name}</div>
              </div>
            ))}
          </div>
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
                <div className="modal-form-group">
                  <label htmlFor="integration-name">Workspace Name</label>
                  <input
                    id="integration-name"
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Enter workspace name"
                  />
                </div>
                <div className="modal-form-group">
                  <label htmlFor="integration-email">Workspace Email</label>
                  <input
                    id="integration-email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Enter workspace email"
                  />
                </div>
                <button
                  className="modal-button primary"
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
                <div className="modal-form-group">
                  <label htmlFor="integration-name">Workspace Name</label>
                  <input
                    id="integration-name"
                    type="text"
                    value={name}
                    onChange={(e) => setName(e.target.value)}
                    placeholder="Enter workspace name"
                  />
                </div>
                <div className="modal-form-group">
                  <label htmlFor="integration-email">Workspace Email</label>
                  <input
                    id="integration-email"
                    type="email"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    placeholder="Enter workspace email"
                  />
                </div>
                <button
                  className="modal-button primary"
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
            <div className="modal-form-group">
              <label htmlFor="integration-name">Name</label>
              <input
                id="integration-name"
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Enter name for this integration"
              />
            </div>
            <div className="modal-form-group">
              <label htmlFor="integration-email">Email</label>
              <input
                id="integration-email"
                type="email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
                placeholder="Enter email associated with this account"
              />
            </div>
          </>
        )}
      </div>
      {isLoading && providers.length === 0 && (
        <div className="add-integration-form loading">
          <div className="loading-spinner-small"></div>
          <p>Loading integration providers...</p>
        </div>
      )}
    </Modal>
  );
};

export default AddIntegration;