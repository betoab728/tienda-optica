<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda Óptica</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">

    <x-header />
    <x-navbar />

    <main>
        @yield('content')
    </main>

</body>
</html>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>