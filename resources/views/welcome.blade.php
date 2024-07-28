<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <!-- Styles -->

        @vite(['resources/css/app.css'])
        @vite(['resources/js/app.js'])
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50">
        <div id="app">
            <counter></counter>
            <div class="stats-container">
                <div class="stats-chart-wrapper">
                    <stats-chart class="stats-chart"></stats-chart>
                </div>
                <div class="stats-table-wrapper">
                    <stats-table class="stats-table"></stats-table>
                </div>
            </div>
        </div>

    </body>
</html>
