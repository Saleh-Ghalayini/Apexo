import { apiRequest } from '../utils/apiRequest';

export interface IntegrationProvider {
  id: string;
  name: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  description: string;
  iconUrl: string;
}

export interface Integration {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  status: 'active' | 'inactive';
  linkingDate: string;
  provider: string;
}

export const IntegrationService = {
  async getProviders(): Promise<IntegrationProvider[]> {
    try {
      const response = await apiRequest<{ success: boolean, payload: IntegrationProvider[] }>({
        url: '/integrations/providers',
        method: 'GET',
      });
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch integration providers:', error);
      return [];
    }
  },
  async getIntegrations(): Promise<Integration[]> {
    try {
      const response = await apiRequest<{ success: boolean, payload: Integration[] }>({
        url: '/integrations',
        method: 'GET',
      });
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch integrations:', error);
      return [];
    }
  },
  async connect(providerId: string): Promise<{ url: string }> {
    return { url: '' };
  },
  async disconnect(integrationId: string): Promise<boolean> {
    return false;
  },
  async updateStatus(integrationId: string, status: 'active' | 'inactive'): Promise<boolean> {
    return false;
  }
};