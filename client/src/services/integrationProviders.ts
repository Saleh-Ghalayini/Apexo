import { apiRequest } from '../utils/apiRequest';

// Specific provider types
export const ProviderType = {
  Slack: 'workspace',
  Notion: 'channel',
  GoogleCalendar: 'scheduler',
  Email: 'Email'
} as const;

export type ProviderType = typeof ProviderType[keyof typeof ProviderType];

export interface SlackIntegrationConfig {
  workspaceId: string;
  channelId?: string;
  botToken?: string;
}

export interface NotionIntegrationConfig {
  workspaceId: string;
  pageId?: string;
  accessToken?: string;
}

export interface GoogleCalendarConfig {
  calendarId: string;
  accessToken?: string;
  refreshToken?: string;
}

export interface EmailConfig {
  smtpServer: string;
  port: number;
  useSsl: boolean;
  username: string;
}

export interface GoogleCalendarEvent {
  summary: string;
  description?: string;
  start: {
    dateTime: string; // ISO string
    timeZone?: string;
  };
  end: {
    dateTime: string; // ISO string
    timeZone?: string;
  };
  attendees?: Array<{ email: string }>;
  [key: string]: unknown; // Add additional fields as needed
}