<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
        }

        th {
            background: #f5f5f5;
        }
    </style>
</head>

<body>
    <h1>Employee Report</h1>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Period:</strong> {{ $employeeAnalytics->period_start }} to {{ $employeeAnalytics->period_end }}</p>
    <p><strong>Summary:</strong> {{ $analytics['summary'] ?? '' }}</p>
    <p><strong>Meetings Attended:</strong> {{ $analytics['meetings_attended'] ?? '' }}</p>
    <p><strong>Tasks Completed:</strong> {{ $analytics['tasks_completed'] ?? '' }}</p>
    <p><strong>Tasks Assigned:</strong> {{ $analytics['tasks_assigned'] ?? '' }}</p>
    <p><strong>Sentiment:</strong> {{ $analytics['sentiment'] ?? '' }}</p>
    <p><strong>Notable Achievements:</strong> {{ isset($analytics['notable_achievements']) ? implode(', ', $analytics['notable_achievements']) : '' }}</p>
    <p><strong>Areas for Improvement:</strong> {{ isset($analytics['areas_for_improvement']) ? implode(', ', $analytics['areas_for_improvement']) : '' }}</p>
</body>

</html>