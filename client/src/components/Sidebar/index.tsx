import React, { useState, useEffect, useCallback } from 'react';
import apexoLogo from '../../assets/images/apexo_logo.svg';
import addIcon from '../../assets/images/add_icon.png';
import { ChatService } from '../../services/chatService';
import type { ChatSession } from '../../services/chatService';

interface SidebarProps {
  onSelectSession?: (sessionId: string) => void;
  onNewChat?: () => void;
}

const Sidebar: React.FC<SidebarProps> = ({ onSelectSession, onNewChat }) => {
  const [isExpanded, setIsExpanded] = useState(false);
  const [sessions, setSessions] = useState<ChatSession[]>([]);

  const fetchSessions = useCallback(async () => {
    try {
      const data = await ChatService.getSessions();
      setSessions(Array.isArray(data) ? data : []);
    } catch {
      setSessions([]);
    }
  }, []);

  useEffect(() => {
    fetchSessions();
  }, [fetchSessions]);

  return (
    <div className={`sidebar ${isExpanded ? 'expanded' : ''}`}>
      <button onClick={() => setIsExpanded(!isExpanded)}>
        {isExpanded ? 'Collapse' : 'Expand'}
      </button>
      {isExpanded && (
        <div>
          <img src={apexoLogo} alt="Apexo Logo" />
          <button onClick={onNewChat}>
            <img src={addIcon} alt="New Chat" />
            New Chat
          </button>
          <ul>
            {sessions.map((session) => (
              <li key={session.id}>
                <button onClick={() => onSelectSession?.(session.id)}>
                  {session.title}
                </button>
              </li>
            ))}
          </ul>
        </div>
      )}
    </div>
  );
};

export default Sidebar;