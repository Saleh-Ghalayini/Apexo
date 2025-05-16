/**
 * Utility functions to validate Notion integration functionality
 */

import { IntegrationService } from '../services/integrationService';

export interface ValidationResult {
  isValid: boolean;
  message: string;
  details?: Record<string, unknown>;
}

export class NotionValidation {
  static async validateOAuthConfig(): Promise<ValidationResult> {
    try {
      const response = await fetch('/api/integrations/notion/authorize/info');
      const data = await response.json();

      if (data.success) {
        return {
          isValid: true,
          message: 'Notion OAuth configuration is valid',
          details: {
            clientId: data.clientIdConfigured,
            redirectUri: data.redirectUriConfigured
          }
        };
      } else {
        return {
          isValid: false,
          message: 'Notion OAuth configuration is incomplete or invalid',
          details: data
        };
      }
    } catch (error) {
      return {
        isValid: false,
        message: 'Failed to validate Notion OAuth configuration',
        details: { error }
      };
    }
  }

  static async validateDatabaseAccess(): Promise<ValidationResult> {
    try {
      const databases = await IntegrationService.getNotionDatabases();

      if (databases && databases.length > 0) {
        return {
          isValid: true,
          message: `Successfully fetched ${databases.length} databases from Notion`,
          details: { count: databases.length }
        };
      } else {
        return {
          isValid: false,
          message: 'No databases found in the connected Notion workspace',
          details: { databases }
        };
      }
    } catch (error) {
      return {
        isValid: false,
        message: 'Failed to fetch databases from Notion',
        details: { error }
      };
    }
  }

  static async validateDatabaseStorage(): Promise<ValidationResult> {
    return { isValid: false, message: 'Not implemented' };
  }
}