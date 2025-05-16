import './Message.css';
import React from 'react';
import { cleanAIResponse } from '../../utils/cleanAIResponse';

export interface MessageProps {
  text: string;
  isUser: boolean;
  timestamp?: Date;
}

const Message: React.FC<MessageProps> = ({ text, isUser, timestamp = new Date() }) => {
  const formattedTime = timestamp.toLocaleTimeString([], { 
    hour: '2-digit', 
    minute: '2-digit' 
  });
  // If the message is from the user, we don't need to clean it
  const displayText = isUser ? text : cleanAIResponse(text);
  return (
    <div className={`message-container ${isUser ? 'user-message' : 'ai-message'}`}>
      <div className="message-content">
        <div className="message-text">{displayText}</div>
        <div className="message-time">{formattedTime}</div>
      </div>
    </div>
  );
};

export default Message;