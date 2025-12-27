<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationListResource;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $repo;

    public function __construct(NotificationRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(Request $request)
    {
        $authUser = Auth::user();

        $user = User::find($authUser->id);

        abort_unless($user, 404, 'User not found');

        $unreadCount = $user->unreadNotifications()->count();

        $notifications = $this->repo->list($request);

        return NotificationListResource::collection($notifications)->additional([
            'unread_count' => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $this->repo->markAsRead($id);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function delete($id)
    {
        return $this->repo->deleteNoti($id);
    }
}
