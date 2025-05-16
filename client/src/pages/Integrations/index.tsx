import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import './Integrations.css';
import plusIcon from '../../assets/images/add_icon.png';
import arrowIcon from '../../assets/images/l_arrow_icon.png';

const IntegrationsPage: React.FC = () => {
  const navigate = useNavigate();
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [accounts, setAccounts] = useState<any[]>([]);
  const [isModalOpen, setIsModalOpen] = useState(false);

  useEffect(() => {
    setTimeout(() => {
      setLoading(false);
      setAccounts([{ id: '1', name: 'Test Integration' }]);
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
      <ul>
        {accounts.map(acc => (
          <li key={acc.id}>{acc.name}</li>
        ))}
      </ul>
      {isModalOpen && <div>Modal Placeholder</div>}
    </div>
  );
};

export default IntegrationsPage;