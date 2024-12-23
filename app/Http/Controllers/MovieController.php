<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller implements HasMiddleware
{

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            'auth',
            'check.device'
        ];
    }

    public function index()
    {
        $latestMovies = Movie::latest()->limit(8)->get();
        $topRatedMovies = Movie::with('ratings')
            ->get()
            ->sortByDesc('average_rating')
            ->take(8);
        return view('movies.index', [
            'latestMovies' => $latestMovies,
            'topRatedMovies' => $topRatedMovies
        ]);
    }

    public function show(Movie $movie)
    {
        $userPlan = Auth::user()->plans()->where('active', true)->first();
        $streamingUrl = $movie->getStreamingUrl($userPlan->resolution);
        return view('movies.show', [
            'movie' => $movie,
            'streamingUrl' => $streamingUrl
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->input('q');
        $movies = Movie::where('title', 'like', "%$search%")->get();
        return view('movies.search', [
            'keyword' => $search,
            'movies' => $movies
        ]);
    }
}