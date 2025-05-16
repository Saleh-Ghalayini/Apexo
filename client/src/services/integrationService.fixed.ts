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
    return [];
  },
  async getIntegrations(): Promise<Integration[]> {
    return [];
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