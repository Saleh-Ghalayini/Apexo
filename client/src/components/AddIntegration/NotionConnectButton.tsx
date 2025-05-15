import { useState } from 'react';

interface NotionConnectButtonProps {
  onSuccess?: () => void;
}

export default function NotionConnectButton({ onSuccess }: NotionConnectButtonProps) {
  return (
    <button type="button" className="modal-button primary">
      Connect Notion
    </button>
  );
}