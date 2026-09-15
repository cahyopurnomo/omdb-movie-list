@extends('layouts.app')

@section('title', __('movies.title'))

@section('content')
<div class="hero-section">
    <h1 class="hero-title">{{ __('movies.title') }}</h1>
    <p class="hero-subtitle">{{ __('messages.tagline') }}</p>

    <!-- Search & Filter Card -->
    <div class="search-box-card">
        <form action="{{ route('movies.index') }}" method="GET" class="search-form-grid" id="search-form">
            <div class="input-group">
                <span class="input-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </span>
                <input 
                    type="text" 
                    name="search" 
                    id="search-input" 
                    class="form-control has-icon" 
                    placeholder="{{ __('movies.search_placeholder') }}" 
                    value="{{ $query }}"
                    required
                >
            </div>

            <div>
                <select name="type" id="type-select" class="form-control" style="min-width: 130px;">
                    <option value="">{{ __('movies.all_types') }}</option>
                    <option value="movie" {{ $type === 'movie' ? 'selected' : '' }}>{{ __('movies.type_movie') }}</option>
                    <option value="series" {{ $type === 'series' ? 'selected' : '' }}>{{ __('movies.type_series') }}</option>
                    <option value="episode" {{ $type === 'episode' ? 'selected' : '' }}>{{ __('movies.type_episode') }}</option>
                </select>
            </div>

            <div>
                <input 
                    type="text" 
                    name="year" 
                    id="year-input" 
                    class="form-control" 
                    placeholder="{{ __('movies.year_placeholder') }}" 
                    value="{{ $year }}" 
                    maxlength="4"
                    style="width: 120px;"
                >
            </div>

            <button type="submit" class="btn btn-primary" id="btn-search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span>{{ __('movies.search_button') }}</span>
            </button>

            @if($query !== '' || $type || $year)
                <a href="{{ route('movies.index') }}" class="btn btn-secondary" title="{{ __('movies.reset_button') }}">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </a>
            @endif
        </form>
    </div>
</div>

<!-- Results Header -->
<div class="results-header">
    <div class="results-count">
        @if($totalResults > 0)
            {!! __('movies.results_found', ['count' => '<strong>' . number_format($totalResults) . '</strong>', 'query' => '<strong>' . e($query) . '</strong>']) !!}
        @else
            <span>{{ __('movies.no_results') }}</span>
        @endif
    </div>
</div>

@if(empty($movies))
    <div class="empty-state">
        <div class="empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="2" width="20" height="20" rx="2.18" ry="2.18"></rect>
                <line x1="7" y1="2" x2="7" y2="22"></line>
                <line x1="17" y1="2" x2="17" y2="22"></line>
                <line x1="2" y1="12" x2="22" y2="12"></line>
            </svg>
        </div>
        <h3 class="empty-title">{{ __('movies.no_results') }}</h3>
        <p class="empty-desc">{{ $error ?? __('movies.error_not_found') }}</p>
    </div>
@else
    <!-- Movies Grid -->
    <div class="movie-grid" id="movie-grid">
        @foreach($movies as $movie)
            @php
                $isFav = in_array($movie['imdbID'], $favoriteIds);
                $hasPoster = !empty($movie['Poster']) && $movie['Poster'] !== 'N/A';
            @endphp
            <div class="movie-card" data-imdb="{{ $movie['imdbID'] }}" id="movie-card-{{ $movie['imdbID'] }}">
                <div class="poster-wrapper">
                    @if($hasPoster)
                        <img 
                            src="{{ $movie['Poster'] }}" 
                            alt="{{ $movie['Title'] }}" 
                            class="movie-poster" 
                            loading="lazy"
                            onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'poster-placeholder\'><svg width=\'32\' height=\'32\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'1.5\'><rect x=\'2\' y=\'2\' width=\'20\' height=\'20\' rx=\'2\'></rect><circle cx=\'8.5\' cy=\'8.5\' r=\'1.5\'></circle><polyline points=\'21 15 16 10 5 21\'></polyline></svg><span>No Image</span></div>';"
                        >
                    @else
                        <div class="poster-placeholder">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="2" y="2" width="20" height="20" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <span>No Poster</span>
                        </div>
                    @endif

                    <!-- Favorite Toggle Button -->
                    <button 
                        type="button" 
                        class="btn-fav-toggle {{ $isFav ? 'active' : '' }}" 
                        data-imdb="{{ $movie['imdbID'] }}"
                        data-title="{{ $movie['Title'] }}"
                        data-year="{{ $movie['Year'] }}"
                        data-poster="{{ $movie['Poster'] }}"
                        data-type="{{ $movie['Type'] }}"
                        title="{{ $isFav ? __('favorites.remove_from_favorites') : __('favorites.add_to_favorites') }}"
                        onclick="toggleFavorite(event, this)"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>

                    <!-- Badges -->
                    <div class="movie-badges">
                        <span class="badge badge-{{ $movie['Type'] ?? 'movie' }}">
                            {{ ucfirst($movie['Type'] ?? 'movie') }}
                        </span>
                        <span class="badge badge-year">{{ $movie['Year'] }}</span>
                    </div>
                </div>

                <div class="movie-info">
                    <div>
                        <h2 class="movie-title" title="{{ $movie['Title'] }}">{{ $movie['Title'] }}</h2>
                    </div>

                    <div style="margin-top: 0.75rem;">
                        <a href="{{ route('movies.show', $movie['imdbID']) }}" class="btn btn-secondary" style="width: 100%; font-size: 0.82rem; padding: 0.5rem;">
                            <span>{{ __('movies.details') }}</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Infinite Scroll Sentinel / Loading Status -->
    <div id="scroll-sentinel" class="scroll-status">
        <div id="loading-spinner" class="spinner" style="display: none;"></div>
        <p id="scroll-message" style="display: none;">{{ __('movies.loading') }}</p>
        <button type="button" id="btn-load-more" class="btn btn-secondary" style="display: none;" onclick="loadNextPage()">
            {{ __('movies.load_more') }}
        </button>
    </div>
@endif
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let currentPage = 1;
    let totalResults = {{ (int) $totalResults }};
    let currentQuery = @json($query);
    let currentType = @json($type);
    let currentYear = @json($year);
    let isLoading = false;
    let hasMorePages = totalResults > 10;

    const movieGrid = document.getElementById('movie-grid');
    const loadingSpinner = document.getElementById('loading-spinner');
    const scrollMessage = document.getElementById('scroll-message');
    const btnLoadMore = document.getElementById('btn-load-more');
    const sentinel = document.getElementById('scroll-sentinel');

    // Toggle Favorite function
    async function toggleFavorite(event, btn) {
        event.stopPropagation();
        event.preventDefault();

        const imdbId = btn.getAttribute('data-imdb');
        const isFav = btn.classList.contains('active');

        if (!isFav) {
            // Add favorite
            try {
                const response = await fetch("{{ route('favorites.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        imdb_id: imdbId,
                        title: btn.getAttribute('data-title'),
                        year: btn.getAttribute('data-year'),
                        poster: btn.getAttribute('data-poster'),
                        type: btn.getAttribute('data-type')
                    })
                });
                const data = await response.json();
                if (data.success) {
                    btn.classList.add('active');
                    btn.setAttribute('title', "{{ __('favorites.remove_from_favorites') }}");
                    showToast(data.message || "{{ __('favorites.added') }}", 'success');
                }
            } catch (err) {
                console.error(err);
                showToast("Failed to update favorite.", 'error');
            }
        } else {
            // Remove favorite
            try {
                const response = await fetch(`{{ url('favorites') }}/${imdbId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    btn.classList.remove('active');
                    btn.setAttribute('title', "{{ __('favorites.add_to_favorites') }}");
                    showToast(data.message || "{{ __('favorites.removed') }}", 'success');
                }
            } catch (err) {
                console.error(err);
                showToast("Failed to remove favorite.", 'error');
            }
        }
    }

    // Infinite Scroll / Next Page Loader
    async function loadNextPage() {
        if (isLoading || !hasMorePages) return;
        isLoading = true;
        currentPage++;

        if (loadingSpinner) loadingSpinner.style.display = 'block';
        if (scrollMessage) {
            scrollMessage.innerText = "{{ __('movies.loading') }}";
            scrollMessage.style.display = 'block';
        }
        if (btnLoadMore) btnLoadMore.style.display = 'none';

        const url = new URL("{{ route('movies.search') }}", window.location.origin);
        url.searchParams.set('search', currentQuery);
        if (currentType) url.searchParams.set('type', currentType);
        if (currentYear) url.searchParams.set('year', currentYear);
        url.searchParams.set('page', currentPage);

        try {
            const res = await fetch(url.toString(), {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();

            if (data.success && data.movies && data.movies.length > 0) {
                appendMovies(data.movies, data.favoriteIds || []);
                const loadedCount = currentPage * 10;
                if (loadedCount >= data.totalResults || data.movies.length < 10) {
                    hasMorePages = false;
                    if (scrollMessage) {
                        scrollMessage.innerText = "{{ __('movies.all_loaded') }}";
                        scrollMessage.style.display = 'block';
                    }
                }
            } else {
                hasMorePages = false;
                if (scrollMessage) {
                    scrollMessage.innerText = "{{ __('movies.all_loaded') }}";
                    scrollMessage.style.display = 'block';
                }
            }
        } catch (e) {
            console.error(e);
            if (btnLoadMore) btnLoadMore.style.display = 'inline-flex';
        } finally {
            isLoading = false;
            if (loadingSpinner) loadingSpinner.style.display = 'none';
        }
    }

    function appendMovies(movies, favoriteIds) {
        if (!movieGrid) return;

        movies.forEach(movie => {
            // Avoid duplicate card if already present
            if (document.getElementById(`movie-card-${movie.imdbID}`)) return;

            const isFav = favoriteIds.includes(movie.imdbID);
            const hasPoster = movie.Poster && movie.Poster !== 'N/A';
            const card = document.createElement('div');
            card.className = 'movie-card';
            card.id = `movie-card-${movie.imdbID}`;
            card.setAttribute('data-imdb', movie.imdbID);

            const posterHtml = hasPoster 
                ? `<img src="${movie.Poster}" alt="${escapeHtml(movie.Title)}" class="movie-poster" loading="lazy" onerror="this.parentElement.innerHTML='<div class=\\'poster-placeholder\\'><span>No Image</span></div>';">`
                : `<div class="poster-placeholder"><span>No Poster</span></div>`;

            card.innerHTML = `
                <div class="poster-wrapper">
                    ${posterHtml}
                    <button 
                        type="button" 
                        class="btn-fav-toggle ${isFav ? 'active' : ''}" 
                        data-imdb="${movie.imdbID}"
                        data-title="${escapeHtml(movie.Title)}"
                        data-year="${escapeHtml(movie.Year)}"
                        data-poster="${escapeHtml(movie.Poster)}"
                        data-type="${escapeHtml(movie.Type)}"
                        title="${isFav ? '{{ __('favorites.remove_from_favorites') }}' : '{{ __('favorites.add_to_favorites') }}'}"
                        onclick="toggleFavorite(event, this)"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>
                    <div class="movie-badges">
                        <span class="badge badge-${movie.Type || 'movie'}">${(movie.Type || 'movie').toUpperCase()}</span>
                        <span class="badge badge-year">${movie.Year}</span>
                    </div>
                </div>
                <div class="movie-info">
                    <div>
                        <h2 class="movie-title" title="${escapeHtml(movie.Title)}">${escapeHtml(movie.Title)}</h2>
                    </div>
                    <div style="margin-top: 0.75rem;">
                        <a href="{{ url('movies') }}/${movie.imdbID}" class="btn btn-secondary" style="width: 100%; font-size: 0.82rem; padding: 0.5rem;">
                            <span>{{ __('movies.details') }}</span>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            `;
            movieGrid.appendChild(card);
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    // IntersectionObserver for smooth infinite scroll
    if (sentinel && hasMorePages) {
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && hasMorePages && !isLoading) {
                loadNextPage();
            }
        }, { rootMargin: '200px' });
        observer.observe(sentinel);
    }
</script>
@endpush
