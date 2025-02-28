<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the login input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check credentials
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Successful login
            return redirect()->route('dashboard'); // Redirect to the dashboard
        } else {
            // Failed login
            return back()->with('error', 'Invalid email or password!');
        }
    }
}
