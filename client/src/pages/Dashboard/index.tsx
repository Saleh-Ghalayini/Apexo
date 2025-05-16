import React, { useState, useRef, useEffect } from 'react';

interface ChatMessage {
  id: number;
  text: string;
  isUser: boolean;
  timestamp: Date;
}

const Sidebar = () => <div style={{ width: 200, background: '#eee' }}>Sidebar</div>;
const Message = ({ text, isUser }: { text: string; isUser: boolean }) => (
  <div style={{ textAlign: isUser ? 'right' : 'left' }}>{text}</div>
);

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
    <div style={{ display: 'flex' }}>
      <Sidebar />
      <div style={{ flex: 1 }}>
        <div>
          {messages.map((msg) => (
            <Message key={msg.id} text={msg.text} isUser={msg.isUser} />
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
    </div>
  );
};

export default Dashboard;