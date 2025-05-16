import { apiRequest } from '../utils/apiRequest';

export interface IntegrationProvider {
  id: string;
  name: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  description: string;
  iconUrl: string;
}