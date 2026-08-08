@extends('layouts.app')

@section('title', 'Accounts')
@section('page-title', 'Accounts / Wallets')

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="card-title">Your Accounts</div>
            <a href="{{ route('accounts.create') }}" class="btn btn-primary">+ Add Account</a>
        </div>

        @if($accounts->count())
            <div class="grid grid-3">
                @foreach($accounts as $account)
                    <div class="stat-card" style="align-items:flex-start;flex-direction:column;gap:14px">
                        <div style="display:flex;align-items:center;justify-content:space-between;width:100%">
                            <span class="stat-icon" style="background:{{ $account->color }}22;color:{{ $account->color }}">
                                {{ $account->icon ?? '🏦' }}
                            </span>
                            <span class="badge badge-blue">{{ ucfirst(str_replace('_', ' ', $account->type)) }}</span>
                        </div>
                        <div>
                            <div class="stat-label">{{ $account->name }}</div>
                            <div class="stat-value">{{ $account->currency }} {{ number_format($account->balance, 2) }}</div>
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
