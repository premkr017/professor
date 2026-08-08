<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['account', 'category', 'toAccount'])
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

        // Summary stats (all user transactions, not just filtered page)
        $all = Transaction::where('user_id', auth()->id());
        $totalIncome = (clone $all)->where('type', 'income')->sum('amount');
        $totalExpense = (clone $all)->where('type', 'expense')->sum('amount');
        $net = $totalIncome - $totalExpense;

        return view('admin.transactions.index', compact(
            'transactions', 'accounts', 'categories', 'totalIncome', 'totalExpense', 'net'
        ));
    }

    public function create()
    {
        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();
        $paymentMethods = Transaction::PAYMENT_METHODS;

        return view('admin.transactions.create', compact('accounts', 'categories', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTransaction($request);

        $data['user_id'] = auth()->id();

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        if (!empty($data['category_id'])) {
            $category = Category::find($data['category_id']);
            abort_if($category && $category->user_id !== auth()->id(), 403);
        }

        // Handle transfer target account
        $toAccount = null;
        if ($data['type'] === 'transfer') {
            $data['category_id'] = null;
            $toAccount = Account::findOrFail($data['to_account_id']);
            abort_if($toAccount->user_id !== auth()->id(), 403);
            abort_if($toAccount->id === $account->id, 422, 'Source and destination account cannot be the same.');
        } else {
            $data['to_account_id'] = null;
        }

        // Handle attachment upload
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction = Transaction::create($data);

        // Update account balances
        $this->applyToBalance($account, $data['type'], $data['amount'], $toAccount);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction #' . $transaction->id . ' recorded successfully.');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $accounts = Account::where('user_id', auth()->id())->get();
        $categories = Category::where('user_id', auth()->id())->get();
        $paymentMethods = Transaction::PAYMENT_METHODS;

        return view('admin.transactions.edit', compact('transaction', 'accounts', 'categories', 'paymentMethods'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $data = $this->validateTransaction($request);

        // Reverse old balance effect
        $oldAccount = $transaction->account;
        $oldToAccount = $transaction->toAccount;
        $this->reverseFromBalance($oldAccount, $transaction->type, $transaction->amount, $oldToAccount);

        $data['user_id'] = auth()->id();

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        // Handle transfer target account
        $toAccount = null;
        if ($data['type'] === 'transfer') {
            $data['category_id'] = null;
            $toAccount = Account::findOrFail($data['to_account_id']);
            abort_if($toAccount->user_id !== auth()->id(), 403);
            abort_if($toAccount->id === $account->id, 422, 'Source and destination account cannot be the same.');
        } else {
            $data['to_account_id'] = null;
}

        // Handle new attachment upload (replace old if exists)
        if ($request->hasFile('attachment')) {
            if ($transaction->attachment) {
                Storage::disk('public')->delete($transaction->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction->update($data);

        // Apply new balance effect
        $this->applyToBalance($account, $data['type'], $data['amount'], $toAccount);

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorizeOwnership($transaction);

        $this->reverseFromBalance($transaction->account, $transaction->type, $transaction->amount, $transaction->toAccount);

        if ($transaction->attachment) {
            Storage::disk('public')->delete($transaction->attachment);
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully.');
    }

    private function validateTransaction(Request $request): array
    {
        $rules = [
            'type'             => ['required', 'in:income,expense,transfer'],
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['nullable', 'exists:categories,id'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'payment_method'   => ['nullable', 'in:' . implode(',', array_keys(Transaction::PAYMENT_METHODS))],
            'transaction_date' => ['required', 'date'],
            'attachment'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,webp', 'max:5120'],
        ];

        // Add transfer-specific rules when type is transfer
        $rules['to_account_id'] = ['nullable', 'exists:accounts,id'];

        return $request->validate($rules);
    }

    private function applyToBalance(Account $account, string $type, float $amount, ?Account $toAccount = null)
    {
        if ($type === 'income') {
            $account->increment('balance', $amount);
        } elseif ($type === 'expense') {
            $account->decrement('balance', $amount);
        } elseif ($type === 'transfer' && $toAccount) {
            $account->decrement('balance', $amount);
            $toAccount->increment('balance', $amount);
        }
    }

    private function reverseFromBalance(Account $account, string $type, float $amount, ?Account $toAccount = null)
    {
        if ($type === 'income') {
            $account->decrement('balance', $amount);
        } elseif ($type === 'expense') {
            $account->increment('balance', $amount);
        } elseif ($type === 'transfer' && $toAccount) {
            $account->increment('balance', $amount);
            $toAccount->decrement('balance', $amount);
        }
    }

    private function authorizeOwnership(Transaction $transaction)
    {
        abort_if($transaction->user_id !== auth()->id(), 403);
    }
}
