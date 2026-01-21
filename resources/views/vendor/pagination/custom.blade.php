{{-- @if ($paginator->hasPages())
    <div class="pagination-custom">
        <!-- Previous Page Link -->
        @if ($paginator->onFirstPage())
            <button type="button" aria-label="Sebelumnya" disabled>&lt;</button>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" aria-label="Sebelumnya">
                <button type="button">&lt;</button>
            </a>
        @endif

        <!-- Pagination Elements -->
        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <button type="button" class="active" aria-label="Halaman {{ $page }}">{{ $page }}</button>
                    @else
                        <a href="{{ $url }}" aria-label="Halaman {{ $page }}">
                            <button type="button">{{ $page }}</button>
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach

        <!-- Next Page Link -->
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" aria-label="Berikutnya">
                <button type="button">&gt;</button>
            </a>
        @else
            <button type="button" aria-label="Berikutnya" disabled>&gt;</button>
        @endif
    </div>
@endif --}}

@if ($paginator->hasPages())
    <nav class="pagination-wrapper text-center mt-4">
        <ul class="pagination justify-content-center">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a>
                </li>
            @endif

            {{-- Nomor Halaman --}}
            @foreach ($elements as $element)
                {{-- Tanda ... --}}
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array halaman --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">&raquo;</span>
                </li>
            @endif

        </ul>
    </nav>
@endif
