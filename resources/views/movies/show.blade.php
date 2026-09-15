@extends('layouts.app')

@section('title', $movie['Title'] ?? __('movies.details'))

@section('content')
<div class="detail-container">
    <a href="{{ route('movies.index') }}" class="back-link">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
        </svg>
        <span>{{ __('movies.back_to_list') }}</span>
    </a>

    <div class="detail-card">
        <!-- Poster Column -->
        <div class="detail-poster-col">
            @if(!empty($movie['Poster']) && $movie['Poster'] !== 'N/A')
                <img 
                    src="{{ $movie['Poster'] }}" 
                    alt="{{ $movie['Title'] }}" 
                    class="detail-poster-img"
                    onerror="this.onerror=null; this.src='https://via.placeholder.com/300x450/0f172a/94a3b8?text=No+Poster';"
                >
            @else
                <div class="poster-placeholder" style="width: 260px; height: 380px; border-radius: 14px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="2" width="20" height="20" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    <span>No Poster Available</span>
                </div>
            @endif
        </div>

        <!-- Info Column -->
        <div class="detail-info-col">
            <div class="detail-header">
                <div>
                    <div class="detail-badges" style="margin-bottom: 0.5rem;">
                        <span class="badge badge-{{ $movie['Type'] ?? 'movie' }}">
                            {{ ucfirst($movie['Type'] ?? 'movie') }}
                        </span>
                        @if(!empty($movie['Rated']) && $movie['Rated'] !== 'N/A')
                            <span class="badge" style="background: rgba(255,255,255,0.08); color: #e2e8f0;">
                                {{ $movie['Rated'] }}
                            </span>
                        @endif
                        <span class="badge badge-year">{{ $movie['Year'] ?? '' }}</span>
                        @if(!empty($movie['Runtime']) && $movie['Runtime'] !== 'N/A')
                            <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: #a5b4fc;">
                                {{ $movie['Runtime'] }}
                            </span>
                        @endif
                    </div>
                    <h1 class="detail-title">{{ $movie['Title'] }}</h1>
                </div>

                <!-- Favorite Toggle Button -->
                <button 
                    type="button" 
                    id="btn-detail-fav"
                    class="btn-fav-toggle {{ $isFavorite ? 'active' : '' }}" 
                    data-imdb="{{ $movie['imdbID'] }}"
                    data-title="{{ $movie['Title'] }}"
                    data-year="{{ $movie['Year'] ?? '' }}"
                    data-poster="{{ $movie['Poster'] ?? '' }}"
                    data-type="{{ $movie['Type'] ?? '' }}"
                    title="{{ $isFavorite ? __('favorites.remove_from_favorites') : __('favorites.add_to_favorites') }}"
                    onclick="toggleDetailFavorite(this)"
                    style="position: static; width: 44px; height: 44px;"
                >
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                </button>
            </div>

            <!-- Genre Pills -->
            @if(!empty($movie['Genre']) && $movie['Genre'] !== 'N/A')
                <div class="genre-tags">
                    @foreach(explode(',', $movie['Genre']) as $genre)
                        <span class="genre-pill">{{ trim($genre) }}</span>
                    @endforeach
                </div>
            @endif

            <!-- Rating Banner -->
            @if(!empty($movie['imdbRating']) && $movie['imdbRating'] !== 'N/A')
                <div class="rating-bar">
                    <div class="rating-score">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" stroke-width="1">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span>{{ $movie['imdbRating'] }}</span>
                        <span style="font-size: 0.9rem; color: var(--text-dim); font-weight: 500;">/ 10</span>
                    </div>
                    @if(!empty($movie['imdbVotes']) && $movie['imdbVotes'] !== 'N/A')
                        <div style="font-size: 0.85rem; color: var(--text-muted); border-left: 1px solid var(--border-subtle); padding-left: 1rem;">
                            <strong>{{ $movie['imdbVotes'] }}</strong> {{ __('movies.votes') }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Synopsis / Plot -->
            @if(!empty($movie['Plot']) && $movie['Plot'] !== 'N/A')
                <div class="synopsis-box">
                    <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-dim); font-weight: 700; margin-bottom: 0.35rem;">
                        {{ __('movies.plot') }}
                    </div>
                    <p>{{ $movie['Plot'] }}</p>
                </div>
            @endif

            <!-- Metadata Table -->
            <div class="meta-table">
                @if(!empty($movie['Director']) && $movie['Director'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.director') }}</span>
                        <span class="meta-value">{{ $movie['Director'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Writer']) && $movie['Writer'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.writer') }}</span>
                        <span class="meta-value">{{ $movie['Writer'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Actors']) && $movie['Actors'] !== 'N/A')
                    <div class="meta-item" style="grid-column: 1 / -1;">
                        <span class="meta-label">{{ __('movies.actors') }}</span>
                        <span class="meta-value">{{ $movie['Actors'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Released']) && $movie['Released'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.release_date') }}</span>
                        <span class="meta-value">{{ $movie['Released'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Language']) && $movie['Language'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.language') }}</span>
                        <span class="meta-value">{{ $movie['Language'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Country']) && $movie['Country'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.country') }}</span>
                        <span class="meta-value">{{ $movie['Country'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['BoxOffice']) && $movie['BoxOffice'] !== 'N/A')
                    <div class="meta-item">
                        <span class="meta-label">{{ __('movies.box_office') }}</span>
                        <span class="meta-value">{{ $movie['BoxOffice'] }}</span>
                    </div>
                @endif

                @if(!empty($movie['Awards']) && $movie['Awards'] !== 'N/A')
                    <div class="meta-item" style="grid-column: 1 / -1;">
                        <span class="meta-label">{{ __('movies.awards') }}</span>
                        <span class="meta-value" style="color: var(--accent-gold);">{{ $movie['Awards'] }}</span>
                    </div>
                @endif
            </div>

            <!-- External Ratings -->
            @if(!empty($movie['Ratings']) && is_array($movie['Ratings']) && count($movie['Ratings']) > 0)
                <div style="padding-top: 1rem; border-top: 1px solid var(--border-subtle);">
                    <div class="meta-label" style="margin-bottom: 0.5rem;">{{ __('movies.ratings') }}</div>
                    <div class="ratings-list">
                        @foreach($movie['Ratings'] as $rating)
                            <div class="rating-chip">
                                <span class="rating-chip-source">{{ $rating['Source'] }}:</span>
                                <span class="rating-chip-val">{{ $rating['Value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function toggleDetailFavorite(btn) {
        const imdbId = btn.getAttribute('data-imdb');
        const isFav = btn.classList.contains('active');

        if (!isFav) {
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
                showToast("Failed to add favorite.", 'error');
            }
        } else {
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
</script>
@endpush
