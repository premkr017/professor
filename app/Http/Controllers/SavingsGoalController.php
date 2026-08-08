<?php

namespace App\Http\Controllers;

use App\Models\SavingsGoal;
use Illuminate\Http\Request;

class SavingsGoalController extends Controller
{
    public function index()
    {
        $goals = SavingsGoal::where('user_id', auth()->id())->latest()->get();
        return view('admin.savings_goals.index', compact('goals'));
    }

    public function create()
    {
        return view('admin.savings_goals.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'gt:0'],
            'saved_amount'  => ['nullable', 'numeric', 'min:0'],
            'deadline'      => ['nullable', 'date'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();
        $data['saved_amount'] = $data['saved_amount'] ?? 0;

        SavingsGoal::create($data);

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal created successfully.');
    }

    public function edit(SavingsGoal $goal)
    {
        $this->authorizeOwnership($goal);
        return view('admin.savings_goals.edit', compact('goal'));
    }

    public function update(Request $request, SavingsGoal $goal)
    {
        $this->authorizeOwnership($goal);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'gt:0'],
            'saved_amount'  => ['nullable', 'numeric', 'min:0'],
            'deadline'      => ['nullable', 'date'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $goal->update($data);

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal updated successfully.');
    }

    public function destroy(SavingsGoal $goal)
    {
        $this->authorizeOwnership($goal);
        $goal->delete();

        return redirect()->route('savings-goals.index')->with('success', 'Savings goal deleted successfully.');
    }

    private function authorizeOwnership(SavingsGoal $goal)
    {
        abort_if($goal->user_id !== auth()->id(), 403);
    }
}
