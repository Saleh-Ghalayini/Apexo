<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Meeting Report</title>
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
    <h1>Meeting Report</h1>
    <p><strong>Title:</strong> {{ $meeting->title }}</p>
    <p><strong>Scheduled At:</strong> {{ $meeting->scheduled_at }}</p>
    <p><strong>Ended At:</strong> {{ $meeting->ended_at }}</p>
    <p><strong>Summary:</strong> {{ $analytics['summary'] ?? ($analytics['error'] ?? 'No summary available') }}</p>
    <p><strong>Sentiment:</strong> {{ $analytics['sentiment'] ?? ($analytics['error'] ?? 'No sentiment available') }}</p>
    <p><strong>Topics:</strong> {{ isset($analytics['topics']) ? implode(', ', $analytics['topics']) : ($analytics['error'] ?? 'No topics available') }}</p>
    <h2>Action Items</h2>
    @if(isset($analytics['action_items']) && is_array($analytics['action_items']) && count($analytics['action_items']))
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Assignee</th>
                <th>Due Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($analytics['action_items'] as $item)
            <tr>
                <td>{{ $item['description'] ?? '' }}</td>
                <td>{{ $item['assignee'] ?? '' }}</td>
                <td>{{ $item['due_date'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @elseif(isset($analytics['error']))
    <p><em>{{ $analytics['error'] }}</em></p>
    @else
    <p><em>No action items available</em></p>
    @endif
</body>

</html>