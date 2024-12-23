<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $movies = $category->movies()->latest()->get();
        return view('categories.show', [
            'category' => $category,
            'movies' => $movies
        ]);
    }
}