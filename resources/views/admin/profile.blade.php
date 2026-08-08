@extends('layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Personal Information</div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Save Profile</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Security</div>
            </div>

            <form method="POST" action="{{ route('profile.password') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input id="current_password" type="password" name="current_password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password">New Password</label>
                    <input id="password" type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-danger">Change Password</button>
            </form>
        </div>
    </div>

    <div class="grid grid-2" style="margin-top: 8px;">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Overview</div>
            </div>

            <div class="grid grid-2">
                <div class="stat-card">
                    <div class="stat-icon blue">🏦</div>
                    <div>
                        <div class="stat-label">Accounts</div>
                        <div class="stat-value">{{ $stats['accounts'] }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">💳</div>
                    <div>
                        <div class="stat-label">Transactions</div>
                        <div class="stat-value">{{ $stats['transactions'] }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon amber">🎯</div>
                    <div>
                        <div class="stat-label">Budgets</div>
                        <div class="stat-value">{{ $stats['budgets'] }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">💰</div>
                    <div>
                        <div class="stat-label">Savings Goals</div>
                        <div class="stat-value">{{ $stats['savings_goals'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">Account Summary</div>
            </div>
            <div style="display:grid; gap:12px;">
                <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f1f5f9;">
                    <span style="color:#64748b">Member Since</span>
                    <strong>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; padding:12px 0; border-bottom:1px solid #f1f5f9;">
                    <span style="color:#64748b">Loans / Debts</span>
                    <strong>{{ $stats['loans'] }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; padding:12px 0;">
                    <span style="color:#64748b">Recurring Plans</span>
                    <strong>{{ $stats['recurring'] }}</strong>
                </div>
            </div>
        </div>
    </div>
@endsection
