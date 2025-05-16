/**
 * Utility functions to validate Notion integration functionality
 */

import { IntegrationService } from '../services/integrationService';

/**
 * Types for validation results
 */
export interface ValidationResult {
  isValid: boolean;
  message: string;
  details?: Record<string, unknown>;
}

/**
 * Provides static methods to validate Notion integration configuration,
 * database access, and storage functionality.
 */
export class NotionValidation {
  /**
   * Validate that the Notion OAuth integration is configured properly.
   * Checks backend endpoint for OAuth client and redirect URI configuration.
   */
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
          details: data || {}
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

  /**
   * Validate that we can fetch databases from Notion.
   * Calls IntegrationService.getNotionDatabases and checks for results.
   */
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
          details: { databases: databases || [] }
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

  /**
   * Validate that we can save and retrieve Notion databases.
   * Attempts to save the first database and then retrieve it.
   */
  static async validateDatabaseStorage(): Promise<ValidationResult> {
    try {
      const databases = await IntegrationService.getNotionDatabases();

      if (!databases || databases.length === 0) {
        return {
          isValid: false,
          message: 'No databases available to test storage',
          details: {}
        };
      }

      const firstDb = databases[0];
      const saved = await IntegrationService.saveNotionDatabase(firstDb.id);

      if (!saved) {
        return {
          isValid: false,
          message: 'Failed to save database',
          details: { database: firstDb }
        };
      }

      const savedDatabases = await IntegrationService.getSavedNotionDatabases();

      if (savedDatabases && savedDatabases.length > 0) {
        const savedDb = savedDatabases.find(db => db.database_id === firstDb.id);

        if (savedDb) {
          return {
            isValid: true,
            message: 'Successfully saved and retrieved database',
            details: { savedDb }
          };
        } else {
          return {
            isValid: false,
            message: 'Database was saved but not found in retrieved list',
            details: { savedDatabases }
          };
        }
      } else {
        return {
          isValid: false,
          message: 'Failed to retrieve saved databases',
          details: { savedDatabases: savedDatabases || [] }
        };
      }
    } catch (error) {
      return {
        isValid: false,
        message: 'Error during database storage validation',
        details: { error }
      };
    }
  }
}