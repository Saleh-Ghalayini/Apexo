import { apiRequest } from '../utils/apiRequest';

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
    dateTime: string;
    timeZone?: string;
  };
  end: {
    dateTime: string;
    timeZone?: string;
  };
  attendees?: Array<{ email: string }>;
  [key: string]: unknown;
}

export const IntegrationProviders = {
  slack: {
    async connect(workspaceId: string): Promise<boolean> {
      return false;
    },
    async sendMessage(channelId: string, message: string): Promise<boolean> {
      return false;
    }
  },
  googleCalendar: {
    async createEvent(calendarId: string, event: GoogleCalendarEvent): Promise<boolean> {
      return false;
    }
  },
  notion: {
    async connect(accessToken: string): Promise<boolean> {
      return false;
    },
    async createPage(parentPageId: string, content: Record<string, unknown>): Promise<boolean> {
      return false;
    }
  },
  email: {
    async connect(config: EmailConfig): Promise<boolean> {
      return false;
    },
    async sendEmail(to: string, subject: string, body: string): Promise<boolean> {
      return false;
    }
  }
};