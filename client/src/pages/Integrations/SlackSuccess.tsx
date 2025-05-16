// SlackSuccess.tsx: Called after Slack OAuth, sets localStorage and closes the window
import { useEffect } from 'react';

const SlackSuccess: React.FC = () => {
  useEffect(() => {
    localStorage.setItem('slack_auth_completed', 'true');
    setTimeout(() => {
      window.close();
    }, 500);
  }, []);

  return (
    <div style={{ padding: 40, textAlign: 'center' }}>
      <h2>Slack Connected!</h2>
      <p>You can close this window and return to Apexo.</p>
    </div>
  );
};

export default SlackSuccess;
