# Apexo AI Chat with Role-Based Database Access

This document outlines how to use and test the AI chat functionality with role-based database access.

## Overview

The Apexo application has been enhanced with AI chat capabilities that can access the database based on the user's role. This means:

-   **Regular employees** can access only their own tasks, meetings, and personal information
-   **Managers** can access all data for their department members
-   **HR** can access all employee data across the company

## Testing the Functionality

### Using the Artisan Command

To quickly test the AI chat with database access, use the following command:

```bash
php artisan chat:test-ai {email} {message}
```

For example:

```bash
# Test as a regular employee
php artisan chat:test-ai employee@example.com "What tasks do I have due today?"

# Test as a manager
php artisan chat:test-ai manager@example.com "Show me all tasks assigned to my team members"

# Test as HR
php artisan chat:test-ai hr@example.com "Give me information about employee John Smith"
```

### Via API Endpoints

You can also test through the API endpoints:

1. Login with the user credentials:

```
POST /api/v1/auth/login
{
  "email": "employee@example.com",
  "password": "password"
}
```

2. Create a new chat session:

```
POST /api/v1/chat/sessions
{
  "title": "Database Test",
  "initial_message": "Hello, can you help me with my tasks?"
}
```

3. Send follow-up messages:

```
POST /api/v1/chat/sessions/{session_id}/messages
{
  "message": "What tasks do I have due this week?"
}
```

## Sample Questions to Test

### For All Users

-   "What are my upcoming tasks?"
-   "Do I have any meetings scheduled for today?"
-   "What's my employee information?"

### For Managers

-   "Show me all tasks assigned to my team"
-   "What's the status of tasks for my department?"
-   "Who has overdue tasks in my team?"
-   "Show me analytics for my department"

### For HR

-   "Who works in the Engineering department?"
-   "Show me all employees with overdue tasks"
-   "What are the analytics for all departments?"
-   "Give me information about employee Jane Doe"

## Populating Test Data

The system comes with test data that you can populate using:

```bash
php artisan db:seed --class=ChatAITestDataSeeder
```

This will create:

-   Users with different roles (employee, manager, HR)
-   Sample tasks for each user
-   Sample meetings with attendees
-   Department structure for testing manager access

## Technical Implementation

The role-based access is implemented through:

1. **AIToolsService**: Provides role-specific AI tools
2. **DataAccessService**: Implements data access logic with permission checks
3. **PromptService**: Adds role-specific capabilities to the system prompt
4. **ChatService**: Integrates tools with the chat flow

Each tool checks the user's role before allowing access to specific data, ensuring proper security boundaries.
