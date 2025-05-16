import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import LandingPage from './pages/LandingPage/LandingPage';
import Login from './pages/Auth/Login';
import Signup from './pages/Auth/Signup';
import Dashboard from './pages/Dashboard';
import IntegrationsPage from './pages/Integrations';
import SlackSuccess from './pages/Integrations/SlackSuccess';
import NotionSuccess from './pages/Integrations/NotionSuccess';
import NotionDatabasesPage from './pages/NotionDatabases';
import NotionTestComponent from './components/NotionTest';
import { AuthProvider } from './context/AuthContext';

function App() {
  return (
    <AuthProvider>
      <Router>
        <Routes>
          <Route path="/" element={<LandingPage />} />
          <Route path="/login" element={<Login />} />
          <Route path="/signup" element={<Signup />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/integrations" element={<IntegrationsPage />} />
          <Route path="/integrations/slack/success" element={<SlackSuccess />} />
          <Route path="/integrations/notion/success" element={<NotionSuccess />} />
          <Route path="/notion/databases" element={<NotionDatabasesPage />} />
          <Route path="/notion/test" element={<NotionTestComponent />} />
        </Routes>
      </Router>
    </AuthProvider>
  );
}

export default App;