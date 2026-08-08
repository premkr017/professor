<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();

        $stats = [
            'accounts' => $user->accounts()->count(),
            'transactions' => $user->transactions()->count(),
            'budgets' => $user->budgets()->count(),
            'savings_goals' => $user->savingsGoals()->count(),
            'loans' => $user->loans()->count(),
            'recurring' => $user->recurringTransactions()->count(),
        ];

        return view('admin.profile', compact('user', 'stats'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'mobile' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function changePassword(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update(['password' => $data['password']]);

        return back()->with('success', 'Password changed successfully.');
    }
}
