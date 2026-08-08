@extends('layouts.app')

@section('title', 'Budgets')
@section('page-title', 'Budgets')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Monthly Budget Overview</div>
                <div class="card-subtitle">Track your spending limits and stay in control of every category.</div>
            </div>
            <a href="{{ route('budgets.create') }}" class="btn btn-primary">+ Create Budget</a>
        </div>

        @if($budgets->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Budget</th>
                            <th>Category</th>
                            <th>Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($budgets as $budget)
                            @php
                                $percentage = $budget->amount > 0 ? min(100, round(($budget->spent / $budget->amount) * 100)) : 0;
                                $isOver = $budget->spent > $budget->amount;
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:700;color:#0f172a">{{ $budget->name ?: 'Budget' }}</div>
                                    <div style="font-size:12px;color:#64748b">{{ $budget->start_date->format('M d') }} - {{ $budget->end_date ? $budget->end_date->format('M d') : 'End of month' }}</div>
                                </td>
                                <td>{{ $budget->category?->name ?? 'All Expenses' }}</td>
                                <td>{{ ucfirst($budget->period) }}</td>
                                <td>₹{{ number_format($budget->amount, 2) }}</td>
                                <td>
                                    <div style="display:flex;flex-direction:column;gap:6px">
                                        <span class="badge {{ $isOver ? 'badge-red' : 'badge-green' }}">{{ $isOver ? 'Over Budget' : 'On Track' }}</span>
                                        <div style="font-size:12px;color:#64748b">Spent ₹{{ number_format($budget->spent ?? 0, 2) }} • {{ $percentage }}%</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('budgets.edit', $budget) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Delete this budget?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <div class="icon">🎯</div>
                <p>No budgets yet. Create your first budget to keep spending under control.</p>
                <a href="{{ route('budgets.create') }}" class="btn btn-primary">Create Budget</a>
            </div>
        @endif
    </div>
@endsection
