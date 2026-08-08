@extends('layouts.app')

@section('title', 'Admin')
@section('page-title', 'Admin Panel')

@section('content')
    <div class="grid grid-4">
        <div class="stat-card">
            <div class="stat-icon blue">👥</div>
            <div>
                <div class="stat-label">Users</div>
                <div class="stat-value">1</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">💳</div>
            <div>
                <div class="stat-label">Transactions</div>
                <div class="stat-value">{{ \App\Models\Transaction::count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon amber">🏷️</div>
            <div>
                <div class="stat-label">Categories</div>
                <div class="stat-value">{{ \App\Models\Category::count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon purple">🔔</div>
            <div>
                <div class="stat-label">Notifications</div>
                <div class="stat-value">{{ \App\Models\AppNotification::count() }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="card-title">Administration Quick Actions</div>
        </div>
        <div class="grid grid-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-primary">Manage Transactions</a>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Manage Categories</a>
            <a href="{{ route('budgets.index') }}" class="btn btn-secondary">Manage Budgets</a>
            <a href="{{ route('reports.index') }}" class="btn btn-secondary">Open Reports</a>
        </div>
    </div>
@endsection
