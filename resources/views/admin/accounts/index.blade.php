@extends('layouts.app')

@section('title', 'Accounts')
@section('page-title', 'Accounts / Wallets')

@section('content')
    @if($accounts->count())
        <div class="grid grid-3" style="margin-bottom:24px">
            @php
                $totalBalance = $accounts->sum('balance');
                $totalAssets = $accounts->where('balance', '>', 0)->sum('balance');
                $totalDebt = abs($accounts->where('balance', '<', 0)->sum('balance'));
            @endphp
            <div class="stat-card">
                <div class="stat-icon blue">💼</div>
                <div>
                    <div class="stat-label">Total Balance</div>
                    <div class="stat-value">₹{{ number_format($totalBalance, 2) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">📈</div>
                <div>
                    <div class="stat-label">Assets</div>
                    <div class="stat-value">₹{{ number_format($totalAssets, 2) }}</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">📉</div>
                <div>
                    <div class="stat-label">Debt/Credit Used</div>
                    <div class="stat-value">-₹{{ number_format($totalDebt, 2) }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="card-title">Your Accounts</div>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">+ Add Account</a>
        </div>

        @if($accounts->count())
            <div class="grid grid-3">
                @foreach($accounts as $account)
                    @php
                        $isNegative = $account->balance < 0;
                        $typeLabel = \App\Http\Controllers\AccountController::ACCOUNT_TYPES[$account->type] ?? ucfirst(str_replace('_', ' ', $account->type));
                        $maskedNumber = $account->account_number ? '••••' . substr($account->account_number, -4) : null;
                    @endphp
                    <div class="stat-card" style="align-items:flex-start;flex-direction:column;gap:14px">
                        <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
                            <span class="stat-icon" style="background:{{ $account->color }}22;color:{{ $account->color }}">
                                {{ $account->icon ?? '🏦' }}
                            </span>
                            <span class="badge {{ $isNegative ? 'badge-red' : 'badge-blue' }}">{{ $typeLabel }}</span>
                        </div>
                        <div style="width:100%">
                            <div class="stat-label">{{ $account->name }}</div>
                            <div class="stat-value" style="{{ $isNegative ? 'color:#dc2626' : '' }}">
                                {{ $isNegative ? '-' : '' }}{{ $account->currency }} {{ number_format(abs($account->balance), 2) }}
                            </div>
                            @if($account->bank_name)
                                <div style="font-size:13px;color:#64748b;margin-top:4px">🏛️ {{ $account->bank_name }}</div>
                            @endif
                            @if($maskedNumber)
                                <div style="font-size:13px;color:#94a3b8;margin-top:2px">💳 {{ $maskedNumber }}</div>
                            @endif
                            @if($account->notes)
                                <div style="font-size:13px;color:#94a3b8;margin-top:4px">{{ $account->notes }}</div>
                            @endif
                        </div>
                        <div style="display:flex;gap:8px;width:100%">
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-secondary btn-sm" style="flex:1;justify-content:center">Edit</a>
                            <form method="POST" action="{{ route('accounts.destroy', $account) }}" style="flex:1" onsubmit="return confirm('Delete this account? Its transactions will also be deleted.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="width:100%;justify-content:center">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="icon">🏦</div>
                <p>You don't have any accounts yet. Add your first wallet or bank account.</p>
                <a href="{{ route('accounts.create') }}" class="btn btn-primary">Add Account</a>
            </div>
        @endif
    </div>
@endsection

