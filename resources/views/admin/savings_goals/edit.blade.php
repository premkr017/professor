@extends('layouts.app')

@section('title', 'Edit Savings Goal')
@section('page-title', 'Edit Savings Goal')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Update Savings Goal</div>
                <div class="card-subtitle">Revise the target or progress as your plans evolve.</div>
            </div>
        </div>

        <form method="POST" action="{{ route('savings-goals.update', $goal) }}">
            @csrf
            @method('PUT')
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Goal Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $goal->name) }}" placeholder="New Laptop" required>
                </div>
                <div class="form-group">
                    <label for="target_amount">Target Amount</label>
                    <input type="number" id="target_amount" name="target_amount" step="0.01" min="0" value="{{ old('target_amount', $goal->target_amount) }}" required>
                </div>
                <div class="form-group">
                    <label for="saved_amount">Amount Saved</label>
                    <input type="number" id="saved_amount" name="saved_amount" step="0.01" min="0" value="{{ old('saved_amount', $goal->saved_amount) }}">
                </div>
                <div class="form-group">
                    <label for="deadline">Deadline</label>
                    <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $goal->deadline?->format('Y-m-d')) }}">
                </div>
                <div class="form-group" style="grid-column:1 / -1">
                    <label for="notes">Notes</label>
                    <textarea id="notes" name="notes" rows="4" placeholder="Add a short note about this goal.">{{ old('notes', $goal->notes) }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Goal</button>
                <a href="{{ route('savings-goals.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
