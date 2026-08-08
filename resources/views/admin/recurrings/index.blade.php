@extends('layouts.app')

@section('title', 'Recurring Transactions')
@section('page-title', 'Recurring Transactions')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Automated Transactions</div>
                <div class="card-subtitle">Schedule income and expenses so your finances stay consistent without manual entry.</div>
            </div>
            <a href="{{ route('recurrings.create') }}" class="btn btn-primary">+ Add Recurring</a>
        </div>

        @if($recurrings->count())
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Frequency</th>
                            <th>Next Date</th>
                            <th>Status</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recurrings as $recurring)
                            <tr>
                                <td>
                                    <div style="font-weight:700;color:#0f172a">{{ $recurring->description ?: 'Recurring transaction' }}</div>
                                    <div style="font-size:12px;color:#64748b">{{ $recurring->account?->name }} • {{ $recurring->category?->name ?? 'Uncategorized' }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $recurring->type === 'income' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($recurring->type) }}</span>
                                </td>
                                <td>₹{{ number_format($recurring->amount, 2) }}</td>
                                <td>{{ ucfirst($recurring->frequency) }}</td>
                                <td>{{ $recurring->next_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge {{ $recurring->is_active ? 'badge-green' : 'badge-gray' }}">{{ $recurring->is_active ? 'Active' : 'Paused' }}</span>
                                </td>
                                <td>
                                    <div class="td-actions">
                                        <a href="{{ route('recurrings.edit', $recurring) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form method="POST" action="{{ route('recurrings.destroy', $recurring) }}" onsubmit="return confirm('Delete this recurring transaction?')">
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
                <div class="icon">🔁</div>
                <p>No recurring transactions yet. Add one to automate your monthly bills and income.</p>
                <a href="{{ route('recurrings.create') }}" class="btn btn-primary">Add Recurring</a>
            </div>
        @endif
    </div>
@endsection
