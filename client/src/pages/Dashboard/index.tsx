import './Dashboard.css';
import './loading.css';
import Sidebar from '../../components/Sidebar';
import Message from '../../components/Message';
import addIcon from '../../assets/images/add_icon.png';
import userIcon from '../../assets/images/user_icon.png';
import wUserIcon from '../../assets/images/w_user_icon.png';
import React, { useState, useRef, useEffect } from 'react';
import arrowIcon from '../../assets/images/arrow_icon.png';
import logoutIcon from '../../assets/images/w_logout_icon.png';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../hooks/useAuth';
import { ChatService } from '../../services/chatService';
import type { ChatMessage as ApiChatMessage } from '../../services/chatService';

interface ChatMessage {
  id: number;
  text: string;
  isUser: boolean;
  timestamp: Date;
  metadata?: Record<string, unknown>;
}

const Dashboard: React.FC = () => {
  const navigate = useNavigate();
  const { user, logout } = useAuth();
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [inputValue, setInputValue] = useState('');
  const [showProfileDropdown, setShowProfileDropdown] = useState(false);
  const [profilePicture, setProfilePicture] = useState<string | null>(null);
  const [sessionId, setSessionIdState] = useState<string | null>(() => localStorage.getItem('current_session_id'));
  const [isLoading, setIsLoading] = useState(false);
  const messagesEndRef = useRef<HTMLDivElement>(null);
  const inputRef = useRef<HTMLTextAreaElement>(null);
  const dropdownRef = useRef<HTMLDivElement>(null);
  
  const setSessionId = (id: string | null) => {
    setSessionIdState(id);
    if (id) {
      localStorage.setItem('current_session_id', id);
    } else {
      localStorage.removeItem('current_session_id');
    }
  };

  useEffect(() => {
    scrollToBottom();
  }, [messages]);
  
  useEffect(() => {
    if (user?.avatar) {
      setProfilePicture(user.avatar);
    } else {
      const savedProfilePicture = localStorage.getItem('profilePicture');
      if (savedProfilePicture) {
        setProfilePicture(savedProfilePicture);
      }
    }
  }, [user]);

  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setShowProfileDropdown(false);
      }
    }

    document.addEventListener('mousedown', handleClickOutside);
    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, []);

  const scrollToBottom = () => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  };

  const handleInputChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    setInputValue(e.target.value);
    
    if (inputRef.current) {
      inputRef.current.style.height = 'auto';
      inputRef.current.style.height = `${Math.min(inputRef.current.scrollHeight, 150)}px`;
    }
  };

  const handleSendMessage = async () => {
    if (!inputValue.trim() || isLoading) return;

    setIsLoading(true);
    const userMessage: ChatMessage = {
      id: Date.now(),
      text: inputValue,
      isUser: true,
      timestamp: new Date()
    };

    const messageToSend = inputValue;
    setInputValue('');
    if (inputRef.current) inputRef.current.style.height = 'auto';

    try {
      if (!sessionId) {
        console.log('[handleSendMessage] Creating new session');
        const res = await ChatService.createSession(messageToSend);
        console.log('[handleSendMessage] Create session response:', res);
        
        if (res && res.session) {
          setSessionId(res.session.id);
          let newMessages: ChatMessage[] = [];
          if (Array.isArray(res.messages) && res.messages.length > 0) {
            newMessages = res.messages.map((msg: ApiChatMessage) => ({
              id: Number(msg.id || Date.now()),
              text: msg.content || '',
              isUser: user && msg.user_id === user?.id ? true : false,
              timestamp: new Date(msg.created_at || Date.now())
            }));
          } else if (res.user_message && res.ai_message) {
            newMessages = [
              {
                id: Number(res.user_message.id || Date.now()),
                text: res.user_message.content || '',
                isUser: true,
                timestamp: new Date(res.user_message.created_at || Date.now())
              },
              {
                id: Number(res.ai_message.id || Date.now()),
                text: res.ai_message.content || '',
                isUser: false,
                timestamp: new Date(res.ai_message.created_at || Date.now())
              }
            ];
          }
          setMessages(prev => [...prev, ...newMessages]);
        } else {
          console.error('[handleSendMessage] Invalid response from createSession:', res);
          setMessages(prev => [
            ...prev,
            {
              id: Date.now() + 3,
              text: 'Received an invalid response. Please try again.',
              isUser: false,
              timestamp: new Date()
            }
          ]);
        }
      } else {
        setMessages(prev => [...prev, userMessage]);
        const res = await ChatService.sendMessage(sessionId, messageToSend);
        // Check for direct file download in AI response
        if (res && res.ai_message && res.ai_message.content) {
          let aiContent = res.ai_message.content;
          let fileMeta: { base64: string; name?: string; mime?: string } | null = null;
          // Try to parse file info if present in metadata or content
          if ('metadata' in res.ai_message && res.ai_message.metadata) {
            try {
              const metaRaw = (res.ai_message as { metadata?: unknown }).metadata;
              const meta = typeof metaRaw === 'string' ? JSON.parse(metaRaw) : metaRaw;
              if (meta && typeof meta === 'object' && Array.isArray((meta as { tool_results?: unknown[] }).tool_results)) {
                for (const toolResult of (meta as { tool_results?: { file?: { base64: string; name?: string; mime?: string } }[] }).tool_results || []) {
                  if (toolResult && toolResult.file && toolResult.file.base64) {
                    fileMeta = toolResult.file;
                    break;
                  }
                }
              }
            } catch {
              // ignore
            }
          }
          if (!fileMeta && aiContent.includes('base64')) {
            try {
              const match = aiContent.match(/\{[^}]*base64[^}]*\}/);
              if (match) fileMeta = JSON.parse(match[0]);
            } catch (e) { /* ignore */ }
          }
          if (fileMeta && fileMeta.base64) {
            
            const byteCharacters = atob(fileMeta.base64);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
              byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: fileMeta.mime || 'application/octet-stream' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = fileMeta.name || 'report.pdf';
            document.body.appendChild(link);
            link.click();
            setTimeout(() => {
              window.URL.revokeObjectURL(link.href);
              link.remove();
            }, 1000);
            aiContent = 'The report got generated and downloaded on your machine.';
          }
          setMessages(prev => [
            ...prev,
            {
              id: Date.now(),
              text: aiContent,
              isUser: false,
              timestamp: new Date()
            }
          ]);
        } else if (res && typeof (res as object & { error?: string }).error === 'string') {
          setMessages(prev => [
            ...prev,
            {
              id: Date.now() + 3,
              text: ((res as { error?: string }).error) || 'Received an invalid response. Please try again.',
              isUser: false,
              timestamp: new Date()
            }
          ]);
        } else {
          setMessages(prev => [
            ...prev,
            {
              id: Date.now() + 3,
              text: 'Received an invalid response. Please try again.',
              isUser: false,
              timestamp: new Date()
            }
          ]);
        }
      }
    } catch (error) {
      console.error('[handleSendMessage] Error:', error);
      setMessages(prev => [
        ...prev,
        {
          id: Date.now() + 2,
          text: 'There was an error sending your message. Please try again.',
          isUser: false,
          timestamp: new Date()
        }
      ]);
    } finally {
      setIsLoading(false);
    }
  };

  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      handleSendMessage();
    }
  };

  const toggleProfileDropdown = () => {
    setShowProfileDropdown(!showProfileDropdown);
  };
  
  const handleLogout = async () => {
    await logout();
    navigate('/login');
  };

  const handleNewChat = () => {
    setSessionId(null);
    setMessages([]);
  };

  const handleSelectSession = async (selectedSessionId: string) => {
    if (sessionId !== selectedSessionId) {
      setSessionId(selectedSessionId);
      setIsLoading(true);
      
      try {
        console.log('[handleSelectSession] Fetching messages for session:', selectedSessionId);
        const apiMessages = await ChatService.getMessages(selectedSessionId);
        console.log('[handleSelectSession] Fetched messages:', apiMessages);
        
        if (Array.isArray(apiMessages) && apiMessages.length > 0) {
          setMessages(
            apiMessages.map((msg, idx) => {
              let isUser = false;
              if (user && typeof msg.user_id !== 'undefined') {
                isUser = msg.user_id === user.id;
              } else {
                isUser = idx % 2 === 0;
              }
              return {
                id: Number(msg.id || Date.now() + idx),
                text: msg.content || '(No content)',
                isUser,
                timestamp: new Date(msg.created_at || Date.now())
              };
            })
          );
        } else {
          console.log('[handleSelectSession] No messages found or invalid format');
          setMessages([]);
        }
      } catch (error) {
        console.error('[handleSelectSession] Error fetching messages:', error);
        setMessages([]);
      } finally {
        setIsLoading(false);
      }
    }
  };

  return (
    <div className="dashboard-container">
      <Sidebar onSelectSession={handleSelectSession} onNewChat={handleNewChat} />
      
      <div className="dashboard-content">
        <div className="dashboard-header">
          <div className="user-avatar-container" ref={dropdownRef}>
            <div className="user-avatar" onClick={toggleProfileDropdown}>
              {profilePicture ? (
                <img 
                  src={profilePicture} 
                  className="profile-picture" 
                  alt="Profile" 
                />
              ) : (
                <img src={userIcon} width='20px' height='20px' alt="User" />
              )}
            </div>
            {showProfileDropdown && (
              <div className="profile-dropdown">
                <div className="profile-header">
                  <div className="profile-name">{user?.name || 'User'}</div>
                  <div className="profile-email">{user?.email || 'user@example.com'}</div>
                </div>
                
                <div className="dropdown-section">
                  <div className="dropdown-item">
                    <div className="dropdown-icon">
                      <img src={wUserIcon} width='18px' height='18px' alt="Account" />
                    </div>
                    Account settings
                  </div>
                </div>
                
                <div className="dropdown-divider"></div>
                
                <div className="dropdown-section">
                  <div className="dropdown-item logout-item" onClick={handleLogout}>
                    <div className="dropdown-icon">
                      <img src={logoutIcon} width='16px' height='16px' alt="Logout" />
                    </div>
                    Logout
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
        
        <div className="dashboard-main">
          {messages.length === 0 ? (
            <h1 className="welcome-message">
              Welcome Back, <span className="username">{user?.name || 'User'}</span>
            </h1>
          ) : (
            <div className="messages-container">
              {messages.map((message) => (
                <Message 
                  key={message.id}
                  text={message.text}
                  isUser={message.isUser}
                  timestamp={message.timestamp}
                />
              ))}
              {isLoading && (
                <div className="message-container ai-message">
                  <div className="message-content">
                    <div className="message-text">
                      <div className="loading-dots">
                        <span className="dot"></span>
                        <span className="dot"></span>
                        <span className="dot"></span>
                      </div>
                    </div>
                  </div>
                </div>
              )}
              <div ref={messagesEndRef} />
            </div>
          )}
        </div>
        
        <div className="dashboard-chat">
          <div className="chat-input-container">
            <img src={addIcon} alt='upload' />
            <textarea
              ref={inputRef}
              value={inputValue}
              onChange={handleInputChange}
              onKeyDown={handleKeyDown}
              placeholder="Type a command or ask Apexo for assistance..."
              className="chat-input"
              rows={1}
              disabled={isLoading}
            />
            <button 
              className="send-button" 
              onClick={handleSendMessage} 
              disabled={isLoading || !inputValue.trim()}
            >
              <img src={arrowIcon} alt='send' />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;