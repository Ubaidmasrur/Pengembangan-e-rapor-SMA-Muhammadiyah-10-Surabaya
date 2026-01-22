{{-- resources/views/vendor/pagination/default.blade.php --}}
@if ($paginator->hasPages())
    <div class="flex items-center justify-between" role="navigation" aria-label="Navigasi halaman data">
        <div class="text-xs text-gray-600">
            Menampilkan {{ $paginator->firstItem() }} sampai {{ $paginator->lastItem() }} dari total {{ $paginator->total() }} data.
        </div>
        <nav aria-label="Pagination Navigation">
            <ul class="inline-flex items-center space-x-1 text-sm" role="list">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li>
                                                <span aria-disabled="true" aria-label="Halaman sebelumnya tidak tersedia"
                                                            class="inline-flex items-center px-2 py-1 text-gray-400 bg-gray-200 rounded cursor-default">
                                                        <i class="fas fa-chevron-left w-4 h-4" aria-hidden="true"></i>
                                                </span>
                    </li>
                @else
                    <li>
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                           class="inline-flex items-center px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           aria-label="Halaman Sebelumnya">
                            <i class="fas fa-chevron-left w-4 h-4" aria-hidden="true"></i>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @php
                    $total = $paginator->lastPage();
                    $current = $paginator->currentPage();
                    $start = max(1, $current - 2);
                    $end = min($total, $current + 2);
                @endphp

                {{-- First page --}}
                @if ($start > 1)
                    <li>
                        <a href="{{ $paginator->url(1) }}"
                           class="inline-flex items-center px-2 py-1 rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           aria-label="Halaman 1">{{ 1 }}</a>
                    </li>
                    @if ($start > 2)
                        <li><span class="px-2" aria-hidden="true">...</span></li>
                    @endif
                @endif

                {{-- Page numbers --}}
                @for ($i = $start; $i <= $end; $i++)
                    @if ($i == $current)
                        <li>
                            <span aria-current="page"
                                  class="inline-flex items-center px-2 py-1 rounded bg-blue-600 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $i }}</span>
                        </li>
                    @else
                        <li>
                            <a href="{{ $paginator->url($i) }}"
                               class="inline-flex items-center px-2 py-1 rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                               aria-label="Halaman {{ $i }}">{{ $i }}</a>
                        </li>
                    @endif
                @endfor

                {{-- Last page --}}
                @if ($end < $total)
                    @if ($end < $total - 1)
                        <li><span class="px-2" aria-hidden="true">...</span></li>
                    @endif
                    <li>
                        <a href="{{ $paginator->url($total) }}"
                           class="inline-flex items-center px-2 py-1 rounded bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           aria-label="Halaman {{ $total }}">{{ $total }}</a>
                    </li>
                @endif

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li>
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                           class="inline-flex items-center px-2 py-1 text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           aria-label="Halaman Berikutnya">
                            <i class="fas fa-chevron-right w-4 h-4" aria-hidden="true"></i>
                        </a>
                    </li>
                @else
                    <li>
                                                <span aria-disabled="true" aria-label="Halaman berikutnya tidak tersedia"
                                                            class="inline-flex items-center px-2 py-1 text-gray-400 bg-gray-200 rounded cursor-default">
                                                        <i class="fas fa-chevron-right w-4 h-4" aria-hidden="true"></i>
                                                </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif