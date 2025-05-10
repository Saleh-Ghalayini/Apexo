import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import './Integrations.css';

interface IntegrationAccount {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler';
  status: 'active' | 'inactive';
  linkingDate: string;
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

  return (
    <div className="integrations-container">
      <div className="integrations-header">
        <button className="back-button" onClick={handleGoBack}>
          ←
        </button>
        <h1 className="header-title">Currently Linked Accounts</h1>
      </div>
      
      <div className="integrations-content">
        <div className="accounts-table-container">
          <table className="accounts-table">
            <thead>
              <tr>
                <th>Account</th>
                <th>Status</th>
                <th>Actions</th>
                <th>Linking Date</th>
              </tr>
            </thead>
            <tbody>
              {accounts.map((account) => (
                <tr key={account.id}>
                  <td>
                    <div>{account.name}</div>
                    <div>{account.email}</div>
                  </td>
                  <td>{account.status}</td>
                  <td>
                    {account.status === 'active' ? (
                      <button onClick={() => handleDisconnect(account.id)}>
                        Disconnect
                      </button>
                    ) : (
                      <button onClick={() => handleConnect(account.id)}>
                        Connect
                      </button>
                    )}
                  </td>
                  <td>{account.linkingDate}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

export default IntegrationsPage;