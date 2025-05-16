// SlackSuccess.tsx: Called after Slack OAuth, sets localStorage and closes the window
import { useEffect } from 'react';

const SlackSuccess: React.FC = () => {
  useEffect(() => {
    localStorage.setItem('slack_auth_completed', 'true');
    setTimeout(() => {
      window.close();
    }, 500);
  }, []);


};

export default SlackSuccess;
