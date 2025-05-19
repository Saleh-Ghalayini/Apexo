import React from 'react';
import { useNavigate } from 'react-router-dom';
import './NotFound.css';

const NotFound: React.FC = () => {
  const navigate = useNavigate();

  return (
    <div className="notfound-container">
      <div className="notfound-content">
        <h1 className="notfound-title">404</h1>
        <h2 className="notfound-subtitle">Page Not Found</h2>
        <p className="notfound-message">Sorry, the page you are looking for does not exist or has been moved.</p>
        <button className="notfound-btn" onClick={() => navigate(-1)}>Go Back</button>
      </div>
    </div>
  );
};

export default NotFound;
