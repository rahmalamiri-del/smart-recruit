<main class="page">
    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert warning">{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="alert danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @yield('content')
</main>
