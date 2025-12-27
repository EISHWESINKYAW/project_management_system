<?php

namespace App\Events;

use App\Http\Resources\Task\TaskDetailResource;
use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectTaskStatusChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $task;

    protected $projectId;
    /**
     * Create a new event instance.
     */
    public function __construct(Task $task, string $projectId)
    {
        $this->task = $task;
        $this->projectId = $projectId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('project.' . $this->projectId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TaskStatusChange';
    }

    public function broadcastWith()
    {
        return [
            'data' => new TaskDetailResource($this->task)
        ];
    }
}
