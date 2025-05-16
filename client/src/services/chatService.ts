import api from './api';

// Types
export interface ChatSession {
  id: string;
  title: string;
  created_at: string;
  updated_at: string;
}

export interface ChatMessage {
  id: string;
  session_id: string;
  user_id: number;
  content: string;
  created_at: string;
}

export interface CreateSessionResponse {
  session: ChatSession;
  user_message?: ChatMessage;
  ai_message?: ChatMessage;
  messages?: ChatMessage[];
}

export interface SendMessageResponse {
  session: ChatSession;
  user_message: ChatMessage;
  ai_message: ChatMessage;
}

// API response wrapper structure
interface ApiResponse<T> {
  success: boolean;
  payload: T;
}

// Service methods
export const ChatService = {
  async createSession(firstMessage: string) {
    const token = localStorage.getItem('auth_token');
    const endpoint = '/chat/sessions';
    try {
      const response = await api.post<ApiResponse<CreateSessionResponse> | CreateSessionResponse>(
        endpoint,
        { initial_message: firstMessage },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      if (response.data && 'success' in response.data && 'payload' in response.data) {
        return response.data.payload;
      }
      return response.data as CreateSessionResponse;
    } catch (error) {
      throw error;
    }
  },
  async sendMessage(sessionId: string, message: string) {
    const token = localStorage.getItem('auth_token');
    const endpoint = `/chat/sessions/${sessionId}/messages`;
    try {
      const response = await api.post<ApiResponse<SendMessageResponse> | SendMessageResponse>(
        endpoint,
        { message },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      if (response.data && 'success' in response.data && 'payload' in response.data) {
        return response.data.payload;
      }
      return response.data as SendMessageResponse;
    } catch (error) {
      throw error;
    }
  },
  async getSessions() {
    // TODO: Implement
  },
  async getMessages(sessionId: string) {
    // TODO: Implement
  },
};