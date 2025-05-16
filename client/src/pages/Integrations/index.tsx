import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import Table from '../../components/Table';
import type { TableColumn } from '../../components/Table';
import './Integrations.css';
import plusIcon from '../../assets/images/add_icon.png';
import arrowIcon from '../../assets/images/l_arrow_icon.png';
import { IntegrationService } from '../../services/integrationService';
import Loading from '../../components/Loading';

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
    loadIntegrations();
  }, []);

  const loadIntegrations = async () => {
    try {
      setLoading(true);
      const fetchedIntegrations = await IntegrationService.getIntegrations();
      setAccounts(fetchedIntegrations);
      setError(null);
    } catch (err) {
      setError('Failed to load integrations.');
    } finally {
      setLoading(false);
    }
  };

  const handleGoBack = () => {
    navigate('/dashboard');
  };

  if (loading) return <Loading />;
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