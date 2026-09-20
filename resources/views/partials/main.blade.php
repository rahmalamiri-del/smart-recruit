<main class="page" id="main-content" tabindex="-1">
    @if(session('success'))
        <div class="alert success" role="status">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert warning" role="status">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert danger" role="alert">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert danger" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
