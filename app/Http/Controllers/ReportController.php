<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $from = $request->filled('from') ? $request->from : now()->startOfMonth()->toDateString();
        $to = $request->filled('to') ? $request->to : now()->endOfMonth()->toDateString();

        $totalIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');

        $totalExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');

        $net = $totalIncome - $totalExpense;

        // Expense breakdown by category
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($group) => (float) $group->sum('amount'));

        // Income breakdown by category
        $incomeByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(fn ($group) => (float) $group->sum('amount'));

        // Monthly trend (last 12 months)
        $monthlyTrend = collect(range(11, 0))->map(function ($i) use ($user, $from, $to) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $income = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $expense = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            return [
                'label' => $start->format('M Y'),
                'income' => $income,
                'expense' => $expense,
            ];
        });

        $accounts = Account::where('user_id', $user->id)->get();

        // Account balance report
        $accountBalances = $accounts->map(function ($account) {
            return [
                'name' => $account->name,
                'balance' => (float) $account->balance,
            ];
        });

        return view('admin.reports.index', compact(
            'from',
            'to',
            'totalIncome',
            'totalExpense',
            'net',
            'expenseByCategory',
            'incomeByCategory',
            'monthlyTrend',
            'accountBalances'
        ));
    }
}
