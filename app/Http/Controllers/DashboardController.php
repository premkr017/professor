<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Loan;
use App\Models\SavingsGoal;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $totalBalance = (float) Account::where('user_id', $user->id)->sum('balance');

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $monthlyIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthlyExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->take(8)
            ->get();

        $accounts = Account::where('user_id', $user->id)->get();

        $totalSavings = (float) SavingsGoal::where('user_id', $user->id)->sum('saved_amount');

        $activeLoans = Loan::where('user_id', $user->id)->where('status', 'active')->get();
        $totalDebt = (float) $activeLoans->sum(fn ($l) => $l->total_amount - $l->paid_amount);

        $budgets = Budget::where('user_id', $user->id)
            ->with('category')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now()->toDateString());
            })
            ->take(5)
            ->get();

        // Monthly income vs expense for chart (last 6 months)
        $chartData = collect(range(5, 0))->map(function ($i) use ($user) {
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
                'label'  => $start->format('M'),
                'income' => $income,
                'expense'=> $expense,
            ];
        });

        return view('admin.dashboard', compact(
            'totalBalance',
            'monthlyIncome',
            'monthlyExpense',
            'recentTransactions',
            'accounts',
            'totalSavings',
            'totalDebt',
            'budgets',
            'chartData'
        ));
    }
}
