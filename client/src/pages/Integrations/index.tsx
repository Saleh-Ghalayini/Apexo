import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Table from '../../components/Table';
import type { TableColumn } from '../../components/Table';
import './Integrations.css';
import plusIcon from '../../assets/images/add_icon.png';
import arrowIcon from '../../assets/images/l_arrow_icon.png';
import slackIcon from '../../assets/images/w_slack_icon.png';
import calendarIcon from '../../assets/images/calendar_icon.png';
import notionIcon from '../../assets/images/notion_icon.png';
import mailIcon from '../../assets/images/w_mail_icon.png';
import AddIntegration from '../../components/AddIntegration';
import { IntegrationService } from '../../services/integrationService';
import Loading from '../../components/Loading';

// Define our Integration type for TypeScript
interface Integration {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  status: 'active' | 'inactive';
  linkingDate: string;
  provider: string;
  [key: string]: unknown; // Index signature to satisfy Record<string, unknown>
}

const IntegrationsPage: React.FC = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [accounts, setAccounts] = useState<Integration[]>([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  // Load integrations when component mounts
  useEffect(() => {
    loadIntegrations();
  }, []);

  const loadIntegrations = async () => {
    try {
      setLoading(true);
      const fetchedIntegrations = await IntegrationService.getIntegrations();
      
      // Map the API response to our Integration type
      const mappedIntegrations = fetchedIntegrations.map(integration => ({
        ...integration,
        // Ensure all required properties are present
        id: integration.id || `fallback-${Date.now()}`,
        name: integration.name || 'Unknown',
        email: integration.email || 'No email provided',
        type: integration.type || 'Other' as const,
        status: integration.status || 'inactive' as const,
        linkingDate: integration.linkingDate || new Date().toLocaleDateString('en-US'),
        provider: integration.provider || 'unknown'
      }));
      
      setAccounts(mappedIntegrations);
      setError(null);
    } catch (err) {
      console.error('Failed to load integrations:', err);
      setError('Failed to load integrations. Please try again later.');
    } finally {
      setLoading(false);
    }
  };

  const handleGoBack = () => {
    navigate('/dashboard');
  };

  const handleConnect = async (accountId: string) => {
    try {
      const success = await IntegrationService.updateStatus(accountId, 'active');
      if (success) {
        setAccounts(accounts.map(account => 
          account.id === accountId ? { ...account, status: 'active' } : account
        ));
      } else {
        setError('Failed to connect the integration. Please try again.');
      }
    } catch (err) {
      console.error('Error connecting integration:', err);
      setError('An error occurred while connecting the integration.');
    }
  };

  const handleDisconnect = async (accountId: string) => {
    try {
      const success = await IntegrationService.updateStatus(accountId, 'inactive');
      if (success) {
        setAccounts(accounts.map(account => 
          account.id === accountId ? { ...account, status: 'inactive' } : account
        ));
      } else {
        setError('Failed to disconnect the integration. Please try again.');
      }
    } catch (err) {
      console.error('Error disconnecting integration:', err);
      setError('An error occurred while disconnecting the integration.');
    }
  };

  const handleOpenModal = () => {
    setIsModalOpen(true);
  };

  const handleCloseModal = () => {
    setIsModalOpen(false);
  };
  const handleAddIntegration = async (integration: {
    name: string;
    email: string;
    type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  }) => {
    try {
      setLoading(true);

      // Skip API call for Slack since it uses OAuth flow
      if (integration.type !== 'workspace') {
        // For non-Slack integrations, use the regular flow
        await IntegrationService.connect(getProviderFromType(integration.type));
      }
      
      // Simulate a newly added integration
      const newIntegration: Integration = {
        id: `new-${Date.now()}`,
        name: integration.name,
        email: integration.email,
        type: integration.type,
        status: 'active',
        linkingDate: new Date().toLocaleDateString('en-US'),
        provider: getProviderFromType(integration.type)
      };

      setAccounts([...accounts, newIntegration]);
      setError(null);
    } catch (err) {
      console.error('Error adding integration:', err);
      setError('Failed to add integration. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  const getProviderFromType = (type: string): string => {
    switch (type) {
      case 'workspace':
        return 'slack';
      case 'channel':
        return 'notion';
      case 'scheduler':
        return 'calendar';
      case 'Email':
        return 'email';
      default:
        return 'other';
    }
  };

  const getIconForType = (type: string) => {
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

  const columns: TableColumn<Integration>[] = [
    {
      key: 'account',
      header: 'Account',
      render: (account) => (
        <div className="account-cell">
          <div className="account-icon">
            <img src={getIconForType(account.type)} width={24} height={24} alt={account.type} />
          </div>
          <div className="account-info">
            <div className="account-name">{account.name}</div>
            <div className="account-email">{account.email}</div>
          </div>
        </div>
      )
    },
    {
      key: 'status',
      header: 'Status',
      render: (account) => (
        <div className={`status-cell ${account.status}`}>
          {account.status === 'active' ? 'Active' : 'Inactive'}
        </div>
      )
    },
    {      key: 'actions',
      header: 'Actions',
      render: (account) => (
        <div style={{ display: 'flex', justifyContent: 'center', gap: '10px' }}>
          {account.status === 'active' ? (
            <>
              <button 
                className="action-button disconnect"
                onClick={() => handleDisconnect(account.id)}
              >
                Disconnect
              </button>
              
              {/* Add Notion-specific actions */}
              {account.provider === 'notion' && (
                <button
                  className="action-button"
                  onClick={() => navigate('/notion/databases')}
                >
                  Manage Databases
                </button>
              )}
            </>
          ) : (
            <button 
              className="action-button connect"
              onClick={() => handleConnect(account.id)}
            >
              Connect
            </button>
          )}
        </div>
      )
    },
    {
      key: 'linkingDate',
      header: 'Linking Date',
    }
  ];

  return (
    <div className="integrations-container">
      <div className="integrations-header">
        <button className="back-button" onClick={handleGoBack}>
          <img src={arrowIcon} width={22} height={22} alt="Go Back" className="back-icon" />
        </button>
        <h1 className="header-title">Currently Linked Accounts</h1>
      </div>
      
      <div className="integrations-content">
        {loading ? (
          <div className="loading-container">
            <Loading />
          </div>
        ) : error ? (
          <div className="error-message">
            {error}
            <button onClick={loadIntegrations} className="retry-button">
              Retry
            </button>
          </div>
        ) : (
          <div className="accounts-table-container">
            <Table<Integration>
              columns={columns}
              data={accounts}
              keyExtractor={(item) => item.id}
              emptyMessage="No integrations found. Click the + button to add one."
              addButton={
                <button 
                  className="add-account-button" 
                  onClick={handleOpenModal}
                >
                  <img src={plusIcon} width={18} height={18} alt="Add Account" />
                </button>
              }
            />
          </div>
        )}      </div>

      {/* Developer Tools Section */}
      {/* <div className="dev-tools-section">
        <h3>Developer Tools</h3>
        <div className="dev-tools-buttons">
          <button 
            className="test-button"
            onClick={() => navigate('/notion/test')}
          >
            Test Notion Integration
          </button>
        </div>
      </div> */}

      {/* Add Integration Modal */}
      <AddIntegration 
        isOpen={isModalOpen}
        onClose={() => {
          handleCloseModal();
          // Refresh the list when modal closes, in case we connected via OAuth
          loadIntegrations();
        }}
        onAddIntegration={handleAddIntegration}
      />
    </div>
  );
};

export default IntegrationsPage;
