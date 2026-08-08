<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public const ACCOUNT_TYPES = [
        'cash'          => 'Cash',
        'bank'          => 'Bank Account',
        'savings'       => 'Savings Account',
        'current'       => 'Current Account',
        'credit_card'   => 'Credit Card',
        'upi'           => 'UPI Wallet',
        'e_wallet'      => 'E-Wallet',
        'investment'    => 'Investment Account',
        'other'         => 'Other',
    ];

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
            'name'            => ['required', 'string', 'max:255'],
            'type'            => ['required', 'in:' . implode(',', array_keys(self::ACCOUNT_TYPES))],
            'bank_name'       => ['nullable', 'string', 'max:255'],
            'account_number'  => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric'],
            'balance'         => ['required', 'numeric'],
            'currency'        => ['required', 'string', 'max:3'],
            'icon'            => ['nullable', 'string', 'max:50'],
            'color'           => ['nullable', 'string', 'max:7'],
            'notes'           => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();
        $data['color'] = $data['color'] ?? '#2563eb';
        // Set initial balance from opening balance if provided.
        $data['balance'] = $data['opening_balance'] ?? $data['balance'];

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
            'name'            => ['required', 'string', 'max:255'],
            'type'            => ['required', 'in:' . implode(',', array_keys(self::ACCOUNT_TYPES))],
            'bank_name'       => ['nullable', 'string', 'max:255'],
            'account_number'  => ['nullable', 'string', 'max:255'],
            'opening_balance' => ['nullable', 'numeric'],
            'balance'         => ['required', 'numeric'],
            'currency'        => ['required', 'string', 'max:3'],
            'icon'            => ['nullable', 'string', 'max:50'],
            'color'           => ['nullable', 'string', 'max:7'],
            'notes'           => ['nullable', 'string', 'max:1000'],
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
