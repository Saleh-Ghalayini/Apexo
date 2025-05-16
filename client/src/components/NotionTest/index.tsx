import React, { useState } from 'react';

interface TestResult {
  name: string;
  status: 'pending' | 'success' | 'error';
  message: string;
  data?: any;
}

const NotionTestComponent: React.FC = () => {
  const [results, setResults] = useState<TestResult[]>([]);
  const [isRunning, setIsRunning] = useState(false);

  return (
    <div>
      <h2>Notion Integration Test</h2>
      <button disabled={isRunning}>{isRunning ? 'Running Tests...' : 'Run Tests'}</button>
      <div>
        {results.length === 0 && !isRunning && <div>No test results yet.</div>}
        {results.map((result, idx) => (
          <div key={idx}>
            <span>{result.name}</span>
            <span>{result.status}</span>
            <span>{result.message}</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default NotionTestComponent;