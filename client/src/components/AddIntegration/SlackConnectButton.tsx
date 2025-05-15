import React from 'react';

const NGROK_BASE = 'https://e5b9-93-126-210-243.ngrok-free.app';
const SLACK_REDIRECT_PATH = '/api/v1/integrations/slack/redirect';

export default function SlackConnectButton() {
  const slackUrl = `${NGROK_BASE}${SLACK_REDIRECT_PATH}`;

  return (
    <a href={slackUrl} target="_blank" rel="noopener noreferrer">
      <button type="button" className="btn btn-slack">
        Connect Slack
      </button>
    </a>
  );
}