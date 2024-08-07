<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order Results</title>
</head>

<body>
    <h1>Purchase Order Results</h1>
    <ul>
        @foreach($data['result'] as $result)
            <li> Product Type ID: {{ $result['product_type_id'] }}, Total: {{ $result['total'] }}</li>
        @endforeach
    </ul>

    <h2>Failed Requests</h2>
    <ul>
        @foreach($data['failedRequests'] as $failedRequests)
            <li> {{ $failedRequests }}</li>
        @endforeach
    </ul>
</body>
</html>
