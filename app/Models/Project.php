<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_IN_PROGRESS,
        self::STATUS_COMPLETED,
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function scopeIsCreatorOrCollaborator($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhereHas('collaborators', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
        });
    }

    public function isCreator($userId)
    {
        return $this->created_by === $userId;
    }

    public function scopeIsCollaborator($query, $userId)
    {
        return $query->whereHas('collaborators', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'project_collaborator', 'project_id', 'user_id')->withTimestamps();
    }

    public function totalTasks()
    {
        return $this->tasks()->count();
    }

    public function completeTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    public function pendingTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_PENDING)->count();
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
