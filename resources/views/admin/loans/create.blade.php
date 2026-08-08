@extends('layouts.app')

@section('title', 'Add Loan/Debt')
@section('page-title', 'Add Loan/Debt')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">New Loan / Debt</div>
                <div class="card-subtitle">Track loans you owe and debts owed to you.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('loans.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="loan" {{ old('type', 'loan') === 'loan' ? 'selected' : '' }}>Loan</option>
                        <option value="debt" {{ old('type') === 'debt' ? 'selected' : '' }}>Debt</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="total_amount">Total Amount</label>
                    <input type="number" id="total_amount" name="total_amount" step="0.01" min="0" value="{{ old('total_amount') }}" required>
                </div>
                <div class="form-group">
                    <label for="paid_amount">Paid Amount</label>
                    <input type="number" id="paid_amount" name="paid_amount" step="0.01" min="0" value="{{ old('paid_amount', 0) }}">
                </div>
                <div class="form-group">
                    <label for="interest_rate">Interest Rate (%)</label>
                    <input type="number" id="interest_rate" name="interest_rate" step="0.01" min="0" max="100" value="{{ old('interest_rate', 0) }}">
                </div>
                <div class="form-group">
                    <label for="due_date">Due Date</label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}">
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:1 / -1">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Add any reminder or detail.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Record</button>
                <a href="{{ route('loans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
