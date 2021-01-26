<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        $token = null;
        if ($user) {
            $token = $user->createToken('Token Name')->accessToken;
        }
        return view('home', compact('token'));
    }
}
