<nav class="{{ $navClass ?? 'sidebar-nav' }}" aria-label="Navigation principale">
    @foreach($navItems as $item)
        <a href="{{ $item['url'] }}" @class(['active' => $item['active']]) @if($item['active']) aria-current="page" @endif>
            @include('partials.icon', ['name' => $item['icon']])
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
