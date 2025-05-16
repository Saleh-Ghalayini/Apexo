import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Table from '../../components/Table';
import type { TableColumn } from '../../components/Table';
import './Integrations.css';
import plusIcon from '../../assets/images/add_icon.png';
import arrowIcon from '../../assets/images/l_arrow_icon.png';

interface Integration {
  id: string;
  name: string;
  email: string;
  type: string;
  status: string;
  linkingDate: string;
  provider: string;
}

const columns: TableColumn<Integration>[] = [
  {
    key: 'name',
    header: 'Name',
    render: (account) => <span>{account.name}</span>
  },
  {
    key: 'email',
    header: 'Email',
    render: (account) => <span>{account.email}</span>
  }
];

const IntegrationsPage: React.FC = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [accounts, setAccounts] = useState<Integration[]>([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    setTimeout(() => {
      setLoading(false);
      setAccounts([
        {
          id: '1',
          name: 'Test Integration',
          email: 'test@example.com',
          type: 'workspace',
          status: 'active',
          linkingDate: '2025-05-17',
          provider: 'slack'
        }
      ]);
    }, 1000);
  }, []);

  const handleGoBack = () => {
    navigate('/dashboard');
  };

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="integrations-container">
      <div className="integrations-header">
        <button className="back-button" onClick={handleGoBack}>
          <img src={arrowIcon} width={22} height={22} alt="Go Back" className="back-icon" />
        </button>
        <h1 className="header-title">Currently Linked Accounts</h1>
      </div>
      <button onClick={() => setIsModalOpen(true)}>
        <img src={plusIcon} width={18} height={18} alt="Add Account" />
      </button>
      <Table<Integration>
        columns={columns}
        data={accounts}
        keyExtractor={(item) => item.id}
        emptyMessage="No integrations found."
      />
      {isModalOpen && <div>Modal Placeholder</div>}
    </div>
  );
};

export default IntegrationsPage;