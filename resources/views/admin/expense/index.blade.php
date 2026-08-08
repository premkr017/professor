@extends('layouts.app')

@section('title', 'Expense Management')
@section('page-title', '🛒 Expense Management')

@php
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $month)->format('F Y');
@endphp

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('expenseTrendChart');
            if (ctx) {
                const data = @json($yearly);
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(d => d.label),
                        datasets: [{
                            label: 'Expense',
                            data: data.map(d => d.amount),
                            backgroundColor: '#ef4444',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { callback: v => '₹' + v.toLocaleString() }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush

@section('content')
    {{-- Month selector --}}
    <div class="card" style="padding:16px 24px;margin-bottom:24px">
        <form method="GET" action="{{ route('expense.index') }}" style="display:flex;align-items:center;gap:14px;flex-wrap:wrap">
            <div>
                <div class="stat-label">Showing expenses for</div>
                <div style="font-weight:700;color:#0f172a;font-size:18px">{{ $monthLabel }}</div>
            </div>
            <div style="flex:1"></div>
            <input type="month" name="month" class="form-control" value="{{ $month }}" style="width:auto">
            <button type="submit" class="btn btn-secondary">Go</button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-3" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon red">📉</div>
            <div>
                <div class="stat-label">Total Expense ({{ $monthLabel }})</div>
                <div class="stat-value">₹{{ number_format($totalExpense, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">🔢</div>
            <div>
                <div class="stat-label">Expense Categories Used</div>
                <div class="stat-value">{{ $breakdown->count() }} <span style="font-size:14px;color:#94a3b8">/ {{ $categories->count() }}</span></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">📅</div>
            <div>
                <div class="stat-label">Average / Day</div>
                <div class="stat-value">₹{{ number_format($avgPerDay, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-2">
        {{-- Quick Add Expense --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">➕ Record Expense</div>
            </div>
            <form method="POST" action="{{ route('expense.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="amount">Amount *</label>
                        <div class="amount-input">
                            <span class="amount-prefix">₹</span>
                            <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" placeholder="500" required>
                        </div>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="transaction_date">Date *</label>
                        <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category_id">Expense Category *</label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->icon ? $category->icon . ' ' : '' }}{{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="account_id">Account *</label>
                        <select name="account_id" id="account_id" class="form-control" required>
                            <option value="">-- Select Account --</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} ({{ $account->currency }} {{ number_format($account->balance, 2) }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="payee">Paid To / Payee</label>
                        <input type="text" name="payee" id="payee" class="form-control" value="{{ old('payee') }}" placeholder="e.g. Big Bazaar, Jio, Landlord">
                        @error('payee')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control">
                            <option value="">-- Select --</option>
                            @foreach(\App\Models\Transaction::PAYMENT_METHODS as $value => $label)
                                <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="2" placeholder="Monthly grocery">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-danger">+ Add Expense</button>
                <a href="{{ route('transactions.create') }}" class="btn btn-secondary" style="margin-left:8px">Advanced</a>
            </form>
        </div>

        {{-- Expense Breakdown by Category --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title">{{ $monthLabel }} Expenses</div>
                <a href="{{ route('transactions.index', ['type' => 'expense']) }}" class="btn btn-secondary btn-sm">View All</a>
            </div>

            @if($breakdown->count())
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th style="text-align:right">Amount</th>
                                <th style="text-align:right">%</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($breakdown as $row)
                                <tr>
                                    <td>
                                        <span class="color-dot" style="background:{{ $row['color'] }}"></span>
                                        {{ $row['icon'] }} {{ $row['name'] }}
                                        <div style="font-size:12px;color:#94a3b8">{{ $row['count'] }} transaction{{ $row['count'] != 1 ? 's' : '' }}</div>
                                    </td>
                                    <td style="text-align:right;font-weight:600;color:#dc2626">₹{{ number_format($row['amount'], 2) }}</td>
                                    <td style="text-align:right">
                                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                                            <div class="progress" style="width:70px;flex-shrink:0"><div class="progress-bar red" style="width:{{ min($row['percent'], 100) }}%"></div></div>
                                            <span style="font-size:13px;color:#64748b">{{ $row['percent'] }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="font-weight:700;color:#0f172a;border-top:2px solid #e2e8f0">Total</td>
                                <td style="text-align:right;font-weight:700;color:#dc2626;border-top:2px solid #e2e8f0">₹{{ number_format($totalExpense, 2) }}</td>
                                <td style="text-align:right;font-weight:700;border-top:2px solid #e2e8f0">100%</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="icon">🛒</div>
                    <p>No expenses recorded for {{ $monthLabel }} yet.</p>
                    <p style="font-size:13px">Use the "Record Expense" form to add your first expense.</p>
                </div>
            @endif

            <hr style="margin:20px 0;border:none;border-top:1px solid #f1f5f9">

            {{-- Expense trend (last 6 months) --}}
            <div class="card-title" style="margin-bottom:12px">Expense Trend <span style="font-size:13px;color:#94a3b8">(last 6 months)</span></div>
            <canvas id="expenseTrendChart" height="120"></canvas>
        </div>
    </div>

    {{-- Recent Expense Transactions --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">Recent Expenses <span style="font-size:13px;color:#94a3b8">({{ $monthLabel }})</span></div>
        </div>
        @if($recentExpense->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Account</th>
                            <th>Paid To</th>
                            <th>Method</th>
                            <th style="text-align:right">Amount</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentExpense as $txn)
                            @php
                                $methodLabel = \App\Models\Transaction::PAYMENT_METHODS[$txn->payment_method] ?? ($txn->payment_method ?? '-');
                            @endphp
                            <tr>
                                <td style="white-space:nowrap">{{ $txn->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($txn->category)
                                        <span class="color-dot" style="background:{{ $txn->category->color }}"></span>
                                        {{ $txn->category->icon ? $txn->category->icon . ' ' : '' }}{{ $txn->category->name }}
                                    @else
                                        <span style="color:#94a3b8">Uncategorized</span>
                                    @endif
                                </td>
                                <td>{{ $txn->account->name ?? '—' }}</td>
                                <td>{{ $txn->payee ?: '—' }}</td>
                                <td><span class="badge badge-gray">{{ $methodLabel }}</span></td>
                                <td style="text-align:right"><span class="amount-expense">-₹{{ number_format($txn->amount, 2) }}</span></td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('transactions.edit', $txn) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $txn) }}" onsubmit="return confirm('Delete this expense?')">
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
                <div class="icon">📭</div>
                <p>No expense transactions recorded for {{ $monthLabel }}.</p>
            </div>
        @endif
    </div>
@endsection

@push('styles')
<style>
    .amount-input {
        position: relative;
    }
    .amount-prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 16px;
        font-weight: 700;
        color: #64748b;
    }
    .amount-input input {
        padding-left: 34px;
    }
</style>
@endpush

