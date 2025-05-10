import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Table from '../../components/Table';
import type { TableColumn } from '../../components/Table';
import './Integrations.css';
import plusIcon from '../../assets/images/add_icon.png';
import arrowIcon from '../../assets/images/l_arrow_icon.png';

interface IntegrationAccount {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler';
  status: 'active' | 'inactive';
  linkingDate: string;
  [key: string]: unknown;
}

const IntegrationsPage: React.FC = () => {
  const navigate = useNavigate();
  const [accounts, setAccounts] = useState<IntegrationAccount[]>([
    {
      id: '1',
      name: 'My Workspace',
      email: 'apex@gmail.com',
      type: 'workspace',
      status: 'active',
      linkingDate: '04-04-2025'
    },
    {
      id: '2',
      name: 'My Channel',
      email: 'apex@gmail.com',
      type: 'channel',
      status: 'inactive',
      linkingDate: '05-04-2025'
    },
    {
      id: '3',
      name: 'My Scheduler',
      email: 'apex@gmail.com',
      type: 'scheduler',
      status: 'inactive',
      linkingDate: '05-04-2025'
    }
  ]);

  const handleGoBack = () => {
    navigate('/dashboard');
  };

  const handleConnect = (accountId: string) => {
    setAccounts(accounts.map(account => 
      account.id === accountId ? { ...account, status: 'active' } : account
    ));
  };

  const handleDisconnect = (accountId: string) => {
    setAccounts(accounts.map(account => 
      account.id === accountId ? { ...account, status: 'inactive' } : account
    ));
  };

  const columns: TableColumn<IntegrationAccount>[] = [
    {
      key: 'account',
      header: 'Account',
      render: (account) => (
        <div className="account-cell">
          <div className="account-icon">
            {account.type === 'workspace' && '🏢'}
            {account.type === 'channel' && '📢'}
            {account.type === 'scheduler' && '📅'}
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
    {
      key: 'actions',
      header: 'Actions',
      render: (account) => (
        <div style={{ display: 'flex', justifyContent: 'center' }}>
          {account.status === 'active' ? (
            <button 
              className="action-button disconnect"
              onClick={() => handleDisconnect(account.id)}
            >
              Disconnect
            </button>
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
        <div className="accounts-table-container">
          <Table<IntegrationAccount>
            columns={columns}
            data={accounts}
            keyExtractor={(item) => item.id}
            addButton={
              <button className="add-account-button">
                <img src={plusIcon} width={18} height={18} alt="Add Account" />
              </button>
            }
          />
        </div>
      </div>
    </div>
  );
};

export default IntegrationsPage;