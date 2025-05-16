import { apiRequest } from '../utils/apiRequest';

/**
 * Represents an integration provider (e.g., Notion, Slack, etc.)
 */
export interface IntegrationProvider {
  id: string;
  name: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  description: string;
  iconUrl: string;
}

/**
 * Represents a connected integration instance.
 */
export interface Integration {
  id: string;
  name: string;
  email: string;
  type: 'workspace' | 'channel' | 'scheduler' | 'Email' | 'Other';
  status: 'active' | 'inactive';
  linkingDate: string;
  provider: string;
}

export type NotionPropertyType =
  | 'title'
  | 'rich_text'
  | 'number'
  | 'select'
  | 'multi_select'
  | 'date'
  | 'checkbox'
  | 'url'
  | 'email'
  | 'phone_number'
  | 'formula'
  | 'relation'
  | 'rollup'
  | 'created_time'
  | 'created_by'
  | 'last_edited_time'
  | 'last_edited_by'
  | 'files'
  | 'people';

export interface NotionProperty {
  id: string;
  type: NotionPropertyType;
  name: string;
  [key: string]: unknown;
}

export interface NotionDatabase {
  id: string;
  title: string;
  description?: string;
  url?: string;
  properties?: Record<string, NotionProperty>;
  created_time?: string;
  last_edited_time?: string;
}

export interface NotionPage {
  id: string;
  properties: Record<string, unknown>;
  url?: string;
}

export interface SavedNotionDatabase {
  id: number;
  integration_id: number;
  data_type: string;
  external_id: string;
  name: string;
  description?: string;
  data: Record<string, unknown>;
  is_active: boolean;
  created_at: string;
  updated_at: string;
}

export const IntegrationService = {
  /** Get all available integration providers */
  async getProviders(): Promise<IntegrationProvider[]> {
    try {
      const response = await apiRequest<{ success: boolean; payload: IntegrationProvider[] }>({
        url: '/integrations/providers',
        method: 'GET',
      });
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch integration providers:', error);
      return [];
    }
  },

  /** Get all connected integrations for current user */
  async getIntegrations(): Promise<Integration[]> {
    try {
      const response = await apiRequest<{ success: boolean; payload: Integration[] }>({
        url: '/integrations',
        method: 'GET',
      });
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch integrations:', error);
      return [];
    }
  },

  /** Connect an integration and get the redirect URL */
  async connect(providerId: string): Promise<{ url: string }> {
    try {
      const response = await apiRequest<{ success: boolean; payload: { url: string } }>({
        url: `/integrations/connect/${providerId}`,
        method: 'POST',
      });
      if (!response.success || !response.payload?.url) {
        throw new Error('Failed to get integration connect URL');
      }
      return response.payload;
    } catch (error) {
      console.error('Failed to initiate integration connection:', error);
      throw error instanceof Error ? error : new Error('Unknown error during integration connect');
    }
  },

  /** Disconnect an integration */
  async disconnect(integrationId: string): Promise<boolean> {
    try {
      const response = await apiRequest<{ success: boolean }>({
        url: `/integrations/${integrationId}`,
        method: 'DELETE',
      });
      return !!response.success;
    } catch (error) {
      console.error('Failed to disconnect integration:', error);
      return false;
    }
  },

  /** Update integration status */
  async updateStatus(integrationId: string, status: 'active' | 'inactive'): Promise<boolean> {
    try {
      const response = await apiRequest<{ success: boolean }>({
        url: `/integrations/${integrationId}/status`,
        method: 'PATCH',
        data: { status },
      });
      return !!response.success;
    } catch (error) {
      console.error('Failed to update integration status:', error);
      return false;
    }
  },

  // ----- Notion specific methods -----

  /** Get all Notion databases */
  async getNotionDatabases(): Promise<NotionDatabase[]> {
    try {
      const response = await apiRequest<{ success: boolean; payload: NotionDatabase[]; message?: string }>({
        url: '/notion/databases',
        method: 'GET',
      });
      if (!response.success) {
        const msg = response.message || 'Unknown error';
        console.error('Failed to fetch Notion databases:', msg);
        throw new Error(msg);
      }
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch Notion databases:', error);
      throw error instanceof Error ? error : new Error('Unknown error during getNotionDatabases');
    }
  },

  /** Get a specific Notion database */
  async getNotionDatabase(databaseId: string): Promise<NotionDatabase | null> {
    try {
      const response = await apiRequest<{ success: boolean; payload: NotionDatabase }>({
        url: `/notion/databases/${databaseId}`,
        method: 'GET',
      });
      return response.payload || null;
    } catch (error) {
      console.error(`Failed to fetch Notion database ${databaseId}:`, error);
      return null;
    }
  },

  /** Query a Notion database */
  async queryNotionDatabase(databaseId: string, query: Record<string, unknown> = {}): Promise<NotionPage[]> {
    try {
      const response = await apiRequest<{ success: boolean; payload: NotionPage[] }>({
        url: `/notion/databases/${databaseId}/query`,
        method: 'POST',
        data: query,
      });
      return response.payload || [];
    } catch (error) {
      console.error(`Failed to query Notion database ${databaseId}:`, error);
      return [];
    }
  },

  /** Create a page in a Notion database */
  async createNotionPage(databaseId: string, properties: Record<string, unknown>, children: Record<string, unknown>[] = []): Promise<NotionPage | null> {
    try {
      const response = await apiRequest<{ success: boolean; payload: NotionPage }>({
        url: `/notion/databases/${databaseId}/pages`,
        method: 'POST',
        data: { properties, children },
      });
      return response.payload || null;
    } catch (error) {
      console.error(`Failed to create page in Notion database ${databaseId}:`, error);
      return null;
    }
  },

  /** Update a Notion page */
  async updateNotionPage(pageId: string, properties: Record<string, unknown>): Promise<NotionPage | null> {
    try {
      const response = await apiRequest<{ success: boolean; payload: NotionPage }>({
        url: `/notion/pages/${pageId}`,
        method: 'PATCH',
        data: { properties },
      });
      return response.payload || null;
    } catch (error) {
      console.error(`Failed to update Notion page ${pageId}:`, error);
      return null;
    }
  },

  /** Save a Notion database reference in our system */
  async saveNotionDatabase(databaseId: string): Promise<SavedNotionDatabase> {
    try {
      const response = await apiRequest<{ success: boolean; payload: SavedNotionDatabase }>({
        url: `/notion/databases/${databaseId}/save`,
        method: 'POST',
      });
      if (!response.success || !response.payload) {
        throw new Error('Failed to save Notion database');
      }
      return response.payload;
    } catch (error) {
      console.error(`Failed to save Notion database ${databaseId}:`, error);
      throw error instanceof Error ? error : new Error('Unknown error during saveNotionDatabase');
    }
  },

  /** Get saved Notion databases */
  async getSavedNotionDatabases(): Promise<SavedNotionDatabase[]> {
    try {
      const response = await apiRequest<{ success: boolean; payload: SavedNotionDatabase[] }>({
        url: '/notion/saved-databases',
        method: 'GET',
      });
      return response.payload || [];
    } catch (error) {
      console.error('Failed to fetch saved Notion databases:', error);
      return [];
    }
  },
};
