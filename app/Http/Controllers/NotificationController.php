<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get all notifications for current user
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = Auth::user()->notifications()
            ->where('is_read', false)
            ->count();

        if (request()->ajax()) {
            return response()->json([
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        }

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get unread notifications count (for badge)
     */
    public function getUnreadCount()
    {
        $count = Auth::user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get latest notifications (for dropdown)
     */
    public function getLatest()
    {
        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = Auth::user()->notifications()
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi ditandai sebagai sudah dibaca');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca');
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notifikasi dihapus');
    }

    /**
     * Create notification (for system use)
     */
    public static function create($userId, $title, $message, $type = 'info', $link = null, $icon = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'icon' => $icon,
            'link' => $link,
        ]);
    }

    /**
     * Send low stock notifications
     */
    public static function sendLowStockAlert($sparepart)
    {
        $admins = \App\Models\User::whereHas('role', function ($q) {
            $q->whereIn('slug', ['super-admin', 'admin']);
        })->get();

        foreach ($admins as $admin) {
            self::create(
                $admin->id,
                'Stok Menipis!',
                "Sparepart {$sparepart->name} ({$sparepart->code}) stok tersisa {$sparepart->stock} pcs. Minimal stok: {$sparepart->min_stock} pcs.",
                'warning',
                route('spareparts.edit', $sparepart),
                'fa-boxes'
            );
        }
    }
}
