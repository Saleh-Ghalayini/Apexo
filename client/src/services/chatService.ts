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