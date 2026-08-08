@extends('layouts.app')

@section('title', 'Edit Budget')
@section('page-title', 'Edit Budget')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Update Budget</div>
                <div class="card-subtitle">Adjust the limit as your needs change.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('budgets.update', $budget) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Budget Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $budget->name) }}" placeholder="Food Budget">
                </div>
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">All Expenses</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $budget->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Budget Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount', $budget->amount) }}" required>
                </div>
                <div class="form-group">
                    <label for="period">Period</label>
                    <select id="period" name="period" required>
                        <option value="monthly" {{ old('period', $budget->period) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="weekly" {{ old('period', $budget->period) === 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="daily" {{ old('period', $budget->period) === 'daily' ? 'selected' : '' }}>Daily</option>
                        <option value="yearly" {{ old('period', $budget->period) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date" value="{{ old('start_date', $budget->start_date?->format('Y-m-d')) }}" required>
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date', $budget->end_date?->format('Y-m-d')) }}">
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Budget</button>
                <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
