<!DOCTYPE html>
<html>
<head>
    @vite('resources/scss/app.scss')
</head>
<body>
    <div id="app">
        @yield('content')
        @vite('resources/js/app.js')
        <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
    </div>
</body>
</html>
