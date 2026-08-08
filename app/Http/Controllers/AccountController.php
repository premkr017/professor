<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('user_id', auth()->id())->latest()->get();
        return view('admin.accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.accounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'type'     => ['required', 'in:cash,bank,card,e_wallet,other'],
            'balance'  => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'icon'     => ['nullable', 'string', 'max:50'],
            'color'    => ['nullable', 'string', 'max:7'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();
        $data['color'] = $data['color'] ?? '#2563eb';

        Account::create($data);

        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    public function edit(Account $account)
    {
        $this->authorizeOwnership($account);
        return view('admin.accounts.edit', compact('account'));
    }

    public function update(Request $request, Account $account)
    {
        $this->authorizeOwnership($account);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'type'     => ['required', 'in:cash,bank,card,e_wallet,other'],
            'balance'  => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:3'],
            'icon'     => ['nullable', 'string', 'max:50'],
            'color'    => ['nullable', 'string', 'max:7'],
            'notes'    => ['nullable', 'string', 'max:1000'],
        ]);

        $account->update($data);

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    public function destroy(Account $account)
    {
        $this->authorizeOwnership($account);
        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }

    private function authorizeOwnership(Account $account)
    {
        abort_if($account->user_id !== auth()->id(), 403);
    }
}
