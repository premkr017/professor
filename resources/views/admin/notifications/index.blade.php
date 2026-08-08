@extends('layouts.app')

@section('title', 'Notifications')
@section('page-title', 'Notifications')

@section('content')
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Notifications</div>
                <div class="card-subtitle">Stay on top of reminders, alerts, and goal updates.</div>
            </div>
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Mark All Read</button>
            </form>
        </div>

        @if($notifications->count())
            <div style="display:grid;gap:12px">
                @foreach($notifications as $notification)
                    <div style="border:1px solid #e2e8f0;border-radius:12px;padding:14px;display:flex;justify-content:space-between;gap:12px;align-items:flex-start;background:{{ $notification->is_read ? '#f8fafc' : '#fff7ed' }}">
                        <div>
                            <div style="font-weight:700;color:#0f172a">{{ $notification->title }}</div>
                            <div style="margin-top:6px;color:#475569">{{ $notification->message }}</div>
                        </div>
                        <div class="td-actions">
                            @if(!$notification->is_read)
                                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">Read</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('notifications.destroy', $notification) }}" onsubmit="return confirm('Delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="icon">🔔</div>
                <p>No notifications yet. You’ll see payment reminders and budget alerts here.</p>
            </div>
        @endif
    </div>
@endsection
