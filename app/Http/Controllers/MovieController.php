<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Services\OmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    /**
     * @var \App\Services\OmdbService
     */
    protected $omdb;

    /**
     * Create a new MovieController instance.
     *
     * @param  \App\Services\OmdbService  $omdb
     */
    public function __construct(OmdbService $omdb)
    {
        $this->middleware('auth');
        $this->omdb = $omdb;
    }

    /**
     * Display the movie listing page with a default search.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->input('search', '');
        $type  = $request->input('type', '');
        $year  = $request->input('year', '');

        $favoriteIds = Auth::user()
            ->favorites()
            ->pluck('imdb_id')
            ->toArray();

        // Only call OMDB API if there is a search query
        if ($query !== '') {
            $result = $this->omdb->searchMovies($query, $type ?: null, $year ?: null, 1);
            return view('movies.index', [
                'movies'       => $result['success'] ? $result['movies'] : [],
                'totalResults' => $result['success'] ? $result['totalResults'] : 0,
                'query'        => $query,
                'type'         => $type,
                'year'         => $year,
                'error'        => !$result['success'] ? $result['message'] : null,
                'favoriteIds'  => $favoriteIds,
            ]);
        }

        return view('movies.index', [
            'movies'       => [],
            'totalResults' => 0,
            'query'        => '',
            'type'         => $type,
            'year'         => $year,
            'error'        => null,
            'favoriteIds'  => $favoriteIds,
        ]);
    }

    /**
     * Search movies via AJAX for infinite scroll.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('search', '');
        $type  = $request->input('type', '');
        $year  = $request->input('year', '');
        $page  = (int) $request->input('page', 1);

        $result = $this->omdb->searchMovies($query, $type ?: null, $year ?: null, $page);

        $favoriteIds = Auth::user()
            ->favorites()
            ->pluck('imdb_id')
            ->toArray();

        return response()->json([
            'success'      => $result['success'],
            'movies'       => $result['success'] ? $result['movies'] : [],
            'totalResults' => $result['success'] ? $result['totalResults'] : 0,
            'favoriteIds'  => $favoriteIds,
            'message'      => !$result['success'] ? ($result['message'] ?? '') : '',
        ]);
    }

    /**
     * Display the detail page for a single movie.
     *
     * @param  string  $imdbId
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(string $imdbId)
    {
        $result = $this->omdb->getMovieDetail($imdbId);

        if (!$result['success']) {
            return redirect()->route('movies.index')->with('error', $result['message']);
        }

        $isFavorite = Auth::user()
            ->favorites()
            ->where('imdb_id', $imdbId)
            ->exists();

        return view('movies.show', [
            'movie'      => $result['movie'],
            'isFavorite' => $isFavorite,
        ]);
    }
}
