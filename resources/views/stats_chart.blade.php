<!DOCTYPE html>
<html>
<head>
    <title>Stats Chart</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<div id="app">
    <stats-chart></stats-chart>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
