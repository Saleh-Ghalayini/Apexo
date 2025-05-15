import React, { useState } from 'react';

const AIPromptPanel: React.FC = () => {
  const [prompt, setPrompt] = useState('');

  return (
    <div>
      <h2>AI Assistant for Notion</h2>
      <form>
        <textarea
          value={prompt}
          onChange={(e) => setPrompt(e.target.value)}
          placeholder="Type your prompt..."
        />
        <button type="submit">Submit</button>
      </form>
    </div>
  );
};

export default AIPromptPanel;