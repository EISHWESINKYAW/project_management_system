<?php

namespace App\Http\Resources\Comment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'profile' => $this->user->profile ? get_file_url($this->user->profile->url) : null,
            'name' => $this->user->name ?? 'Unknown',
            'user_id' => $this->user->id,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d h:i:s'),
            'replies' => $this->replies->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'parent_id' => $reply->parent_id,
                    'profile' => $reply->user->profile ? get_file_url($reply->user->profile->url) : null,
                    'name' => $reply->user->name ?? 'Unknown',
                    'content' => $reply->content,
                    'created_at' => $reply->created_at->format('Y-m-d h:i'),
                ];
            }),
        ];

        return $data;
    }
}
