<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->get();

        // Compute spent amount per budget for current period
        foreach ($budgets as $budget) {
            $start = $budget->start_date;
            $end = $budget->end_date ?? now()->endOfMonth();

            $budget->spent = (float) Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$start, $end])
                ->where(function ($q) use ($budget) {
                    if ($budget->category_id) {
                        $q->where('category_id', $budget->category_id);
                    }
                })
                ->sum('amount');
        }

        return view('admin.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $categories = Category::where('user_id', auth()->id())->where('type', 'expense')->get();
        return view('admin.budgets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount'      => ['required', 'numeric', 'gt:0'],
            'period'      => ['required', 'in:daily,weekly,monthly,yearly'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['user_id'] = auth()->id();

        if (!empty($data['category_id'])) {
            $category = Category::find($data['category_id']);
            abort_if($category && $category->user_id !== auth()->id(), 403);
        }

        Budget::create($data);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    public function edit(Budget $budget)
    {
        $this->authorizeOwnership($budget);
        $categories = Category::where('user_id', auth()->id())->where('type', 'expense')->get();
        return view('admin.budgets.edit', compact('budget', 'categories'));
    }

    public function update(Request $request, Budget $budget)
    {
        $this->authorizeOwnership($budget);

        $data = $request->validate([
            'name'        => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'amount'      => ['required', 'numeric', 'gt:0'],
            'period'      => ['required', 'in:daily,weekly,monthly,yearly'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $budget->update($data);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    public function destroy(Budget $budget)
    {
        $this->authorizeOwnership($budget);
        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }

    private function authorizeOwnership(Budget $budget)
    {
        abort_if($budget->user_id !== auth()->id(), 403);
    }
}
