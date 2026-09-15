<?php

namespace App\Http\Controllers;

use App\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Create a new FavoriteController instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display all favorites for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $favorites = Auth::user()
            ->favorites()
            ->orderByDesc('created_at')
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Store a new favorite for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'imdb_id' => 'required|string',
            'title'   => 'required|string',
            'year'    => 'nullable|string',
            'poster'  => 'nullable|string',
            'type'    => 'nullable|string',
        ]);

        $favorite = Auth::user()->favorites()->firstOrCreate(
            ['imdb_id' => $validated['imdb_id']],
            [
                'title'  => $validated['title'],
                'year'   => $validated['year'] ?? null,
                'poster' => $validated['poster'] ?? null,
                'type'   => $validated['type'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => __('favorites.added'),
            'id'      => $favorite->id,
        ]);
    }

    /**
     * Remove a favorite by its IMDb ID.
     *
     * @param  string  $imdbId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $imdbId)
    {
        $deleted = Auth::user()
            ->favorites()
            ->where('imdb_id', $imdbId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => __('favorites.removed'),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => __('favorites.not_found'),
        ], 404);
    }
}
