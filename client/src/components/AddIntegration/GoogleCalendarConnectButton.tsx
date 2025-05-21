import React from 'react';

interface GoogleCalendarConnectButtonProps {
  onSuccess?: () => void;
}

const GoogleCalendarConnectButton: React.FC<GoogleCalendarConnectButtonProps> = ({ onSuccess }) => {
  const handleConnect = () => {
    const token = localStorage.getItem('access_token');
    const url = token
      ? `http://localhost:8000/api/v1/google-calendar/redirect?jwt=${encodeURIComponent(token)}`
      : 'http://localhost:8000/api/v1/google-calendar/redirect';
    window.location.href = url;
    if (onSuccess) onSuccess();
  };

  return (
    <button className="modal-button primary" onClick={handleConnect}>
      Connect Google Calendar
    </button>
  );
};

export default GoogleCalendarConnectButton;
