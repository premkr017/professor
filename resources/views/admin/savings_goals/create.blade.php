@extends('layouts.app')

@section('title', 'Create Savings Goal')
@section('page-title', 'Create Savings Goal')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">New Savings Goal</div>
                <div class="card-subtitle">Plan ahead for a laptop, travel, emergency fund, or anything else important.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('savings-goals.store') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Goal Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="New Laptop" required>
                </div>
                <div class="form-group">
                    <label for="target_amount">Target Amount</label>
                    <input type="number" id="target_amount" name="target_amount" step="0.01" min="0" value="{{ old('target_amount') }}" required>
                </div>
                <div class="form-group">
                    <label for="saved_amount">Amount Saved</label>
                    <input type="number" id="saved_amount" name="saved_amount" step="0.01" min="0" value="{{ old('saved_amount', 0) }}">
                </div>
                <div class="form-group">
                    <label for="deadline">Deadline</label>
                    <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}">
                </div>
                <div class="form-group" style="grid-column:1 / -1">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Add a short note about this goal.">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Goal</button>
                <a href="{{ route('savings-goals.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
