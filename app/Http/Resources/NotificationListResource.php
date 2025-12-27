<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'read_at' => $this->read_at,
            'read' => $this->read,
            'notifiable_id' => $this->notifiable_id,
            'redirect_url' => $this->redirect_url,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'user' => $this->user ? $this->user->name : null,
            'profile' => $this->user->profile ? $this->user->profile->url : null,
        ];
    }
}
