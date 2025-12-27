<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use App\Http\Resources\Task\TaskDetailResource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskCreated implements ShouldBroadcast
{
    use SerializesModels;

    public $task;
    protected $channels = [];

    public function __construct(Task $task)
    {
        $this->task = $task;

        // Add collaborators' channels
        foreach ($task->collaborators as $user) {
            $this->channels[] = new PrivateChannel("project.{$task->project_id}.user.{$user->id}.tasks");
        }
    }

    public function broadcastOn()
    {
        return $this->channels;
    }

    public function broadcastAs()
    {
        return 'task.created';
    }

    public function broadcastWith()
    {
        return [
            'data' => new TaskDetailResource($this->task)
        ];
    }
}


