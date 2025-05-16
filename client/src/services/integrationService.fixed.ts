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
    try {
      const response = await apiRequest<{ success: boolean, payload: { url: string } }>({
        url: `/integrations/connect/${providerId}`,
        method: 'POST',
      });
      return response.payload;
    } catch (error) {
      console.error('Failed to initiate integration connection:', error);
      throw error;
    }
  },
  async disconnect(integrationId: string): Promise<boolean> {
    try {
      const response = await apiRequest<{ success: boolean }>({
        url: `/integrations/${integrationId}`,
        method: 'DELETE',
      });
      return response.success;
    } catch (error) {
      console.error('Failed to disconnect integration:', error);
      return false;
    }
  },
  async updateStatus(integrationId: string, status: 'active' | 'inactive'): Promise<boolean> {
    try {
      const response = await apiRequest<{ success: boolean }>({
        url: `/integrations/${integrationId}/status`,
        method: 'PATCH',
        data: { status }
      });
      return response.success;
    } catch (error) {
      console.error('Failed to update integration status:', error);
      return false;
    }
  }
};