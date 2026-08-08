<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Default income sources seeded for each user.
     */
    public const DEFAULT_SOURCES = [
        'Salary',
        'Freelancing',
        'Business',
        'Commission',
        'Interest',
        'Rent',
        'Investment',
        'Bonus',
        'Gift',
        'Other',
    ];

    /**
     * Seed the default income categories for a user (called on registration).
     */
    public static function seedDefaultSources(int $userId): void
    {
        $icons = [
            'Salary'      => '💼',
            'Freelancing' => '🧑‍💻',
            'Business'    => '🏢',
            'Commission'  => '🤝',
            'Interest'    => '🏦',
            'Rent'        => '🏠',
            'Investment'  => '📈',
            'Bonus'       => '🎁',
            'Gift'        => '🎀',
            'Other'       => '✨',
        ];

        $colors = [
            'Salary'      => '#16a34a',
            'Freelancing' => '#0ea5e9',
            'Business'    => '#7c3aed',
            'Commission'  => '#d97706',
            'Interest'    => '#059669',
            'Rent'        => '#2563eb',
            'Investment'  => '#8b5cf6',
            'Bonus'       => '#dc2626',
            'Gift'        => '#ec4899',
            'Other'       => '#64748b',
        ];

        foreach (self::DEFAULT_SOURCES as $source) {
            Category::firstOrCreate(
                [
                    'user_id'  => $userId,
                    'name'     => $source,
                    'type'     => 'income',
                ],
                [
                    'icon'      => $icons[$source] ?? '💰',
                    'color'     => $colors[$source] ?? '#6b7280',
                    'is_system' => true,
                ]
            );
        }
    }

    /**
     * Show the income management page.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Selected month (defaults to current month)
        $month = $request->filled('month') ? $request->month : now()->format('Y-m');
        $from = $month . '-01';
        $to = date('Y-m-t', strtotime($from));

        // Summary totals for the selected month
        $totalIncome = (float) Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$from, $to])
            ->sum('amount');

        $daysInMonth = (int) date('t', strtotime($from));
        $dayOfMonth = (int) date('j');
        $elapsed = in_array($month, [now()->format('Y-m')]) ? $dayOfMonth : $daysInMonth;
        $avgPerDay = $elapsed > 0 ? $totalIncome / $elapsed : 0;

        // Income sources configured (income categories)
        $sources = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->orderBy('name')
            ->get();

        // Monthly breakdown by source (category)
        $transactions = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
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
                'icon'     => $cat?->icon ?? '💰',
                'color'    => $cat?->color ?? '#6b7280',
                'amount'   => $amount,
                'count'    => $group->count(),
                'percent'  => $totalIncome > 0 ? round(($amount / $totalIncome) * 100, 1) : 0,
            ]);
        }

        $breakdown = $breakdown->sortByDesc('amount')->values();

        // Recent income transactions (for the selected month)
        $recentIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
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
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            return [
                'label'  => $start->format('M Y'),
                'amount' => $amount,
            ];
        });

        return view('admin.income.index', compact(
            'month',
            'totalIncome',
            'avgPerDay',
            'sources',
            'breakdown',
            'recentIncome',
            'accounts',
            'yearly'
        ));
    }

    /**
     * Store a new income transaction.
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
        abort_if($category->type !== 'income', 422, 'Selected category is not an income source.');

        $data['user_id'] = auth()->id();
        $data['type'] = 'income';
        $data['to_account_id'] = null;

        $transaction = Transaction::create($data);

        // Update account balance
        $account->increment('balance', $data['amount']);

        return redirect()->route('income.index', ['month' => $data['transaction_date']])
            ->with('success', 'Income of ₹' . number_format($data['amount'], 2) . ' recorded successfully.');
    }
}
