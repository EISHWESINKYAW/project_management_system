<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'notifiable_type',
        'notifiable_id',
        'user_id',
        'title',
        'description',
        'read',
        'read_at',
        'redirect_url'
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unread()
    {
        return $this->where('read', false);
    }

    public static function recordNotification($notifiable, $title, $description, $userId, $redirectUrl = null)
    {
        return self::create([
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'user_id' => $userId,
            'title' => $title,
            'description' => $description,
            'redirect_url' => $redirectUrl
        ]);
    }
};
