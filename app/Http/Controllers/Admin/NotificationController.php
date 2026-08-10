<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $notifications = $user->notifications()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        /** @var User $user */
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'unread_count' => $user->unreadNotifications->count()]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }
}
