<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['account', 'category'])
            ->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('from')) {
            $query->whereDate('transaction_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('transaction_date', '<=', $request->to);
        }

        $transactions = $query->paginate(15)->withQueryString();

        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('admin.transactions.index', compact('transactions', 'accounts', 'categories'));
    }

    public function create()
    {
        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('admin.transactions.create', compact('accounts', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => ['required', 'in:income,expense,transfer'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        $data['user_id'] = auth()->id();

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        if (!empty($data['category_id'])) {
            $category = Category::find($data['category_id']);
            abort_if($category && $category->user_id !== auth()->id(), 403);
        }

        $transaction = Transaction::create($data);

        // Update account balance
        $this->applyToBalance($account, $data['type'], $data['amount']);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction #' . $transaction->id . ' recorded successfully.');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();

        return view('admin.transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $data = $request->validate([
            'type'             => ['required', 'in:income,expense,transfer'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        // Reverse old balance effect
        $oldAccount = $transaction->account;
        $this->reverseFromBalance($oldAccount, $transaction->type, $transaction->amount);

        $data['user_id'] = auth()->id();

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        $transaction->update($data);

        // Apply new balance effect
        $this->applyToBalance($account, $data['type'], $data['amount']);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $this->reverseFromBalance($transaction->account, $transaction->type, $transaction->amount);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    private function applyToBalance(Account $account, string $type, float $amount)
    {
        if ($type === 'income') {
            $account->increment('balance', $amount);
        } elseif ($type === 'expense') {
            $account->decrement('balance', $amount);
        }
    }

    private function reverseFromBalance(Account $account, string $type, float $amount)
    {
        if ($type === 'income') {
            $account->decrement('balance', $amount);
        } elseif ($type === 'expense') {
            $account->increment('balance', $amount);
        }
    }

    private function authorizeOwnership(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
    }
}
