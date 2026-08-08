@extends('layouts.app')

@section('title', 'Settings')
@section('page-title', 'Settings')

@section('content')
    <div class="grid grid-2">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Preferences</div>
            </div>

            <form method="POST" action="{{ route('settings.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="currency">Default Currency</label>
                    <select id="currency" name="currency" class="form-control">
                        @foreach(['USD','EUR','GBP','INR','JPY','CAD','AUD'] as $currency)
                            <option value="{{ $currency }}" {{ (old('currency', $user->settings['currency'] ?? 'USD')) === $currency ? 'selected' : '' }}>{{ $currency }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="locale">Language / Locale</label>
                    <input id="locale" name="locale" value="{{ old('locale', $user->settings['locale'] ?? 'en') }}" class="form-control" placeholder="en, fr, de, hi">
                </div>

                <button type="submit" class="btn btn-primary">Save Preferences</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="card-title">App Experience</div>
            </div>

            <div style="display:grid; gap:16px;">
                <div style="padding:16px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
                    <div style="font-weight:700; margin-bottom:6px;">Smart reminders</div>
                    <div style="color:#64748b; font-size:14px;">Stay ahead of upcoming bills, deposits, and savings targets.</div>
                </div>
                <div style="padding:16px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
                    <div style="font-weight:700; margin-bottom:6px;">Live dashboard insights</div>
                    <div style="color:#64748b; font-size:14px;">Track balances, spending trends, and budgets from a single view.</div>
                </div>
                <div style="padding:16px; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc;">
                    <div style="font-weight:700; margin-bottom:6px;">Secure account management</div>
                    <div style="color:#64748b; font-size:14px;">Manage personal details, passwords, and notification preferences safely.</div>
                </div>
            </div>
        </div>
    </div>
@endsection
