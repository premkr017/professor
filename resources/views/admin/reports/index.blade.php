@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('page-title', 'Reports & Analytics')

@section('content')
    <div class="card" style="margin-bottom:24px">
        <div class="card-header">
            <div>
                <div class="card-title">Financial Overview</div>
                <div class="card-subtitle">Analyze income, expenses, and account balances over time.</div>
            </div>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="form-grid" style="margin-top:12px">
            <div class="form-group">
                <label for="from">From</label>
                <input type="date" id="from" name="from" value="{{ request('from') }}">
            </div>
            <div class="form-group">
                <label for="to">To</label>
                <input type="date" id="to" name="to" value="{{ request('to') }}">
            </div>
            <div class="form-group">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-primary">Apply</button>
            </div>
        </form>
    </div>

    <div class="grid grid-3" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon green">💰</div>
            <div>
                <div class="stat-label">Total Income</div>
                <div class="stat-value">₹{{ number_format($totalIncome, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">🛒</div>
            <div>
                <div class="stat-label">Total Expense</div>
                <div class="stat-value">₹{{ number_format($totalExpense, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">📈</div>
            <div>
                <div class="stat-label">Net</div>
                <div class="stat-value">₹{{ number_format($net, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-title">Income vs Expense</div>
            <div style="margin-top:16px">
                <div style="font-size:13px;color:#64748b;margin-bottom:8px">Income</div>
                <div style="height:12px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-bottom:14px">
                    <div style="height:100%;width:{{ $totalIncome > 0 ? min(100, round(($totalIncome / max($totalIncome + $totalExpense, 1)) * 100)) : 0 }}%;background:#10b981"></div>
                </div>
                <div style="font-size:13px;color:#64748b;margin-bottom:8px">Expense</div>
                <div style="height:12px;background:#e2e8f0;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:{{ $totalExpense > 0 ? min(100, round(($totalExpense / max($totalIncome + $totalExpense, 1)) * 100)) : 0 }}%;background:#ef4444"></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">Expense by Category</div>
            <div style="margin-top:16px">
                @if($expenseByCategory->count())
                    @foreach($expenseByCategory as $name => $amount)
                        @php $share = $totalExpense > 0 ? round(($amount / $totalExpense) * 100) : 0; @endphp
                        <div style="margin-bottom:12px">
                            <div style="display:flex;justify-content:space-between;font-size:13px;color:#475569;margin-bottom:6px">
                                <span>{{ $name }}</span>
                                <span>{{ $share }}%</span>
                            </div>
                            <div style="height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden">
                                <div style="height:100%;width:{{ $share }}%;background:#3b82f6"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="color:#64748b">No expense data for the selected period.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:24px">
        <div class="card-title">Monthly Trend</div>
        <div style="margin-top:16px;display:grid;gap:12px">
            @foreach($monthlyTrend as $month)
                <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e2e8f0;padding-bottom:8px">
                    <span style="font-weight:600;color:#0f172a">{{ $month['label'] }}</span>
                    <span style="font-size:13px;color:#64748b">Income ₹{{ number_format($month['income'], 2) }} • Expense ₹{{ number_format($month['expense'], 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
