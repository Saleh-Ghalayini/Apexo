import React, { useState } from 'react';
import { NotionTest } from '../../utils/notionTest';

interface TestResult {
  name: string;
  status: 'pending' | 'success' | 'error';
  message: string;
  data?: any;
}

const NotionTestComponent: React.FC = () => {
  const [results, setResults] = useState<TestResult[]>([]);
  const [isRunning, setIsRunning] = useState(false);

  const addResult = (result: TestResult) => {
    setResults(prev => [...prev, result]);
  };

  const runAllTests = async () => {
    setIsRunning(true);
    setResults([]);

    // Test 1: OAuth Flow
    addResult({ name: 'OAuth Flow', status: 'pending', message: 'Testing OAuth connection...' });
    const oauthResult = await NotionTest.testOAuthFlow();
    addResult({
      name: 'OAuth Flow',
      status: oauthResult.success ? 'success' : 'error',
      message: oauthResult.message,
      data: oauthResult.integrationId
    });

    setIsRunning(false);
  };

  return (
    <div>
      <h2>Notion Integration Test</h2>
      <button onClick={runAllTests} disabled={isRunning}>
        {isRunning ? 'Running Tests...' : 'Run Tests'}
      </button>
      <div>
        {results.length === 0 && !isRunning && <div>No test results yet.</div>}
        {results.map((result, idx) => (
          <div key={idx}>
            <span>{result.name}</span>
            <span>{result.status}</span>
            <span>{result.message}</span>
            {result.data && <pre>{JSON.stringify(result.data, null, 2)}</pre>}
          </div>
        ))}
      </div>
    </div>
  );
};

export default NotionTestComponent;