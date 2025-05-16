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
      try {
        const response = await apiRequest<{ success: boolean }>({
          url: '/api/v1/integrations/slack/connect',
          method: 'POST',
          data: { workspaceId }
        });
        return response.success;
      } catch (error) {
        console.error('Failed to connect to Slack:', error);
        return false;
      }
    },
    async sendMessage(channelId: string, message: string): Promise<boolean> {
      try {
        const response = await apiRequest<{ success: boolean }>({
          url: '/api/v1/integrations/slack/message',
          method: 'POST',
          data: { channelId, message }
        });
        return response.success;
      } catch (error) {
        console.error('Failed to send Slack message:', error);
        return false;
      }
    }
  },
  googleCalendar: {
    async createEvent(calendarId: string, event: GoogleCalendarEvent): Promise<boolean> {
      try {
        const response = await apiRequest<{ success: boolean }>({
          url: '/api/v1/integrations/gcalendar/event',
          method: 'POST',
          data: { calendarId, event }
        });
        return response.success;
      } catch (error) {
        console.error('Failed to create calendar event:', error);
        return false;
      }
    }
  },
  notion: {
    async connect(accessToken: string): Promise<boolean> {
      try {
        const response = await apiRequest<{ success: boolean }>({
          url: '/api/v1/integrations/notion/connect',
          method: 'POST',
          data: { accessToken }
        });
        return response.success;
      } catch (error) {
        console.error('Failed to connect to Notion:', error);
        return false;
      }
    },
    async createPage(parentPageId: string, content: Record<string, unknown>): Promise<boolean> {
      try {
        const response = await apiRequest<{ success: boolean }>({
          url: '/api/v1/integrations/notion/page',
          method: 'POST',
          data: { parentPageId, content }
        });
        return response.success;
      } catch (error) {
        console.error('Failed to create Notion page:', error);
        return false;
      }
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