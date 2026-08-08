@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Overview')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('trendChart');
            if (ctx) {
                const data = @json($chartData);
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.label),
                        datasets: [
                            {
                                label: 'Income',
                                data: data.map(d => d.income),
                                backgroundColor: '#16a34a',
                                borderRadius: 6
                            },
                            {
                                label: 'Expenses',
                                data: data.map(d => d.expense),
                                backgroundColor: '#dc2626',
                                borderRadius: 6
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: v => '$' + v.toLocaleString() }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    <div class="grid grid-4">
        <div class="stat-card">
            <div class="stat-icon blue">🏦</div>
            <div>
                <div class="stat-label">Total Balance</div>
                <div class="stat-value">${{ number_format($totalBalance, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">📈</div>
            <div>
                <div class="stat-label">Income (Month)</div>
                <div class="stat-value">${{ number_format($monthlyIncome, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">📉</div>
            <div>
                <div class="stat-label">Expenses (Month)</div>
                <div class="stat-value">${{ number_format($monthlyExpense, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">💰</div>
            <div>
                <div class="stat-label">Savings</div>
                <div class="stat-value">${{ number_format($totalSavings, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Income vs Expenses <span style="font-size:13px;color:#94a3b8">(last 6 months)</span></div>
            </div>
            <canvas id="trendChart" height="120"></canvas>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Accounts</div>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            @if($accounts->count())
                @foreach($accounts as $account)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid #f1f5f9">
                        <div style="display:flex;align-items:center;gap:12px">
                            <span class="stat-icon" style="width:40px;height:40px;background:{{ $account->color }}22;color:{{ $account->color }}">
                                {{ $account->icon ?? '🏦' }}
                            </span>
                            <div>
                                <div style="font-weight:600;color:#0f172a">{{ $account->name }}</div>
                                <div style="font-size:12px;color:#94a3b8">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</div>
                            </div>
                        </div>
                        <div style="font-weight:700;color:#0f172a">${{ number_format($account->balance, 2) }}</div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="icon">🏦</div>
                    <p>No accounts yet</p>
                    <a href="{{ route('accounts.create') }}" class="btn btn-primary btn-sm">Add Account</a>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Recent Transactions</div>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary btn-sm">View All</a>
            </div>
            @if($recentTransactions->count())
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Account</th>
                                <th style="text-align:right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $txn)
                                <tr>
                                    <td>{{ $txn->transaction_date->format('M d') }}</td>
                                    <td>
                                        {{ $txn->description ?: ($txn->category->name ?? 'N/A') }}
                                        @if($txn->payee)
                                            <div style="font-size:12px;color:#94a3b8">{{ $txn->payee }}</div>
                                        @endif
                                    </td>
                                    <td>{{ $txn->account->name }}</td>
                                    <td style="text-align:right">
                                        <span class="{{ $txn->type === 'income' ? 'amount-income' : 'amount-expense' }}">
                                            {{ $txn->type === 'income' ? '+' : '-' }} ${{ number_format($txn->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="icon">💳</div>
                    <p>No transactions yet</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-sm">Add Transaction</a>
                </div>
            @endif
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Budget Health</div>
                <a href="{{ route('budgets.index') }}" class="btn btn-secondary btn-sm">Manage</a>
            </div>
            @if($budgets->count())
                @foreach($budgets as $budget)
                    @php
                        $pct = $budget->amount > 0 ? round(($budget->spent / $budget->amount) * 100) : 0;
                        $barClass = $pct > 100 ? 'red' : ($pct > 75 ? 'amber' : 'green');
                    @endphp
                    <div style="margin-bottom:18px">
                        <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                            <span style="font-weight:600;color:#334155">{{ $budget->name ?: ($budget->category->name ?? 'General') }}</span>
                            <span style="font-size:13px;color:#64748b">${{ number_format($budget->spent, 0) }} / ${{ number_format($budget->amount, 0) }}</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $barClass }}" style="width:{{ min($pct, 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-state">
                    <div class="icon">🎯</div>
                    <p>No budgets set for this period</p>
                    <a href="{{ route('budgets.create') }}" class="btn btn-primary btn-sm">Create Budget</a>
                </div>
            @endif

            <hr style="margin:20px 0;border:none;border-top:1px solid #f1f5f9">

            <div style="display:flex;justify-content:space-between;align-items:center">
                <div>
                    <div class="stat-label">Total Debt</div>
                    <div class="stat-value" style="color:#dc2626">${{ number_format($totalDebt, 2) }}</div>
                </div>
                <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-sm">View Loans</a>
            </div>
        </div>
    </div>
@endsection
