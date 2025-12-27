<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'profile_url',
        'password',
        'role_id',
        'phone',
        'gender',
        'education',
        'address',
        'nrc_no',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('create', 'detail', 'update', 'delete', 'edit');
    }

    public function projectCreator()
    {
        return $this->hasMany(Project::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false);
    }

    public function taskCreator()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_collaborator');
    }

    public function isCollaboratorOfProject($projectId)
    {
        return $this->projects()->where('project_id', $projectId)->exists();
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_collaborator');
    }

    public function completeTasks()
    {
        return $this->tasks()->where('status', Task::STATUS_COMPLETED)->count();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'comment_by');
    }

    public function profile(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function isProjectCreator()
    {
        return !!$this->projectCreator;
    }

    public function isTaskCreator()
    {
        return !!$this->taskCreator;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
