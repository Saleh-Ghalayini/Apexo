import React, { useState } from 'react';
import './NotionTest.css';

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

    // Test 2: Databases
    addResult({ name: 'Fetch Databases', status: 'pending', message: 'Fetching Notion databases...' });
    const databasesResult = await NotionTest.testDatabasesFetch();
    addResult({
      name: 'Fetch Databases',
      status: databasesResult.success ? 'success' : 'error',
      message: databasesResult.message,
      data: databasesResult.databases
    });

    // Test 3: Save Database (only if we have databases)
    if (databasesResult.success && databasesResult.databases && databasesResult.databases.length > 0) {
      const firstDbId = databasesResult.databases[0].id;
      addResult({ name: 'Save Database', status: 'pending', message: `Saving database ${firstDbId.substring(0, 8)}...` });
      const saveResult = await NotionTest.testSaveDatabase(firstDbId);
      addResult({
        name: 'Save Database',
        status: saveResult.success ? 'success' : 'error',
        message: saveResult.message,
        data: saveResult.savedDatabase
      });
    }

    setIsRunning(false);
  };

  return (
    <div className="notion-test-container">
      <div className="notion-test-header">
        <h2>Notion Integration Test</h2>
        <button
          onClick={runAllTests}
          disabled={isRunning}
          className="notion-test-button"
        >
          {isRunning ? 'Running Tests...' : 'Run Tests'}
        </button>
      </div>
      <div className="notion-test-results">
        {results.length === 0 && !isRunning && (
          <div className="notion-test-empty">
            No test results yet. Click "Run Tests" to start testing.
          </div>
        )}
        {results.map((result, index) => (
          <div
            key={`${result.name}-${index}`}
            className={`notion-test-result ${result.status}`}
          >
            <div className="test-result-header">
              <span className="test-name">{result.name}</span>
              <span className="test-status">
                {result.status === 'pending' && '⏳'}
                {result.status === 'success' && '✅'}
                {result.status === 'error' && '❌'}
              </span>
            </div>
            <div className="test-message">{result.message}</div>
            {result.data && (
              <div className="test-data">
                <pre>{JSON.stringify(result.data, null, 2)}</pre>
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
};

export default NotionTestComponent;