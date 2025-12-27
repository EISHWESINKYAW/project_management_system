<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'content',
        'parent_id',
        'comment_by_id',
        'comment_by',
    ];

    const PROJECT = 'project';
    const TASK = 'task';

    const COMMENTABLE_TYPES = [
        self::PROJECT,
        self::TASK,
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'comment_by');
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }

    public function scopeOnlyParent($query)
    {
        return $query->whereNull('parent_id');
    }
}
