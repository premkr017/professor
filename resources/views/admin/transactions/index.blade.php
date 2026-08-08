@extends('layouts.app')

@section('title', 'Transactions')
@section('page-title', 'Transactions')

@section('content')
    {{-- Summary Stats --}}
    <div class="grid grid-3" style="margin-bottom:24px">
        <div class="stat-card">
            <div class="stat-icon green">📈</div>
            <div>
                <div class="stat-label">Total Income</div>
                <div class="stat-value">₹{{ number_format($totalIncome, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon red">📉</div>
            <div>
                <div class="stat-label">Total Expense</div>
                <div class="stat-value">-₹{{ number_format($totalExpense, 2) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">💼</div>
            <div>
                <div class="stat-label">Net</div>
                <div class="stat-value" style="{{ $net < 0 ? 'color:#dc2626' : '' }}">₹{{ number_format($net, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">All Transactions</div>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">+ Add Transaction</a>
        </div>

        {{-- Filter Bar --}}
        <form method="GET" action="{{ route('transactions.index') }}" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px">
            <select name="type" class="form-control" style="width:auto;min-width:130px">
                <option value="">All Types</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>Transfer</option>
            </select>

            <select name="account_id" class="form-control" style="width:auto;min-width:150px">
                <option value="">All Accounts</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>

            <select name="category_id" class="form-control" style="width:auto;min-width:150px">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>

            <input type="date" name="from" class="form-control" value="{{ request('from') }}" style="width:auto">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" style="width:auto">

            <button type="submit" class="btn btn-secondary">Filter</button>
            @if(request()->has('type') || request()->has('account_id') || request()->has('category_id') || request()->has('from') || request()->has('to'))
                <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-sm" style="align-self:center">Reset</a>
            @endif
        </form>

        @if($transactions->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Category</th>
                            <th>Account</th>
                            <th>Description</th>
                            <th>Method</th>
                            <th style="text-align:right">Amount</th>
                            <th>Receipt</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            @php
                                $methodLabel = \App\Models\Transaction::PAYMENT_METHODS[$transaction->payment_method] ?? ($transaction->payment_method ?? '-');
                            @endphp
                            <tr>
                                <td style="white-space:nowrap">{{ $transaction->transaction_date->format('d/m/Y') }}</td>
                                <td>
                                    @if($transaction->type === 'income')
                                        <span class="badge badge-green">Income</span>
                                    @elseif($transaction->type === 'expense')
                                        <span class="badge badge-red">Expense</span>
                                    @else
                                        <span class="badge badge-purple">Transfer</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->category)
                                        <span class="color-dot" style="background:{{ $transaction->category->color }}"></span>
                                        {{ $transaction->category->name }}
                                    @elseif($transaction->type === 'transfer')
                                        <span class="color-dot" style="background:#7c3aed"></span>
                                        {{ $transaction->toAccount->name ?? 'Transfer' }}
                                    @else
                                        <span style="color:#94a3b8">—</span>
                                    @endif
                                </td>
                                <td style="white-space:nowrap">
                                    {{ $transaction->account->name ?? '—' }}
                                    @if($transaction->type === 'transfer' && $transaction->toAccount)
                                        <span style="color:#94a3b8">→ {{ $transaction->toAccount->name }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $transaction->description ?: ($transaction->payee ?: '—') }}
                                </td>
                                <td><span class="badge badge-gray">{{ $methodLabel }}</span></td>
                                <td style="text-align:right">
                                    @if($transaction->type === 'income')
                                        <span class="amount-income">+₹{{ number_format($transaction->amount, 2) }}</span>
                                    @elseif($transaction->type === 'expense')
                                        <span class="amount-expense">-₹{{ number_format($transaction->amount, 2) }}</span>
                                    @else
                                        <span style="color:#7c3aed;font-weight:600">₹{{ number_format($transaction->amount, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($transaction->attachment)
                                        <a href="{{ asset('storage/' . $transaction->attachment) }}" target="_blank" title="View receipt" style="text-decoration:none">📎</a>
                                    @else
                                        <span style="color:#cbd5e1">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction?')">
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

            <div class="pagination">
                {{ $transactions->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="icon">💳</div>
                <p>No transactions found. Record your first income or expense.</p>
                <a href="{{ route('transactions.create') }}" class="btn btn-primary">Add Transaction</a>
            </div>
        @endif
    </div>
@endsection
