@extends('layouts.app')

@section('title', 'Loans & Debts')
@section('page-title', 'Loans & Debts')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Loans & Debts</div>
                <div class="card-subtitle">Track personal loans and money you owe or are owed.</div>
            </div>
            <a href="{{ route('loans.create') }}" class="btn btn-primary">+ Add Record</a>
        </div>

        @if($loans->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Total</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Due</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                            @php
                                $remaining = max((float) $loan->total_amount - (float) $loan->paid_amount, 0);
                            @endphp
                            <tr>
                                <td>
                                    <div style="font-weight:700;color:#0f172a">{{ $loan->name }}</div>
                                    <div style="font-size:12px;color:#64748b">{{ $loan->notes ?: 'No notes' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $loan->type === 'debt' ? 'badge-red' : 'badge-blue' }}">{{ ucfirst($loan->type) }}</span>
                                </td>
                                <td>₹{{ number_format($loan->total_amount, 2) }}</td>
                                <td>₹{{ number_format($loan->paid_amount, 2) }}</td>
                                <td>₹{{ number_format($remaining, 2) }}</td>
                                <td>{{ $loan->due_date ? $loan->due_date->format('M d, Y') : '—' }}</td>
                                <td>
                                    <span class="badge {{ $loan->status === 'active' ? 'badge-yellow' : 'badge-green' }}">{{ ucfirst($loan->status) }}</span>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('loans.edit', $loan) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('loans.destroy', $loan) }}" onsubmit="return confirm('Delete this loan/debt?')">
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
                <div class="icon">🏷️</div>
                <p>No loans or debts yet. Add one to keep outstanding balances visible.</p>
                <a href="{{ route('loans.create') }}" class="btn btn-primary">Add Record</a>
            </div>
        @endif
    </div>
@endsection
