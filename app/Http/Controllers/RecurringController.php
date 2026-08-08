<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;

class RecurringController extends Controller
{
    public function index()
    {
        $recurrings = RecurringTransaction::where('user_id', auth()->id())
            ->with(['account', 'category'])
            ->latest()
            ->get();

        return view('admin.recurrings.index', compact('recurrings'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('admin.recurrings.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => ['required', 'in:income,expense'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:255'],
            'frequency'        => ['required', 'in:daily,weekly,monthly,yearly'],
            'start_date'       => ['required', 'date'],
            'next_date'        => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $data['user_id'] = auth()->id();
        $data['is_active'] = $request->boolean('is_active');

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        RecurringTransaction::create($data);

        return redirect()->route('recurrings.index')->with('success', 'Recurring transaction created successfully.');
    }

    public function edit(RecurringTransaction $recurring)
    {
        $this->authorizeOwnership($recurring);

        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('admin.recurrings.edit', compact('recurring', 'accounts', 'categories'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $this->authorizeOwnership($recurring);

        $data = $request->validate([
            'type'             => ['required', 'in:income,expense'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:255'],
            'frequency'        => ['required', 'in:daily,weekly,monthly,yearly'],
            'start_date'       => ['required', 'date'],
            'next_date'        => ['required', 'date'],
            'end_date'         => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'        => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $recurring->update($data);

        return redirect()->route('recurrings.index')->with('success', 'Recurring transaction updated successfully.');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $this->authorizeOwnership($recurring);
        $recurring->delete();

        return redirect()->route('recurrings.index')->with('success', 'Recurring transaction deleted successfully.');
    }

    private function authorizeOwnership(RecurringTransaction $recurring)
    {
        abort_if($recurring->user_id !== auth()->id(), 403);
    }
}
