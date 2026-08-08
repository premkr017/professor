@extends('layouts.app')

@section('title', 'Create Recurring Transaction')
@section('page-title', 'Create Recurring Transaction')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">New Recurring Entry</div>
                <div class="card-subtitle">Set up a repeating income or expense to save time each month.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('recurrings.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        <option value="expense" {{ old('type', 'expense') === 'expense' ? 'selected' : '' }}>Expense</option>
                        <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>Income</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <input type="text" id="description" name="description" value="{{ old('description') }}" placeholder="Netflix subscription">
                </div>
                <div class="form-group">
                    <label for="account_id">Account</label>
                    <select id="account_id" name="account_id" required>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Uncategorized</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount') }}" required>
                </div>
                <div class="form-group">
                    <label for="frequency">Frequency</label>
                    <select id="frequency" name="frequency" required>
                        <option value="monthly" {{ old('frequency', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="weekly" {{ old('frequency') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="yearly" {{ old('frequency') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="next_date">Next Date</label>
                    <input type="date" id="next_date" name="next_date" value="{{ old('next_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}">
                </div>
                <div class="form-group">
                    <label for="is_active">Status</label>
                    <select id="is_active" name="is_active">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Paused</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Recurring</button>
                <a href="{{ route('recurrings.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
