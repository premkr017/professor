@extends('layouts.app')

@section('title', 'Savings Goals')
@section('page-title', 'Savings Goals')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Your Savings Goals</div>
                <div class="card-subtitle">Build milestones for big purchases, emergencies, and future plans.</div>
            </div>
            <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">+ Add Goal</a>
        </div>

        @if($goals->count())
            <div class="grid grid-3">
                @foreach($goals as $goal)
                    @php
                        $target = max((float) $goal->target_amount, 1);
                        $saved = (float) $goal->saved_amount;
                        $percentage = min(100, round(($saved / $target) * 100));
                        $remaining = max($target - $saved, 0);
                    @endphp
                    <div class="stat-card">
                        <div class="stat-icon green">🎯</div>
                        <div style="width:100%">
                            <div style="font-weight:700;color:#0f172a">{{ $goal->name }}</div>
                            <div style="font-size:12px;color:#64748b;margin:6px 0 10px">Target ₹{{ number_format($target, 2) }} • Saved ₹{{ number_format($saved, 2) }}</div>
                            <div style="height:8px;background:#e2e8f0;border-radius:999px;overflow:hidden;margin-bottom:8px">
                                <div style="height:100%;width:{{ $percentage }}%;background:linear-gradient(90deg,#10b981,#34d399)"></div>
                            </div>
                            <div style="font-size:13px;color:#475569">{{ $percentage }}% complete • Remaining ₹{{ number_format($remaining, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="icon">💰</div>
                <p>No savings goals yet. Create a goal to track progress toward your next big milestone.</p>
                <a href="{{ route('savings-goals.create') }}" class="btn btn-primary">Create Goal</a>
            </div>
        @endif
    </div>
@endsection
