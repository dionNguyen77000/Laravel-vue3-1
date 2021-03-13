<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posty</title>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
</head>
<body>
    @yield('content')
    <script>
        // window.User = {
        //     id: '{{ optional(auth()->user())->id }}',
        //     name: '{{ optional(auth()->user())->name }}',
        // }
        window.User = {!! json_encode(optional(auth()->user())->only('id', 'name')) !!}
    </script>
</body>
</html>