<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class SessionsContoller extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $user = $request->validate([
            'email' => ['required', 'string','email', 'max:255'],
            'password' => ['required', Password::default()],
        ]);

        if (Auth::attempt($user)) {
            return redirect('/');
        }else
        {
            return back()->withErrors([
                'email' => 'Los unis emaila ili sifre'
            ]);
        }
    }

    public function destroy()
    {
        Auth::logout();
        return redirect('/');
    }
}
