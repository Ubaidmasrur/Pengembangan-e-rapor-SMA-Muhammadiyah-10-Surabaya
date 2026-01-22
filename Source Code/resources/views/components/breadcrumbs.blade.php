@props(['items' => []])

<nav class="text-sm text-gray-700" aria-label="Breadcrumb">
    <ol class="flex items-center flex-wrap">
        <li class="flex items-center">
            <a href="{{ url('/') }}" class="flex items-center text-gray-500 hover:text-gray-700">
                <i class="fas fa-home mr-2 text-gray-400" aria-hidden="true"></i>
                <span class="sr-only">Beranda</span>
                <span class="hidden sm:inline">Beranda</span>
            </a>
        </li>

        @if(!empty($items) && is_array($items) && count($items) > 0)
            @foreach($items as $item)
                <li class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-300 mx-2" aria-hidden="true"></i>

                    @if(isset($item['url']) && !$loop->last)
                        <a href="{{ $item['url'] }}" class="text-gray-500 hover:text-gray-700">{{ $item['label'] ?? '-' }}</a>
                    @else
                        <span class="text-gray-700 font-medium" aria-current="page">{{ $item['label'] ?? '-' }}</span>
                    @endif
                </li>
            @endforeach
        @else
            {{-- fallback: allow consumer to pass custom breadcrumb items via slot --}}
            {{ $slot ?? '' }}
        @endif
    </ol>
</nav>
