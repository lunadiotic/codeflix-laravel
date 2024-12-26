<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function overview()
    {
        $user = Auth::user();
        $membership = $user->memberships()->where('active', true)->with('plan')->first();
        return view('profile.overview', compact('user', 'membership'));
    }
}
