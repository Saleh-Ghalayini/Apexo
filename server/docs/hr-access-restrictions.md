# HR Access Restrictions Implementation

This document describes the implementation of access restrictions for HR users in the Apexo system.

## Requirements

1. HR users can only access employee information from their own company, not from other companies
2. HRs should be able to retrieve employee information by name instead of user ID
3. This feature should be exclusive to users with HR role

## Implementation

### Company-Level Restrictions for HR Users

The primary implementation of company-level restrictions is in the `getEmployeeInfo` method of the `DataAccessService` class.
For HR users, we've added a restriction to ensure they can only access employee data within their own company:

```php
if ($user->isHR()) {
    // HR users can only access employees from their own company
    $query->where('company_id', $user->company_id);
}
```

This ensures that whenever an HR user attempts to retrieve employee information, they will only see results from their own company.

### Name-Based Search Functionality

The system supports searching for employees by either ID or name:

```php
// Check if employeeIdentifier is numeric (id) or name
if (is_numeric($employeeIdentifier)) {
    $query->where('id', $employeeIdentifier);
} else {
    $query->where('name', 'like', "%{$employeeIdentifier}%");
}
```

When searching by name, the system performs a partial match using SQL `LIKE` operator.

### HR Role Exclusivity

The role-based access control system ensures that:

1. Employees can only access their own information
2. Managers can only access information about employees in their department
3. HR users can access information about all employees within their company

## Testing

A dedicated test command has been created to verify the HR access restrictions functionality:

```bash
php artisan test:hr-access --seed-test-data
```

This command tests several scenarios:

-   HR users accessing employees from their own company (should succeed)
-   HR users attempting to access employees from other companies (should fail)
-   HR users searching for employees by name and by ID

## AI System Integration

The AI chat system has been updated to include awareness of these restrictions. When an HR user asks the AI to retrieve employee information, the AI will:

1. Check that the user has HR role
2. Restrict searches to employees within the user's company
3. Support searching by employee name
4. Maintain confidentiality of sensitive employee information

## Technical Details

-   The company relationship is defined in the User model
-   The DataAccessService implements the access restrictions
-   The AI system uses the appropriate tools and permissions based on the user's role
-   Test data includes multiple companies to verify cross-company restrictions
