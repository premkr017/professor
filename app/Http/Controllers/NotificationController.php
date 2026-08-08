<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::where('user_id', auth()->id())->latest()->get();

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(AppNotification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->update(['is_read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(AppNotification $notification)
    {
        abort_if($notification->user_id !== auth()->id(), 403);

        $notification->delete();

        return back()->with('success', 'Notification deleted.');
    }
}
