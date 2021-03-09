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
<body class="bg-gray-200">
    <nav class="p-6 bg-white flex justify-between mb-6">
        <ul class="flex items-center">
       
        <li>
            <a href="{{route('home')}}" class="p-3">Home</a>
        </li>
        <li>
            <a href="{{route('dashboard')}}" class="p-3">Dashboard</a>
        </li>
        <li>
            <a href="{{route('reminder')}}" class="p-3">Reminder</a>
        </li>
        </ul>

        <ul class="flex items-center">
        <!-- @if (auth()->user()) -->
        @auth
        <li>
          Hi  <a href="" class="p-3">{{auth()->user()->name}}</a>
        </li>
        <li>
            <form action="{{route('logout')}}" method="post" class="p-3 inline">
            @csrf
            <button type="submit">Logout</button>
            </form>
        </li>


        {{-- @role('admin')
        <li>
           <a href="">Admin</a>
        </li>
        @endrole --}}

        {{-- another way --}}
        @if (auth()->user()->hasRole('admin'))
        <li>
            <a href="">Admin</a>
         </li>
        @endif
        @endauth
        <!-- @else -->
        @guest
        <li>
            <a href="{{ route('login') }}" class="p-3">Login</a>
        </li>
        <li>
            <a href="{{route('register')}}" class="p-3">Register</a>
        </li>
        @endguest
        </ul>
       
        <!-- @endif -->
    </nav>
    @yield('content')
    {{-- <script src="../js/app.js"></script> --}}
    <script>
        // window.User = {
        //     id: '{{ optional(auth()->user())->id }}',
        //     name: '{{ optional(auth()->user())->name }}',
        // }
        window.User = {!! json_encode(optional(auth()->user())->only('id', 'name')) !!}
    </script>
</body>
</html>