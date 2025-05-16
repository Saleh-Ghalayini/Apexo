import React, { useState, useRef, useEffect } from 'react';

interface ChatMessage {
  id: number;
  text: string;
  isUser: boolean;
  timestamp: Date;
}

const Dashboard: React.FC = () => {
  const [messages, setMessages] = useState<ChatMessage[]>([]);
  const [inputValue, setInputValue] = useState('');
  const messagesEndRef = useRef<HTMLDivElement>(null);

  const handleSend = () => {
    if (!inputValue.trim()) return;
    setMessages([
      ...messages,
      {
        id: Date.now(),
        text: inputValue,
        isUser: true,
        timestamp: new Date()
      }
    ]);
    setInputValue('');
  };

  useEffect(() => {
    messagesEndRef.current?.scrollIntoView({ behavior: 'smooth' });
  }, [messages]);

  return (
    <div>
      <div>
        {messages.map((msg) => (
          <div key={msg.id} style={{ textAlign: msg.isUser ? 'right' : 'left' }}>
            {msg.text}
          </div>
        ))}
        <div ref={messagesEndRef} />
      </div>
      <input
        value={inputValue}
        onChange={e => setInputValue(e.target.value)}
        placeholder="Type a message"
      />
      <button onClick={handleSend}>Send</button>
    </div>
  );
};

export default Dashboard;