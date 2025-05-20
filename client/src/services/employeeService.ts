import api from './api';

export interface Employee {
  id: number;
  name: string;
  email: string;
  role: string;
  job_title?: string;
  department?: string;
  phone?: string;
  avatar?: string;
  company_id: number;
  created_at: string;
  updated_at: string;
}

export const EmployeeService = {

  async getAllEmployees(): Promise<Employee[]> {
    const token = localStorage.getItem('auth_token');
    const response = await api.get<{ success: boolean; payload: Employee[] }>(
      '/employees',
      { headers: { Authorization: `Bearer ${token}` } }
    );
    if (response.data && response.data.success && response.data.payload) {
      return response.data.payload;
    }
    return [];
  },


  async searchEmployeesByName(name: string): Promise<Employee[]> {
    const token = localStorage.getItem('auth_token');
    const response = await api.get<{ success: boolean; payload: Employee[] }>(
      `/employees/search?name=${encodeURIComponent(name)}`,
      { headers: { Authorization: `Bearer ${token}` } }
    );
    if (response.data && response.data.success && response.data.payload) {
      return response.data.payload;
    }
    return [];
  },


  async getEmployeeById(id: number): Promise<Employee | null> {
    const token = localStorage.getItem('auth_token');
    const response = await api.get<{ success: boolean; payload: Employee }>(
      `/employees/${id}`,
      { headers: { Authorization: `Bearer ${token}` } }
    );
    if (response.data && response.data.success && response.data.payload) {
      return response.data.payload;
    }
    return null;
  },
};
