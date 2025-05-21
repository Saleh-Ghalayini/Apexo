// types.ts

// Add a type for Integration with optional metadata
export interface Integration {
  id: string;
  provider: string;
  status: string;
  metadata?: {
    workspace_name?: string;
    [key: string]: unknown;
  };
  [key: string]: unknown;
}