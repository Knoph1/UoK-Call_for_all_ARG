<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CommonPagesController extends Controller
{
    //default/index/root page for the portal
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('pages.dashboard');
        }
        return view('pages.common.home');
    }

    //
    public function setupadmin()
    {
        $allusers = User::all();
        return view('pages.common.setupadmin', compact('allusers'));
    }


    public function makeInitialAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,userid',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->isadmin = true;
        $user->role = 1;
        $user->save();

        return redirect()->back()->with('success', 'User has been set as Admin.');
    }

    //About ARG portal page
    public function about()
    {
        return view('pages.common.about');
    }


    //login page
    public function login()
    {
        return view('pages.auth.login');
    }

    //Register new account page
    public function register()
    {
        return view('pages.auth.register');
    }
}
