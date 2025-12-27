<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationRepository
{
    public function model()
    {
        return Notification::query();
    }

    public function list(Request $request)
    {
        $request->validate([
            'per_page' => 'integer|min:1|max:100',
            'search' => 'nullable|string|max:255',
        ]);

        $authUser = $request->user();

        $perPage = $request->input('per_page', 15);
        $search = $request->input('search');

        return $this->model()
            ->with(['user'])
            ->where('user_id', $authUser->id)
            ->when($search, function ($query) use ($search) {
                return $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function markAsRead($id)
    {
        $notification = $this->model()->find($id);

        abort_unless($notification, 404, 'Notification not found');

        $authUser = Auth::user();

        // သူနဲ့မဆိုင်တဲ့ notification ကို သွားပြီး update လုပ်မိမှာဆိုးလို့
        abort_unless($notification->user_id === $authUser->id, 403, 'Unauthorized action');

        $notification->update(['read' => true, 'read_at' => now()]);

        return $notification;
    }

    public function deleteNoti($id)
    {
        $noti = Notification::find($id);

        abort_unless($noti, 404, 'Notification not found to proceed!!');

        return $noti->delete();
    }
}
