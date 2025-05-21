import { Routes, Route } from 'react-router-dom';
import Login from './pages/Auth/Login';
import Signup from './pages/Auth/Signup';
import LandingPage from './pages/LandingPage/LandingPage';
import Dashboard from './pages/Dashboard';
import IntegrationsPage from './pages/Integrations';
import SlackSuccess from './pages/Integrations/SlackSuccess';
import NotionSuccess from './pages/Integrations/NotionSuccess';
import NotionTestComponent from './components/NotionTest';
import PrivateRoute from './components/PrivateRoute';
import AuthRedirect from './components/AuthRedirect';
import GoogleCalendarCallback from './pages/Integrations/GoogleCalendarCallback';
import NotFound from './pages/NotFound';

const AppRoutes = () => (
  <Routes>
    <Route path="/" element={<LandingPage />} />
    <Route 
      path="/login" 
      element={
        <AuthRedirect>
          <Login />
        </AuthRedirect>
      } 
    />
    <Route 
      path="/signup" 
      element={
        <AuthRedirect>
          <Signup />
        </AuthRedirect>
      } 
    />
    <Route 
      path="/dashboard" 
      element={
        <PrivateRoute>
          <Dashboard />
        </PrivateRoute>
      } 
    />
    <Route 
      path="/integrations" 
      element={
        <PrivateRoute>
          <IntegrationsPage />
        </PrivateRoute>
      } 
    />
    <Route path="/integrations/slack/success" element={<SlackSuccess />} />
    <Route path="/integrations/notion/success" element={<NotionSuccess />} />
    <Route 
      path="/notion/test" 
      element={
        <PrivateRoute>
          <NotionTestComponent />
        </PrivateRoute>
      } 
    />
    <Route path="/integrations/google-calendar/callback" element={<GoogleCalendarCallback />} />
    <Route path="*" element={<NotFound />} />
  </Routes>
);

export default AppRoutes;
