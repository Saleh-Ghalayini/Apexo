import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Login from './pages/Auth/Login';
import Signup from './pages/Auth/Signup';
import LandingPage from './pages/LandingPage/LandingPage';
import Dashboard from './pages/Dashboard';
import IntegrationsPage from './pages/Integrations';
import './index.css';

function App() {
  return (
    <>
      <Router>
        <Routes>          
          <Route path="/" element={<LandingPage />} />
          <Route path="/login" element={<Login />} />
          <Route path="/signup" element={<Signup />} />
          <Route path="/dashboard" element={<Dashboard />} />
          <Route path="/integrations" element={<IntegrationsPage />} />
        </Routes>
      </Router>
    </>
  )
}

export default App
