@extends('layouts.app')

@section('title', __('favorites.title'))

@section('content')
<div class="hero-section" style="text-align: left; margin-bottom: 2rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="hero-title" style="margin-bottom: 0.25rem;">{{ __('favorites.title') }}</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem;">{{ __('favorites.subtitle') }}</p>
        </div>
        <div class="badge" style="background: rgba(99, 102, 241, 0.15); color: #a5b4fc; font-size: 0.85rem; padding: 0.4rem 0.9rem;">
            <span id="fav-count">{{ $favorites->count() }}</span> {{ __('messages.nav_favorites') }}
        </div>
    </div>
</div>

@if($favorites->isEmpty())
    <div class="empty-state" id="empty-state">
        <div class="empty-icon">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
            </svg>
        </div>
        <h3 class="empty-title">{{ __('favorites.empty') }}</h3>
        <p class="empty-desc">{{ __('favorites.empty_hint') }}</p>
        <a href="{{ route('movies.index') }}" class="btn btn-primary" style="margin-top: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span>{{ __('favorites.explore') }}</span>
        </a>
    </div>
@else
    <div class="movie-grid" id="favorites-grid">
        @foreach($favorites as $favorite)
            @php
                $hasPoster = !empty($favorite->poster) && $favorite->poster !== 'N/A';
            @endphp
            <div class="movie-card" id="fav-card-{{ $favorite->imdb_id }}">
                <div class="poster-wrapper">
                    @if($hasPoster)
                        <img 
                            src="{{ $favorite->poster }}" 
                            alt="{{ $favorite->title }}" 
                            class="movie-poster" 
                            loading="lazy"
                            onerror="this.parentElement.innerHTML='<div class=\'poster-placeholder\'><span>No Image</span></div>';"
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

                    <!-- Remove Favorite Button -->
                    <button 
                        type="button" 
                        class="btn-fav-toggle active" 
                        title="{{ __('favorites.remove_from_favorites') }}"
                        onclick="removeFavorite('{{ $favorite->imdb_id }}')"
                    >
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#ef4444" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                    </button>

                    <div class="movie-badges">
                        <span class="badge badge-{{ $favorite->type ?? 'movie' }}">
                            {{ ucfirst($favorite->type ?? 'movie') }}
                        </span>
                        @if($favorite->year)
                            <span class="badge badge-year">{{ $favorite->year }}</span>
                        @endif
                    </div>
                </div>

                <div class="movie-info">
                    <h2 class="movie-title" title="{{ $favorite->title }}">{{ $favorite->title }}</h2>

                    <div style="margin-top: 0.75rem;">
                        <a href="{{ route('movies.show', $favorite->imdb_id) }}" class="btn btn-secondary" style="width: 100%; font-size: 0.82rem; padding: 0.5rem;">
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
@endif
@endsection

@push('scripts')
<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function removeFavorite(imdbId) {
        if (!confirm("Are you sure you want to remove this movie from favorites?")) return;

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
                const card = document.getElementById(`fav-card-${imdbId}`);
                if (card) {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.85)';
                    card.style.transition = 'all 0.3s ease';
                    setTimeout(() => {
                        card.remove();
                        const grid = document.getElementById('favorites-grid');
                        const countEl = document.getElementById('fav-count');
                        if (grid) {
                            const remaining = grid.querySelectorAll('.movie-card').length;
                            if (countEl) countEl.innerText = remaining;
                            if (remaining === 0) {
                                window.location.reload();
                            }
                        }
                    }, 300);
                }
                showToast(data.message || "{{ __('favorites.removed') }}", 'success');
            } else {
                showToast(data.message || "Failed to remove favorite.", 'error');
            }
        } catch (err) {
            console.error(err);
            showToast("Network error.", 'error');
        }
    }
</script>
@endpush
