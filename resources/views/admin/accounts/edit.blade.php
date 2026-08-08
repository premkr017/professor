@extends('layouts.app')

@section('title', 'Edit Account')
@section('page-title', 'Edit Account / Wallet')

@section('content')
    <div class="card" style="max-width:700px">
        <div class="card-header">
            <div class="card-title">Edit Account</div>
        </div>

        <form method="POST" action="{{ route('accounts.update', $account) }}">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Account Name *</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $account->name) }}" required>
            </div>

            <div class="form-group">
                <label for="type">Account Type *</label>
                <select name="type" id="type" class="form-control" required>
                    <option value="">-- Select Account Type --</option>
                    @foreach(\App\Http\Controllers\AccountController::ACCOUNT_TYPES as $value => $label)
                        <option value="{{ $value }}" {{ old('type', $account->type) === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="bank_name">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" class="form-control" value="{{ old('bank_name', $account->bank_name) }}" placeholder="e.g. SBI, HDFC">
                </div>
                <div class="form-group">
                    <label for="account_number">Account Number <span style="color:#94a3b8;font-weight:400">(optional, masked)</span></label>
                    <input type="text" name="account_number" id="account_number" class="form-control" value="{{ old('account_number', $account->account_number) }}" placeholder="XXXX1234">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="opening_balance">Opening Balance</label>
                    <input type="number" step="0.01" name="opening_balance" id="opening_balance" class="form-control" value="{{ old('opening_balance', $account->opening_balance) }}">
                </div>
                <div class="form-group">
                    <label for="balance">Current Balance *</label>
                    <input type="number" step="0.01" name="balance" id="balance" class="form-control" value="{{ old('balance', $account->balance) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="currency">Currency *</label>
                    <input type="text" name="currency" id="currency" class="form-control" value="{{ old('currency', $account->currency) }}" maxlength="3" required>
                </div>
                <div class="form-group">
                    <label for="icon">Icon (emoji)</label>
                    <input type="text" name="icon" id="icon" class="form-control" value="{{ old('icon', $account->icon ?? '🏦') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <input type="color" name="color" id="color" class="form-control" value="{{ old('color', $account->color ?? '#2563eb') }}">
            </div>

            <div class="form-group">
                <label for="notes">Description</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Optional description for this account">{{ old('notes', $account->notes) }}</textarea>
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">Update Account</button>
                <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

