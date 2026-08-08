<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * Show the admin registration form.
     */
    public function showRegistrationForm()
    {
        return view('admin.register');
    }

    /**
     * Handle an admin registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'mobile'           => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'password'         => ['required', 'string', 'min:6'],
            'confirm_password' => ['required', 'string', 'same:password'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'   => $request->mobile,
            'password' => $request->password,
        ]);

// Seed default income sources (categories) for the new user
        IncomeController::seedDefaultSources($user->id);

        // Seed default expense categories for the new user
        ExpenseController::seedDefaultCategories($user->id);

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Handle an admin login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Log the admin out and redirect to the login form.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}

