<?php

declare(strict_types=1);

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
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', Password::default()],
        ]);

        if (Auth::attempt($user)) {
            Auth::user()->refresh();

            return redirect('/')
                ->with('success', __('messages.login'));
        }

        return back()->withErrors([
            'email' => __('messages.bad_credentials'),
        ]);
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/')
            ->with('success', __('messages.logout'));
    }
}
