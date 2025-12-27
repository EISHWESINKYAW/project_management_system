<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
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
        'status',
        'description',
        'due_date',
        'project_id',
        'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
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

    public function scopeIsCollaborator($query, $userId)
    {
        return $query->whereHas('collaborators', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    public function collaborators()
    {
        return $this->belongsToMany(User::class, 'task_collaborator', 'task_id', 'user_id')->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function isCreator($userId)
    {
        return $this->created_by === $userId;
    }
}
