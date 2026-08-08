<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
/**
     * Default expense categories seeded for each user.
     *
     * Each entry is: name => children ([] means no sub-categories).
     */
    public const DEFAULT_EXPENSE_CATEGORIES = [
        'Food'          => ['Grocery', 'Restaurant', 'Delivery'],
        'Rent'          => [],
        'Electricity'   => [],
        'Mobile'        => [],
        'Transport'     => ['Petrol', 'Bus', 'Train', 'Taxi'],
        'Shopping'      => [],
        'Education'     => [],
        'Medical'       => [],
        'Entertainment' => [],
        'EMI'           => [],
        'Bills'         => [],
        'Other'         => [],
    ];

    /**
     * Seed the default expense categories for a user (called on registration).
     */
    public static function seedDefaultCategories(int $userId): void
    {
        $icons = [
            'Food'          => '🍔',
            'Grocery'       => '🛒',
            'Restaurant'    => '🍽️',
            'Delivery'      => '🛵',
            'Rent'          => '🏠',
            'Electricity'   => '💡',
            'Mobile'        => '📱',
            'Transport'     => '🚗',
            'Petrol'        => '⛽',
            'Bus'           => '🚌',
            'Train'         => '🚆',
            'Taxi'          => '🚕',
            'Shopping'      => '🛍',
            'Education'     => '🎓',
            'Medical'       => '🏥',
            'Entertainment' => '🎬',
            'EMI'           => '💳',
            'Bills'         => '🧾',
            'Other'         => '📦',
        ];

        $colors = [
            'Food'          => '#ef4444',
            'Grocery'       => '#f97316',
            'Restaurant'    => '#f43f5e',
            'Delivery'      => '#eab308',
            'Rent'          => '#8b5cf6',
            'Electricity'   => '#f59e0b',
            'Mobile'        => '#10b981',
            'Transport'     => '#3b82f6',
            'Petrol'        => '#0ea5e9',
            'Bus'           => '#38bdf8',
            'Train'         => '#6366f1',
            'Taxi'          => '#f59e0b',
            'Shopping'      => '#ec4899',
            'Education'     => '#14b8a6',
            'Medical'       => '#dc2626',
            'Entertainment' => '#7c3aed',
            'EMI'           => '#d97706',
            'Bills'         => '#64748b',
            'Other'         => '#6b7280',
        ];

        foreach (self::DEFAULT_EXPENSE_CATEGORIES as $parentName => $children) {
            $parent = Category::firstOrCreate(
                [
                    'user_id'  => $userId,
                    'name'     => $parentName,
                    'type'     => 'expense',
                    'parent_id' => null,
                ],
                [
                    'icon'      => $icons[$parentName] ?? '📦',
                    'color'     => $colors[$parentName] ?? '#6b7280',
                    'is_system' => true,
                ]
            );

            foreach ($children as $childName) {
                Category::firstOrCreate(
                    [
                        'user_id'   => $userId,
                        'name'      => $childName,
                        'type'      => 'expense',
                        'parent_id' => $parent->id,
                    ],
                    [
                        'icon'      => $icons[$childName] ?? '📦',
                        'color'     => $colors[$childName] ?? '#6b7280',
                        'is_system' => true,
                    ]
                );
            }
        }
    }

    /**
     * Show the expense management page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Selected month (defaults to current month)
        $month = $request->filled('month') ? $request->month : now()->format('Y-m');
        $from = $month . '-01';
        $to = date('Y-m-t', strtotime($from));

        // Summary totals for the selected month
        $totalExpense = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');

        $daysInMonth = (int) date('t', strtotime($from));
        $dayOfMonth = (int) date('j');
        $elapsed = in_array($month, [now()->format('Y-m')]) ? $dayOfMonth : $daysInMonth;
        $avgPerDay = $elapsed > 0 ? $totalExpense / $elapsed : 0;

        // Expense categories configured
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'expense')
            ->orderBy('name')
            ->get();

        // Monthly breakdown by expense category
        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$from, $to])
            ->with('category')
            ->get();

        $breakdown = collect();
        $byCategory = $transactions->groupBy(function ($t) {
            return $t->category_id ?? 'uncategorized';
        });

        foreach ($byCategory as $categoryId => $group) {
            $amount = (float) $group->sum('amount');
            $cat = $group->first()->category;
            $breakdown->push([
                'id'       => $categoryId,
                'name'     => $cat?->name ?? 'Uncategorized',
                'icon'     => $cat?->icon ?? '📦',
                'color'    => $cat?->color ?? '#6b7280',
                'amount'   => $amount,
                'count'    => $group->count(),
                'percent'  => $totalExpense > 0 ? round(($amount / $totalExpense) * 100, 1) : 0,
            ]);
        }

        $breakdown = $breakdown->sortByDesc('amount')->values();

        // Recent expense transactions (for the selected month)
        $recentExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$from, $to])
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->take(20)
            ->get();

        // Available accounts for the quick-add form
        $accounts = Account::where('user_id', $user->id)->get();

        // Yearly summary for the past 6 months (for a small chart)
        $yearly = collect(range(5, 0))->map(function ($i) use ($user) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $amount = (float) Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            return [
                'label'  => $start->format('M Y'),
                'amount' => $amount,
            ];
        });

        return view('admin.expense.index', compact(
            'month',
            'totalExpense',
            'avgPerDay',
            'categories',
            'breakdown',
            'recentExpense',
            'accounts',
            'yearly'
        ));
    }

    /**
     * Store a new expense transaction.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount'           => ['required', 'numeric', 'gt:0'],
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['required', 'exists:categories,id'],
            'payee'            => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string', 'max:1000'],
            'payment_method'   => ['nullable', 'in:' . implode(',', array_keys(Transaction::PAYMENT_METHODS))],
            'transaction_date' => ['required', 'date'],
        ]);

        $account = Account::findOrFail($data['account_id']);
        abort_if($account->user_id !== auth()->id(), 403);

        $category = Category::findOrFail($data['category_id']);
        abort_if($category->user_id !== auth()->id(), 403);
        abort_if($category->type !== 'expense', 422, 'Selected category is not an expense category.');

        $data['user_id'] = auth()->id();
        $data['type'] = 'expense';
        $data['to_account_id'] = null;

        $transaction = Transaction::create($data);

        // Update account balance
        $account->decrement('balance', $data['amount']);

        return redirect()->route('expense.index', ['month' => $data['transaction_date']])
            ->with('success', 'Expense of ₹' . number_format($data['amount'], 2) . ' recorded successfully.');
    }
}

