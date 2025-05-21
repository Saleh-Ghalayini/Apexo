import React, { useState, useEffect } from 'react';
import { useAuth } from '../../hooks/useAuth';
import api from '../../services/api';
import './EmployeeManagement.css';

interface Employee {
  id: number;
  name: string;
  email: string;
  role: string;
  job_title?: string;
  department?: string;
  phone?: string;
  avatar?: string;
}

const EmployeeManagement: React.FC = () => {
  const { user } = useAuth();
  const [employees, setEmployees] = useState<Employee[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [selectedEmployee, setSelectedEmployee] = useState<Employee | null>(null);

  // Load employees on component mount
  useEffect(() => {
    fetchEmployees();
  }, []);

  // Fetch all employees in the company
  const fetchEmployees = async () => {
    setLoading(true);
    setError('');
    
    try {
      const response = await api.get('/employees');
      setEmployees(response.data.data);
    } catch (err) {
      setError('Failed to load employees. Please try again.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Search employees by name
  const searchEmployees = async () => {
    if (!searchQuery.trim()) {
      fetchEmployees();
      return;
    }

    setLoading(true);
    setError('');
    
    try {
      const response = await api.get(`/employees/search?name=${searchQuery}`);
      setEmployees(response.data.data);
    } catch (err) {
      setError('Search failed. Please try again.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Get details for a specific employee
  const getEmployeeDetails = async (id: number) => {
    setLoading(true);
    setError('');
    
    try {
      const response = await api.get(`/employees/${id}`);
      setSelectedEmployee(response.data.data);
    } catch (err) {
      setError('Failed to load employee details. Please try again.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Handle search input changes
  const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchQuery(e.target.value);
  };

  // Handle search form submission
  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    searchEmployees();
  };

  // Clear search and show all employees
  const clearSearch = () => {
    setSearchQuery('');
    fetchEmployees();
  };

  // Close employee details panel
  const closeDetails = () => {
    setSelectedEmployee(null);
  };

  // Only HR users should access this page
  if (user?.role !== 'hr') {
    return (
      <div className="unauthorized-message">
        <h2>Unauthorized Access</h2>
        <p>Only HR personnel can access the employee management system.</p>
      </div>
    );
  }

  return (
    <div className="employee-management-container">
      <h1>Employee Management</h1>
      
      {/* Search form */}
      <div className="search-section">
        <form onSubmit={handleSearchSubmit}>
          <input
            type="text"
            placeholder="Search employees by name..."
            value={searchQuery}
            onChange={handleSearchChange}
          />
          <button type="submit">Search</button>
          {searchQuery && (
            <button type="button" onClick={clearSearch} className="clear-btn">
              Clear
            </button>
          )}
        </form>
      </div>
      
      {/* Error message */}
      {error && <div className="error-message">{error}</div>}
      
      {/* Loading indicator */}
      {loading && <div className="loading-spinner">Loading...</div>}
      
      {/* Employee list */}
      <div className="employee-list">
        <h2>Company Employees</h2>
        {employees.length === 0 ? (
          <p className="no-results">No employees found</p>
        ) : (
          <table>
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Job Title</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {employees.map(employee => (
                <tr key={employee.id}>
                  <td>{employee.name}</td>
                  <td>{employee.email}</td>
                  <td>{employee.department || 'N/A'}</td>
                  <td>{employee.job_title || 'N/A'}</td>
                  <td>
                    <button 
                      onClick={() => getEmployeeDetails(employee.id)}
                      className="view-details-btn"
                    >
                      View Details
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        )}
      </div>
      
      {/* Employee details panel */}
      {selectedEmployee && (
        <div className="employee-details-panel">
          <div className="panel-header">
            <h2>Employee Details</h2>
            <button onClick={closeDetails} className="close-btn">×</button>
          </div>
          <div className="employee-details">
            {selectedEmployee.avatar && (
              <img 
                src={selectedEmployee.avatar} 
                alt={`${selectedEmployee.name}'s avatar`} 
                className="employee-avatar"
              />
            )}
            <h3>{selectedEmployee.name}</h3>
            <p><strong>Email:</strong> {selectedEmployee.email}</p>
            <p><strong>Role:</strong> {selectedEmployee.role}</p>
            {selectedEmployee.job_title && (
              <p><strong>Job Title:</strong> {selectedEmployee.job_title}</p>
            )}
            {selectedEmployee.department && (
              <p><strong>Department:</strong> {selectedEmployee.department}</p>
            )}
            {selectedEmployee.phone && (
              <p><strong>Phone:</strong> {selectedEmployee.phone}</p>
            )}
          </div>
        </div>
      )}
    </div>
  );
};

export default EmployeeManagement;
