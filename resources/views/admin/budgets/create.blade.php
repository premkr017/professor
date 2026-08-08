@extends('layouts.app')

@section('title', 'Create Budget')
@section('page-title', 'Create Budget')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">New Budget</div>
                <div class="card-subtitle">Set a spending cap for a category or for all expenses in a period.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('budgets.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Budget Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Food Budget">
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">All Expenses</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Budget Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount') }}" required>
                </div>
                <div class="form-group">
                    <label for="period">Period</label>
                    <select id="period" name="period" required>
                        <option value="monthly" {{ old('period', 'monthly') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="weekly" {{ old('period') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="daily" {{ old('period') === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="yearly" {{ old('period') === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Budget</button>
                <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
