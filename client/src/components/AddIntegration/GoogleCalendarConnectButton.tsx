import React from 'react';

const GoogleCalendarConnectButton: React.FC = () => {
  const handleConnect = () => {
    const token = localStorage.getItem('access_token');
    const url = token
      ? `http://localhost:8000/api/v1/google-calendar/redirect?jwt=${encodeURIComponent(token)}`
      : 'http://localhost:8000/api/v1/google-calendar/redirect';
    window.location.href = url;
  };

  return (
    <button className="modal-button primary" onClick={handleConnect}>
      Connect Google Calendar
    </button>
  );
};

export default GoogleCalendarConnectButton;
