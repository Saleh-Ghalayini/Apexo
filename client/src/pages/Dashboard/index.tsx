import React, { useState } from 'react';

const Dashboard: React.FC = () => {
  const [messages, setMessages] = useState<string[]>([]);
  const [inputValue, setInputValue] = useState('');

  return (
    <div>
      <div>
        {messages.map((msg, idx) => (
          <div key={idx}>{msg}</div>
        ))}
      </div>
      <input
        value={inputValue}
        onChange={e => setInputValue(e.target.value)}
        placeholder="Type a message"
      />
    </div>
  );
};

export default Dashboard;