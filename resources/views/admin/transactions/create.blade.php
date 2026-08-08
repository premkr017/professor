@extends('layouts.app')

@section('title', 'Add Transaction')
@section('page-title', 'Add Transaction')

<?php $currentType = old('type', 'expense'); ?>

@section('content')
    <div class="card" style="max-width:760px">
        <div class="card-header">
            <div class="card-title">Record New Transaction</div>
        </div>

        <form method="POST" action="{{ route('transactions.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Transaction Type --}}
            <div class="form-group">
                <label>Transaction Type *</label>
                <div class="type-selector" id="typeSelector">
                    <button type="button" class="type-option {{ $currentType === 'income' ? 'active' : '' }}" data-type="income" onclick="setType('income')">○ Income</button>
                    <button type="button" class="type-option {{ $currentType === 'expense' ? 'active' : '' }}" data-type="expense" onclick="setType('expense')">○ Expense</button>
                    <button type="button" class="type-option {{ $currentType === 'transfer' ? 'active' : '' }}" data-type="transfer" onclick="setType('transfer')">○ Transfer</button>
                </div>
                <input type="hidden" name="type" id="typeInput" value="{{ $currentType }}">
                @error('type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="form-group">
                <label for="amount">Amount *</label>
                <div class="amount-input">
                    <span class="amount-prefix">₹</span>
                    <input type="number" step="0.01" min="0.01" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" placeholder="5000" required>
                </div>
                @error('amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Account --}}
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

            {{-- To Account (transfer only) --}}
            <div class="form-group" id="toAccountGroup" style="{{ $currentType === 'transfer' ? '' : 'display:none' }}">
                <label for="to_account_id">To Account *</label>
                <select name="to_account_id" id="to_account_id" class="form-control">
                    <option value="">-- Select Destination Account --</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('to_account_id') == $account->id ? 'selected' : '' }}>
                            {{ $account->name }} ({{ $account->currency }} {{ number_format($account->balance, 2) }})
                        </option>
                    @endforeach
                </select>
                @error('to_account_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category (income/expense only) --}}
            <div class="form-group" id="categoryGroup" style="{{ $currentType === 'transfer' ? 'display:none' : '' }}">
                <label for="category_id">Category</label>
                <select name="category_id" id="category_id" class="form-control">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-type="{{ $category->type }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->icon ? $category->icon . ' ' : '' }}{{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Date + Payment Method --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="transaction_date">Date *</label>
                    <input type="date" name="transaction_date" id="transaction_date" class="form-control" value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                    @error('transaction_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" class="form-control">
                        <option value="">-- Select Method --</option>
                        @foreach($paymentMethods as $value => $label)
                            <option value="{{ $value }}" {{ old('payment_method', 'cash') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Payee --}}
            <div class="form-group">
                <label for="payee">Payee / Received From</label>
                <input type="text" name="payee" id="payee" class="form-control" value="{{ old('payee') }}" placeholder="e.g. Big Bazaar, Salary, Ravi">
                @error('payee')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Monthly grocery">{{ old('description') }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Attachment --}}
            <div class="form-group">
                <label for="attachment">Attachment (Receipt)</label>
                <input type="file" name="attachment" id="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.webp">
                <div style="font-size:12px;color:#94a3b8;margin-top:6px">JPG, PNG, PDF or WEBP up to 5MB.</div>
                @error('attachment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="display:flex;gap:12px;margin-top:8px">
                <button type="submit" class="btn btn-primary">Save Transaction</button>
                <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection

@push('styles')
<style>
    .type-selector {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .type-option {
        flex: 1;
        min-width: 120px;
        padding: 14px;
        border: 2px solid #e2e8f0;
        background: #fff;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }
    .type-option:hover {
        border-color: #2563eb;
        color: #2563eb;
    }
    .type-option[data-type="income"].active {
        border-color: #16a34a;
        background: #f0fdf4;
        color: #16a34a;
    }
    .type-option[data-type="expense"].active {
        border-color: #dc2626;
        background: #fef2f2;
        color: #dc2626;
    }
    .type-option[data-type="transfer"].active {
        border-color: #7c3aed;
        background: #f5f3ff;
        color: #7c3aed;
    }
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

@push('scripts')
<script>
    var allCategories = @json($categories->map(function ($c) {
        return ['id' => $c->id, 'name' => ($c->icon ? $c->icon . ' ' : '') . $c->name, 'type' => $c->type];
    }));

    function setType(type) {
        document.querySelectorAll('.type-option').forEach(function (btn) {
            btn.classList.toggle('active', btn.dataset.type === type);
        });
        document.getElementById('typeInput').value = type;

        var toGroup = document.getElementById('toAccountGroup');
        var catGroup = document.getElementById('categoryGroup');

        if (type === 'transfer') {
            toGroup.style.display = 'block';
            catGroup.style.display = 'none';
            document.getElementById('category_id').value = '';
        } else {
            toGroup.style.display = 'none';
            document.getElementById('to_account_id').value = '';
            catGroup.style.display = 'block';
            filterCategories(type);
        }
    }

    function filterCategories(type) {
        var select = document.getElementById('category_id');
        select.innerHTML = '<option value="">-- Select Category --</option>';
        allCategories.forEach(function (cat) {
            if (cat.type === type) {
                var opt = document.createElement('option');
                opt.value = cat.id;
                opt.textContent = cat.name;
                select.appendChild(opt);
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var current = document.getElementById('typeInput').value;
        setType(current);
    });
</script>
@endpush
