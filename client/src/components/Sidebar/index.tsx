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

  const groupSessionsByDate = (sessions: ChatSession[]) => {
    if (!Array.isArray(sessions)) return {};
    const groups: { [key: string]: ChatSession[] } = {};
    const today = new Date().toDateString();
    const yesterday = new Date(Date.now() - 86400000).toDateString();
    sessions.forEach(session => {
      const sessionDate = new Date(session.created_at).toDateString();
      let group = 'Previous 7 Days';
      if (sessionDate === today) group = 'Today';
      else if (sessionDate === yesterday) group = 'Yesterday';
      if (!groups[group]) groups[group] = [];
      groups[group].push(session);
    });
    return groups;
  };

  const grouped = groupSessionsByDate(sessions);

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
          {Object.entries(grouped).map(([date, chats]) => (
            <div key={date}>
              <div>{date}</div>
              {chats.map((chat) => (
                <button key={chat.id} onClick={() => onSelectSession?.(chat.id)}>
                  {chat.title}
                </button>
              ))}
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default Sidebar;