import { useEffect, useState } from 'react';
import api from '../../services/api';

const NGROK_BASE = 'https://e5b9-93-126-210-243.ngrok-free.app';
const SLACK_REDIRECT_PATH = '/api/v1/integrations/slack/redirect';

export default function SlackConnectButton() {
  const [userId, setUserId] = useState<string | null>(null);

  useEffect(() => {
    api.get('/user')
      .then(res => {
        const id = res.data?.payload?.id || res.data?.id;
        if (id) setUserId(id.toString());
      })
      .catch(() => setUserId(null));
  }, []);

  const slackUrl = userId
    ? `${NGROK_BASE}${SLACK_REDIRECT_PATH}?user_id=${userId}`
    : `${NGROK_BASE}${SLACK_REDIRECT_PATH}`;

  return (
    <a href={slackUrl} target="_blank" rel="noopener noreferrer">
      <button type="button" className="btn btn-slack" disabled={!userId}>
        Connect Slack
      </button>
    </a>
  );
}