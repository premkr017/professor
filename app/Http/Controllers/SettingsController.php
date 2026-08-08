<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('admin.settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'currency' => ['required', 'string', 'max:3'],
            'locale'   => ['nullable', 'string', 'max:10'],
        ]);

        // Persist settings as JSON on the user record
        $settings = array_merge($user->settings ?? [], $data);
        $user->update(['settings' => $settings]);

        return back()->with('success', 'Settings updated successfully.');
    }
}
