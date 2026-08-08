<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Loan;
use App\Models\RecurringTransaction;
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

        // Upcoming / pending (from recurring transactions within next 14 days)
        $upcomingRecurrings = RecurringTransaction::where('user_id', $user->id)
            ->where('is_active', true)
            ->whereBetween('next_date', [now()->toDateString(), now()->addDays(14)->toDateString()])
            ->with(['account', 'category'])
            ->orderBy('next_date')
            ->get();

        // Expense breakdown for current month (by category)
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get()
            ->map(function ($row) {
                $cat = \App\Models\Category::find($row->category_id);
                return [
                    'label' => $cat?->name ?? 'Uncategorized',
                    'value' => (float) $row->total,
                ];
            });

        // Budget progress (spent vs amount)
        $budgetProgress = $budgets->map(function ($b) use ($user) {
            $start = $b->start_date ?? now()->startOfMonth();
            $end = $b->end_date ?? now()->endOfMonth();

            $spent = (float) Transaction::where('user_id', $user->id)
                ->where('category_id', $b->category_id)
                ->whereBetween('transaction_date', [$start, $end])
                ->where('type', 'expense')
                ->sum('amount');

            $percent = $b->amount > 0 ? min(100, ($spent / $b->amount) * 100) : 0;

            return [
                'budget'   => $b,
                'spent'    => $spent,
                'percent'  => round($percent, 1),
            ];
        });

        // Savings goals progress
        $savingsGoals = SavingsGoal::where('user_id', $user->id)->get()->map(function ($s) {
            $percent = $s->target_amount > 0 ? round(($s->saved_amount / $s->target_amount) * 100, 1) : 0;
            return ['goal' => $s, 'percent' => $percent];
        });

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
            'chartData',
            'upcomingRecurrings',
            'expenseByCategory',
            'budgetProgress',
            'savingsGoals'
        ));
    }
}
