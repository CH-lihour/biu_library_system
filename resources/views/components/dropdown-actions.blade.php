@props([
    'uid',
    'items' => [
        // Example:
        // ['label' => 'Return', 'route' => route('borrows.return', $borrowId), 'method' => 'POST', 'danger' => false],
    ],
])

@php
    $toggleId = "t-{$uid}";
    $menuId = "m-{$uid}";
@endphp

<div class="dd-wrap">
    <div class="icon-btn" id="{{ $toggleId }}" onclick="toggle('{{ $menuId }}','{{ $toggleId }}')">
        <svg viewBox="0 0 16 16">
            <circle cx="3" cy="8" r="1.2" />
            <circle cx="8" cy="8" r="1.2" />
            <circle cx="13" cy="8" r="1.2" />
        </svg>
    </div>

    <div class="dd-menu right" id="{{ $menuId }}">
        @foreach ($items as $item)
            @if (!empty($item['divider']))
                <div class="dd-divider"></div>
                @continue
            @endif

            @php
                $label = $item['label'] ?? 'Action';
                $danger = !empty($item['danger']) ? 'danger' : '';
                $route = $item['route'] ?? null;
                $method = strtoupper($item['method'] ?? 'GET');
                $confirm = $item['confirm'] ?? null;
            @endphp

            @if ($route && $method === 'GET')
                <a href="{{ $route }}"
                   class="dd-item {{ $danger }}"
                   @if($confirm) onclick="return confirm('{{ $confirm }}')" @endif>
                    <svg viewBox="0 0 16 16"><circle cx="8" cy="8" r="1.2" /></svg>
                    {{ $label }}
                </a>
            @elseif($route)
                <form action="{{ $route }}" method="POST" class="m-0 p-0">
                    @csrf
                    @if (!in_array($method, ['GET', 'POST']))
                        @method($method)
                    @endif
                    <button type="submit"
                            class="dd-item {{ $danger }} w-100 text-start border-0 bg-transparent"
                            @if($confirm) onclick="return confirm('{{ $confirm }}')" @endif>
                        <svg viewBox="0 0 16 16"><circle cx="8" cy="8" r="1.2" /></svg>
                        {{ $label }}
                    </button>
                </form>
            @else
                <div class="dd-item {{ $danger }}">
                    <svg viewBox="0 0 16 16"><circle cx="8" cy="8" r="1.2" /></svg>
                    {{ $label }}
                </div>
            @endif
        @endforeach
    </div>
</div>
