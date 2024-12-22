<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

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
}
