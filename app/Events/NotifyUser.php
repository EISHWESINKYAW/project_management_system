<?php

namespace App\Events;

use App\Http\Resources\NotificationDetailResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyUser implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    protected $user;

    protected $noti;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Notification $noti)
    {
        $this->user = $user;
        $this->noti = $noti;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('notification.' . $this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'NewNoti';
    }

    public function broadcastWith()
    {
        return [
            'data' => new NotificationDetailResource($this->noti)
        ];
    }
}
