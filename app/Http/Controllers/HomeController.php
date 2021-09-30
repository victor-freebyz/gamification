<?php

namespace App\Http\Controllers;

use App\Models\Games;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        if($user->hasRole('admin')){
            $games = Games::all();
            return view('admin.index', ['games' => $games]);
        }
        return view('regular.index');
    }
}
