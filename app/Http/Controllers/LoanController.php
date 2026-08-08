<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::where('user_id', auth()->id())->latest()->get();
        return view('admin.loans.index', compact('loans'));
    }

    public function create()
    {
        return view('admin.loans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:loan,debt'],
            'total_amount'  => ['required', 'numeric', 'gt:0'],
            'paid_amount'   => ['nullable', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'due_date'      => ['nullable', 'date'],
            'status'        => ['required', 'in:active,closed'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $data['user_id'] = auth()->id();
        $data['paid_amount'] = $data['paid_amount'] ?? 0;
        $data['interest_rate'] = $data['interest_rate'] ?? 0;

        Loan::create($data);

        return redirect()->route('loans.index')->with('success', 'Loan added successfully.');
    }

    public function edit(Loan $loan)
    {
        $this->authorizeOwnership($loan);
        return view('admin.loans.edit', compact('loan'));
    }

    public function update(Request $request, Loan $loan)
    {
        $this->authorizeOwnership($loan);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:loan,debt'],
            'total_amount'  => ['required', 'numeric', 'gt:0'],
            'paid_amount'   => ['nullable', 'numeric', 'min:0'],
            'interest_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'due_date'      => ['nullable', 'date'],
            'status'        => ['required', 'in:active,closed'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ]);

        $loan->update($data);

        return redirect()->route('loans.index')->with('success', 'Loan updated successfully.');
    }

    public function destroy(Loan $loan)
    {
        $this->authorizeOwnership($loan);
        $loan->delete();

        return redirect()->route('loans.index')->with('success', 'Loan deleted successfully.');
    }

    private function authorizeOwnership(Loan $loan)
    {
        abort_if($loan->user_id !== auth()->id(), 403);
    }
}
